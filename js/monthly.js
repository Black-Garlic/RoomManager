/*
Monthly 2.2.2 by Kevin Thornbloom is licensed under a Creative Commons Attribution-ShareAlike 4.0 International License.
*/

(function ($) {
	"use strict";
	$.fn.extend({
		monthly: function(customOptions) {

			// These are overridden by options declared in footer
			var defaults = {
				dataType: "xml",
				disablePast: false,
				eventList: true,
				events: "",
				jsonUrl: "",
				linkCalendarToEventUrl: false,
				maxWidth: false,
				mode: "event",
				setWidth: false,
				showTrigger: "",
				startHidden: false,
				stylePast: false,
				target: "",
				useIsoDateFormat: false,
				weekStart: 0,	// Sunday
				xmlUrl: ""
			};

			var	options = $.extend(defaults, customOptions),
				uniqueId = $(this).attr("id"),
				parent = "#" + uniqueId,
				currentDate = new Date(),
				currentMonth = currentDate.getMonth() + 1,
				currentYear = currentDate.getFullYear(),
				currentDay = currentDate.getDate(),
				locale = (options.locale || defaultLocale()).toLowerCase(),
				monthNameFormat = options.monthNameFormat || "short",
				weekdayNameFormat = options.weekdayNameFormat || "short",
				monthNames = options.monthNames || defaultMonthNames(),
				dayNames = options.dayNames || defaultDayNames(),
				markupBlankDay = '<div class="m-d monthly-day-blank"><div class="monthly-day-number"></div></div>',
				weekStartsOnMonday = options.weekStart === "Mon" || options.weekStart === 1 || options.weekStart === "1",
				primaryLanguageCode = locale.substring(0, 2).toLowerCase();

		if (options.maxWidth !== false) {
			$(parent).css("maxWidth", options.maxWidth);
		}
		if (options.setWidth !== false) {
			$(parent).css("width", options.setWidth);
		}

		if (options.startHidden) {
			$(parent).addClass("monthly-pop").css({
				display: "none",
				position: "absolute"
			});
			$(document).on("focus", String(options.showTrigger), function (event) {
				$(parent).show();
				event.preventDefault();
			});
			$(document).on("click", String(options.showTrigger) + ", .monthly-pop", function (event) {
				event.stopPropagation();
				event.preventDefault();
			});
			$(document).on("click", function () {
				$(parent).hide();
			});
		}

		// Add Day Of Week Titles
		_appendDayNames(weekStartsOnMonday);

		// Add CSS classes for the primary language and the locale. This allows for CSS-driven
		// overrides of the language-specific header buttons. Lowercased because locale codes
		// are case-insensitive but CSS is not.
		$(parent).addClass("monthly-locale-" + primaryLanguageCode + " monthly-locale-" + locale);

		// Add Header & event list markup
		$(parent).prepend('<div class="monthly-header"><div class="monthly-header-title"><a href="#" class="monthly-header-title-date" onclick="return false"></a></div><a href="#" class="monthly-prev"></a><a href="#" class="monthly-next"></a></div>').append('<div class="monthly-event-list"></div>');

		// Set the calendar the first time
		setMonthly(currentMonth, currentYear);

		// How many days are in this month?
		function daysInMonth(month, year) {
			return month === 2 ? (year & 3) || (!(year % 25) && year & 15) ? 28 : 29 : 30 + (month + (month >> 3) & 1);
		}

		// Build the month
		function setMonthly(month, year) {
			$(parent).data("setMonth", month).data("setYear", year);

			// Get number of days
			var index = 0,
				dayQty = daysInMonth(month, year),
				// Get day of the week the first day is
				mZeroed = month - 1,
				firstDay = new Date(year, mZeroed, 1, 0, 0, 0, 0).getDay(),
				settingCurrentMonth = month === currentMonth && year === currentYear;

			// Remove old days
			$(parent + " .monthly-day, " + parent + " .monthly-day-blank").remove();
			$(parent + " .monthly-event-list, " + parent + " .monthly-day-wrap").empty();
			// Print out the days
			for(var dayNumber = 1; dayNumber <= dayQty; dayNumber++) {
				// Check if it's a day in the past
				var isInPast = options.stylePast && (
					year < currentYear
					|| (year === currentYear && (
						month < currentMonth
						|| (month === currentMonth && dayNumber < currentDay)
					))),
					//파란색 조그만 달력 삭제
					innerMarkup = '<div class="monthly-day-number">' + dayNumber + '</div><div class="new-reservation" data-number="' + dayNumber + '"><i class="" aria-hidden="true"></i></div><div class="monthly-indicator-wrap w3-hide-small"></div>';
				if(options.mode === "event") {
					var thisDate = new Date(year, mZeroed, dayNumber, 0, 0, 0, 0);
					$(parent + " .monthly-day-wrap").append("<div"
						+ attr("class", "m-d monthly-day monthly-day-event"
							+ (isInPast ? " monthly-past-day" : "")
							+ " dt" + thisDate.toISOString().slice(0, 10)
							)
						+ attr("data-number", dayNumber)
						+ ">" + innerMarkup + "</div>");
					$(parent + " .monthly-event-list").append("<div"
						+ attr("class", "monthly-list-item")
						+ attr("id", uniqueId + "day" + dayNumber)
						+ attr("data-number", dayNumber)
						//날짜 클릭시 요일 날짜 출력
						+ '>'
						+"<table id=\"event-table\" data-number=\"" + dayNumber + "\" width=100% class=\"w3-table-all\">\
								<tr>\
									<th style=\"background-color: #DDD; text-align: center; width: 90px;\">" + dayNumber + "일<br>" + dayNames[thisDate.getDay()] + "요일</th>\
									<th style=\"background-color: #DDD; text-align: center; min-width: 35%\">목적</th>\
									<th style=\"background-color: #DDD; text-align: center; width: 35%\" class=\"w3-hide-small\">메모</th>\
									<th style=\"background-color: #DDD; text-align: center; width: 130px;\" class=\"w3-hide-small\">사용자</th>\
								</tr>"
								//<th style=\"border: 1px solid\">사용자</th>\
						+setTimeTable(dayNumber)
						+"</table>"
						+"</div>");
				} else {
					$(parent + " .monthly-day-wrap").append("<a"
						+ attr("href", "#")
						+ attr("class", "m-d monthly-day monthly-day-pick" + (isInPast ? " monthly-past-day" : ""))
						+ attr("data-number", dayNumber)
						+ ">" + innerMarkup + "</a>");
				}
			}

			if (settingCurrentMonth) {
				$(parent + ' *[data-number="' + currentDay + '"]').addClass("monthly-today");
			}

			// Reset button
			$(parent + " .monthly-header-title").html('<a href="#" class="monthly-header-title-date" onclick="return false">' + monthNames[month - 1] + " " + year + "</a>" + (settingCurrentMonth && $(parent + " .monthly-event-list").hide() ? "" : '<a href="#" class="monthly-reset"></a>'));

			// Account for empty days at start
			if(weekStartsOnMonday) {
				if (firstDay === 0) {
					_prependBlankDays(6);
				} else if (firstDay !== 1) {
					_prependBlankDays(firstDay - 1);
				}
			} else if(firstDay !== 7) {
				_prependBlankDays(firstDay);
			}

			// Account for empty days at end
			var numdays = $(parent + " .monthly-day").length,
				numempty = $(parent + " .monthly-day-blank").length,
				totaldays = numdays + numempty,
				roundup = Math.ceil(totaldays / 7) * 7,
				daysdiff = roundup - totaldays;
			if(totaldays % 7 !== 0) {
				for(index = 0; index < daysdiff; index++) {
					$(parent + " .monthly-day-wrap").append(markupBlankDay);
				}
			}

			// Events
			if (options.mode === "event") {
				addEvents(month, year);
			}
			var divs = $(parent + " .m-d");
			for(index = 0; index < divs.length; index += 7) {
				divs.slice(index, index + 7).wrapAll('<div class="monthly-week"></div>');
			}

		}

		function setTimeTable(dayNumber) {
			var timeTable = "";
			var startTime = 8;
			var setAmPm = "AM";
			for (var i = 0; i < (24 - 8); i++) { // (종료시간 - 시작시간) * 시간당 슬롯
				timeTable += "<tr>\
					<td class=\"times\" id=\""+startTime+":00 "+setAmPm+dayNumber+"\" style=\"border:1px solid #DDD\">" + startTime + ":00 " + setAmPm + "</td>\
					<td class=\"table-purpose\" id=\""+startTime+":00 "+setAmPm+dayNumber+"\" name="+dayNumber+" style=\"border:1px solid #DDD\"></td>\
					<td class=\"table-memo w3-hide-small\" id=\""+startTime+":00 "+setAmPm+dayNumber+"\" style=\"border:1px solid #DDD\"></td>\
					<td class=\"table-user w3-hide-small\" id=\""+startTime+":00 "+setAmPm+dayNumber+"\" style=\"border:1px solid #DDD\"></td>\
				</tr>";
											//<td class=\"table-user-name\" value=\""+startTime+":00 "+setAmPm+"\" style=\"border: 1px solid\"></td>\
				timeTable += "<tr>\
					<td class=\"times\" id=\""+startTime+":30 "+setAmPm+dayNumber+"\" style=\"border:1px solid #DDD\">" + startTime + ":30 " + setAmPm + "</td>\
					<td class=\"table-purpose\" id=\""+startTime+":30 "+setAmPm+dayNumber+"\" name="+dayNumber+" style=\"border:1px solid #DDD\"></td>\
					<td class=\"table-memo w3-hide-small\" id=\""+startTime+":30 "+setAmPm+dayNumber+"\" style=\"border:1px solid #DDD\"></td>\
					<td class=\"table-user w3-hide-small\" id=\""+startTime+":30 "+setAmPm+dayNumber+"\" style=\"border:1px solid #DDD\"></td>\
				</tr>";
											//<td class=\"table-user-name\" value=\""+startTime+":30 "+setAmPm+"\" style=\"border: 1px solid\"></td>\
				startTime++;
				if (startTime == 12) {
					setAmPm = "PM";
				}
				if (startTime == 13) {
					startTime = 1;
				}
			}
			return timeTable;
		}

		function addEvent(event, setMonth, setYear, todayDate, todayTime) {
			// Year [0]   Month [1]   Day [2]

			var fullStartDate = _getEventDetail(event, "startdate"),
				fullEndDate = _getEventDetail(event, "enddate"),
				startArr = fullStartDate.split("-"),
				startYear = parseInt(startArr[0], 10),
				startMonth = parseInt(startArr[1], 10),
				startDay = parseInt(startArr[2], 10),
				startDayNumber = startDay,
				endDayNumber = startDay,
				showEventTitleOnDay = startDay,
				startsThisMonth = startMonth === setMonth && startYear === setYear,
				happensThisMonth = startsThisMonth;

			if(fullEndDate) {
				// If event has an end date, determine if the range overlaps this month
				var	endArr = fullEndDate.split("-"),
					endYear = parseInt(endArr[0], 10),
					endMonth = parseInt(endArr[1], 10),
					endDay = parseInt(endArr[2], 10),
					startsInPastMonth = startYear < setYear || (startMonth < setMonth && startYear === setYear),
					endsThisMonth = endMonth === setMonth && endYear === setYear,
					endsInFutureMonth = endYear > setYear || (endMonth > setMonth && endYear === setYear);
				if(startsThisMonth || endsThisMonth || (startsInPastMonth && endsInFutureMonth)) {
					happensThisMonth = true;
					startDayNumber = startsThisMonth ? startDay : 1;
					endDayNumber = endsThisMonth ? endDay : daysInMonth(setMonth, setYear);
					showEventTitleOnDay = startsThisMonth ? startDayNumber : 1;
				}
			}
			if(!happensThisMonth) {
				return;
			}

			var startTime = _getEventDetail(event, "starttime"),
				timeHtml = "",
				eventRoom = _getEventDetail(event, "room"),
				eventMemo = _getEventDetail(event, "memo"), //메모 값을 추가해주기 위해 넘겨진 데이터 받기
				eventName = _getEventDetail(event, "name"),
				eventURL = _getEventDetail(event, "url"),
				eventTitle = _getEventDetail(event, "purpose"),
				eventClass = _getEventDetail(event, "class"),
				eventColor = _getEventDetail(event, "color"),
				eventId = _getEventDetail(event, "id"),
				eventPermission = _getEventDetail(event, "permission"),
				customClass = eventClass ? " " + eventClass : "",
				dayStartTag = "<div",
				dayEndTags = "</span></div>";

			switch(eventPermission) {
				case 0:
					eventColor = "#FAF58C";
					break;
				case 1:
					eventColor = "#47FF9C";
					break;
				case 2:
					return;
				case 3:
					eventColor = "#9DE4FF";
					break;
			}

			if (fullStartDate < todayDate) {
				eventColor = "#AAA";
			} else if (fullStartDate == todayDate && startTime < todayTime) {
				eventColor = "#AAA";
			}

			if(startTime) {
				var endTime = _getEventDetail(event, "endtime");
				timeHtml = '<div><div class="monthly-list-time-start">' + formatTime(startTime) + "</div>"
					+ (endTime ? '<div class="monthly-list-time-end">' + formatTime(endTime) + "</div>" : "")
					+ "</div>";
			}

			if(options.linkCalendarToEventUrl && eventURL) {
				dayStartTag = "<a" + attr("href", eventURL);
				dayEndTags = "</span></a>";
			}

			var	markupDayStart = dayStartTag
					+ attr("data-eventid", eventId)
					+ attr("title", eventTitle)
					// BG and FG colors must match for left box shadow to create seamless link between dates
					+ (eventColor ? attr("style", "background:" + eventColor + ";color:" + eventColor) : ""),
			markupListEvent = "\
				<tr>\
					<td></td>\
					<td>" + eventTitle + "</td>\
					<td>" + formatTime(startTime) + "</td>\
					<td>" + formatTime(endTime) + "</td>\
				</tr>"

			var rowSpan;
			var timeDiff = (getIntFromTime(endTime) - getIntFromTime(startTime));
			var removeId;
			var removeString;
			if (timeDiff % 100 == 0) {
				rowSpan = parseInt(timeDiff / 100) * 2;
			} else {
				rowSpan = parseInt(timeDiff / 100) * 2 + 1;
			}

			for(var index = startDayNumber; index <= endDayNumber; index++) {
				var index = startDayNumber;
				var doShowTitle = index === showEventTitleOnDay;
				// Add to calendar view, 월별 달력에 이벤트 보여주는 부분
				if ($(parent + ' *[data-number="' + index + '"] .monthly-indicator-wrap .monthly-event-indicator').length < 5) {
					$(parent + ' *[data-number="' + index + '"] .monthly-indicator-wrap').append(
							markupDayStart
							+ attr("class", "monthly-event-indicator" + customClass
								// Include a class marking if this event continues from the previous day
								+ (doShowTitle ? "" : " monthly-event-continued")
								)
							+ "><span>" + (doShowTitle ? eventTitle : "") + dayEndTags);
				}
				//날짜 클릭 시 해당 날짜만 나오게
				$('#' + uniqueId+' .monthly-event-list .monthly-list-item').hide();
				// Add to event list, 클릭시 이벤트 보여주는 부분
				$(parent + ' .monthly-list-item[data-number="' + index + '"]')
					.addClass("item-has-event");

				getIntFromTime(formatTime(startTime)+index);

				$('.table-purpose[name="'+index+'"]').each(function(i) {
					if (this.id == formatTime(startTime)+index) {
						$('.table-purpose[id="'+this.id+'"]').html(eventTitle);
						$('.table-purpose[id="'+this.id+'"]').attr('rowspan', rowSpan);
						$('.table-purpose[id="'+this.id+'"]').css('background-color', eventColor);
						$('.table-purpose[id="'+this.id+'"]').css('color', "#111");
						$('.table-memo[id="'+this.id+'"]').html(eventMemo);
						$('.table-memo[id="'+this.id+'"]').attr('rowspan', rowSpan);
						$('.table-memo[id="'+this.id+'"]').attr('class', "w3-hide-small");
						$('.table-memo[id="'+this.id+'"]').css('background-color', eventColor);
						$('.table-memo[id="'+this.id+'"]').css('color', "#111");
						$('.table-user[id="'+this.id+'"]').html(eventName);
						$('.table-user[id="'+this.id+'"]').attr('rowspan', rowSpan);
						$('.table-user[id="'+this.id+'"]').attr('class', "w3-hide-small");
						$('.table-user[id="'+this.id+'"]').css('background-color', eventColor);
						$('.table-user[id="'+this.id+'"]').css('color', "#111");

						removeId = parseInt(getIntFromTime(this.id));

						for (var i = 1; i < rowSpan; i++) {
							if ((removeId / 10) % 10 == 0) {
								removeId += 30;
							} else {
								removeId += 70;
							}

							removeString = removeId + "";

							console.log(removeString);

							if (removeString.length == 3) {
								removeString = removeString.substr(0, 1) + ":" + removeString.substr(1, 2) + " AM" + index;
							} else {
								if (removeString.substr(0, 2) < 12) {
									removeString = removeString.substr(0, 2) + ":" + removeString.substr(2, 2) + " AM" + index;
								} else {
									if (removeString.substr(0, 2) == 12) {
										removeString = removeString.substr(0, 2)+ ":" + removeString.substr(2, 2) + " PM" + index;
									} else {
										removeString = (parseInt(removeString.substr(0, 2)) - 12) + ":" + removeString.substr(2, 2) + " PM" + index;
									}
								}
							}
							$('.table-purpose[id="'+removeString+'"]').remove();
							$('.table-memo[id="'+removeString+'"]').remove();
							$('.table-user[id="'+removeString+'"]').remove();
						}
					}
				});
			}
		}

		function getIntFromTime(time) {
			var tmp;
			if (time.charAt(1) == ':') {
				if (time.charAt(5) == 'P') {
					tmp = parseInt(time.substr(0,1)) + 12;
					time = time.substr(2, 2);
					return tmp + time;
				} else {
					return 0 + time.substr(0, 1) + time.substr(2, 2);
				}
			} else {
				if (time.charAt(6) == 'P') {
					tmp = parseInt(time.substr(0,2)) + 12;
					time = time.substr(3, 2);
					return tmp + time;
				} else {
					return time.substr(0, 2) + time.substr(3, 2);
				}
			}
		}

		function addEvents(month, year) {
			if(options.events) {
				// Prefer local events if provided
				addEventsFromString(options.events, month, year);
			} else {
				var remoteUrl = options.dataType === "xml" ? options.xmlUrl : options.jsonUrl;
				if(remoteUrl) {
					// Replace variables for month and year to load from dynamic sources
					var url = String(remoteUrl).replace("{month}", month).replace("{year}", year);
					$.get(url, {now: $.now()}, function(data) {
						addEventsFromString(data, month, year);
					}, options.dataType).fail(function() {
						console.error("Monthly.js failed to import " + remoteUrl + ". Please check for the correct path and " + options.dataType + " syntax.");
					});
				}
			}
		}

		function addEventsFromString(events, setMonth, setYear) {
			var now = new Date();
			var todayMonth = now.getMonth() + 1;
			if (todayMonth < 10)
				todayMonth = "0" + todayMonth;
			var todayDay = now.getDate();
			if (todayDay < 10)
				todayDay = "0" + todayDay;

			var todayDate = now.getFullYear() + "-" + todayMonth + "-" + todayDay;
			var todayTime = now.toTimeString().substr(0, 8);

			if (options.dataType === "xml") {
				$(events).find("event").each(function(index, event) {
					addEvent(event, setMonth, setYear, todayDate, todayTime);
				});
			} else if (options.dataType === "json") {
				$.each(events.monthly, function(index, event) {
					addEvent(event, setMonth, setYear, todayDate, todayTime);
				});
			}
		}

		function attr(name, value) {
			var parseValue = String(value);
			var newValue = "";
			for(var index = 0; index < parseValue.length; index++) {
				switch(parseValue[index]) {
					case "'": newValue += "&#39;"; break;
					case "\"": newValue += "&quot;"; break;
					case "<": newValue += "&lt;"; break;
					case ">": newValue += "&gt;"; break;
					default: newValue += parseValue[index];
				}
			}
			return " " + name + "=\"" + newValue + "\"";
		}

		function _appendDayNames(startOnMonday) {
			var offset = startOnMonday ? 1 : 0,
				dayName = "",
				dayIndex = 0;
			for(dayIndex = 0; dayIndex < 6; dayIndex++) {
				dayName += "<div>" + dayNames[dayIndex + offset] + "</div>";
			}
			dayName += "<div>" + dayNames[startOnMonday ? 0 : 6] + "</div>";
			$(parent).append('<div class="monthly-day-title-wrap">' + dayName + '</div><div class="monthly-day-wrap"></div>');
		}

		// Detect the user's preferred language
		function defaultLocale() {
			if(navigator.languages && navigator.languages.length) {
				return navigator.languages[0];
			}
			return navigator.language || navigator.browserLanguage;
		}

		// Use the user's locale if possible to obtain a list of short month names, falling back on English
		function defaultMonthNames() {
			if(typeof Intl === "undefined") {
				return ["Jan", "Feb", "Mar", "Apr", "May", "June", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
			}
			var formatter = new Intl.DateTimeFormat(locale, {month: monthNameFormat});
			var names = [];
			for(var monthIndex = 0; monthIndex < 12; monthIndex++) {
				var sampleDate = new Date(2017, monthIndex, 1, 0, 0, 0);
				names[monthIndex] = formatter.format(sampleDate);
			}
			return names;
		}

		function formatDate(year, month, day) {
			if(options.useIsoDateFormat) {
				return new Date(year, month - 1, day, 0, 0, 0).toISOString().substring(0, 10);
			}
			if(typeof Intl === "undefined") {
				return month + "/" + day + "/" + year;
			}
			return new Intl.DateTimeFormat(locale).format(new Date(year, month - 1, day, 0, 0, 0));
		}

		// Use the user's locale if possible to obtain a list of short weekday names, falling back on English
		function defaultDayNames() {
			if(typeof Intl === "undefined") {
				return ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
			}
			var formatter = new Intl.DateTimeFormat(locale, {weekday: weekdayNameFormat}),
				names = [],
				dayIndex = 0,
				sampleDate = null;
			for(dayIndex = 0; dayIndex < 7; dayIndex++) {
				// 2017 starts on a Sunday, so use it to capture the locale's weekday names
				sampleDate = new Date(2017, 0, dayIndex + 1, 0, 0, 0);
				names[dayIndex] = formatter.format(sampleDate);
			}
			return names;
		}

		function _prependBlankDays(count) {
			var wrapperEl = $(parent + " .monthly-day-wrap"),
				index = 0;
			for(index = 0; index < count; index++) {
				wrapperEl.prepend(markupBlankDay);
			}
		}

		function _getEventDetail(event, nodeName) {
			return options.dataType === "xml" ? $(event).find(nodeName).text() : event[nodeName];
		}

		// Returns a 12-hour format hour/minute with period. Opportunity for future localization.
		function formatTime(value) {
			var timeSplit = value.split(":");
			var hour = parseInt(timeSplit[0], 10);
			var period = "AM";
			if(hour > 12) {
				hour -= 12;
				period = "PM";
			} else if (hour == 12) {
				period = "PM";
			} else if(hour === 0) {
				hour = 12;
			}
			return hour + ":" + String(timeSplit[1]) + " " + period;
		}

		function setNextMonth() {
			var	setMonth = $(parent).data("setMonth"),
				setYear = $(parent).data("setYear"),
				newMonth = setMonth === 12 ? 1 : setMonth + 1,
				newYear = setMonth === 12 ? setYear + 1 : setYear;
				if(options.condition>0){ //달이 넘어갈 때 배수로 넘어가는 오류 수정
					if(newMonth==1){
						newMonth=12;
						newYear=newYear-1;
					}else{
						newMonth=newMonth-1;
					}
				}
			setMonthly(newMonth, newYear);
			viewToggleButton();
		}
		function setPreviousMonth() {
			var setMonth = $(parent).data("setMonth"),
				setYear = $(parent).data("setYear"),
				newMonth = setMonth === 1 ? 12 : setMonth - 1,
				newYear = setMonth === 1 ? setYear - 1 : setYear;
				if(options.condition>0){ //달이 넘어갈 때 배수로 넘어가는 오류 수정
					if(newMonth==12){
						newMonth=1;
						newYear=newYear+1;
					}else{
							newMonth=newMonth+1;
					}

				}
			setMonthly(newMonth, newYear);
			viewToggleButton();
		}

		// Function to go back to the month view
		function viewToggleButton() {
			if($(parent + " .monthly-event-list").is(":visible")) {
				$(parent + " .monthly-cal").remove();
				$(parent + " .monthly-header-title").prepend('<a href="#" class="monthly-cal"></a>');
			}
		}

///// start new function
		// function to display modal popup by jerry (new)
		function sampleModalPopup(){
			// �˾� ȣ�� url
			//var targeted_popup_class = $(this).attr('data-popup-open');
			$('[data-popup]').fadeIn(350);
			e.preventDefault();
		}

		// Add new reservation by jerry (new)
		$(document.body).on("click touchstart", parent + " .new-reservation", function (event) {
			var whichDay = $(this).data("number");
			var	setMonth = $(parent).data("setMonth"),
				setYear = $(parent).data("setYear");
			//alert(setYear + '-' + setMonth + '-'+ whichDay);
			sampleModalPopup();
			event.stopPropagation();
		});

		$('[data-popup-close]').on('click', function(e) {
			var targeted_popup_class = jQuery(this).attr('data-popup-close');
			$('[data-popup="' + targeted_popup_class + '"]').fadeOut(350);

			e.preventDefault();
		});
/////	end new function

		// Advance months
		$(document.body).on("click", parent + " .monthly-next", function (event) {
			setNextMonth();
			event.preventDefault();
		});

		// Go back in months
		$(document.body).on("click", parent + " .monthly-prev", function (event) {
			setPreviousMonth();
			event.preventDefault();
		});

		// Reset Month
		$(document.body).on("click", parent + " .monthly-reset", function (event) {
			$('#' + uniqueId+' .monthly-event-list .monthly-list-item').hide();
			$(parent + " .monthly-prev").css("transform", "scale(1)");
			$(parent + " .monthly-next").css("transform", "scale(1)");
			$(this).remove();
			setMonthly(currentMonth, currentYear);
			viewToggleButton();
			event.preventDefault();
			event.stopPropagation();
		});

		// Back to month view
		$(document.body).on("click", parent + " .monthly-cal", function (event) {
			$('#' + uniqueId+' .monthly-event-list .monthly-list-item').hide();
			$(this).remove();
			$(parent + " .monthly-event-list").css("transform", "scale(0)");
			$(parent + " .monthly-prev").css("transform", "scale(1)");
			$(parent + " .monthly-next").css("transform", "scale(1)");
			setTimeout(function() {
				$(parent + " .monthly-event-list").hide();
			}, 250);
			event.preventDefault();
		});



		// 날짜 클릭 할 떄
		$(document.body).on("click touchstart", parent + " .monthly-day", function (event) {
			// If events, show events list
			var whichDay = $(this).data("number");
			if(options.mode === "event" && options.eventList) {
				var	theList = $(parent + " .monthly-event-list"),
					myElement = document.getElementById(uniqueId + "day" + whichDay),
					topPos = myElement.offsetTop;
				theList.show(); //날짜 클릭시 띄워주는 화면을 보여줌
				theList.css("transform");
				theList.css("transform", "scale(1)");
				$(parent + " .monthly-prev").css("transform", "scale(0)");
				$(parent + " .monthly-next").css("transform", "scale(0)");
				$(parent + ' .monthly-list-item[data-number="' + whichDay + '"]').show();
				theList.scrollTop(topPos);
				viewToggleButton(); //날짜 클릭했을때 month 버튼
				if(!options.linkCalendarToEventUrl) {
					event.preventDefault();
				}
			// If picker, pick date
			} else if (options.mode === "picker") {

				var	setMonth = $(parent).data("setMonth"),
					setYear = $(parent).data("setYear");
				// Should days in the past be disabled?
				if($(this).hasClass("monthly-past-day") && options.disablePast) {
					// If so, don't do anything.
					event.preventDefault();
				} else {
					// Otherwise, select the date ...
					$(String(options.target)).val(formatDate(setYear, setMonth, whichDay));
					// ... and then hide the calendar if it started that way
					if(options.startHidden) {
						$(parent).hide();
					}
				}
				event.preventDefault();
			}
		});

		// Clicking an event within the list
		$(document.body).on("click", parent + " .listed-event", function (event) {
			var href = $(this).attr("href");
			// If there isn't a link, don't go anywhere
			if(!href) {
				event.preventDefault();
			}
		});

	}
	});
}(jQuery));
