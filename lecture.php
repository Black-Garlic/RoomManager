<?php
  $menu  = 6;
	include "./inc/top.php";
	include "./inc/gauth.php";
?>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css">
<link rel="stylesheet" href="./css/lecture.css">
<!-- Overlay effect when opening sidebar on small screens -->
<div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>

<!-- !PAGE CONTENT! -->
<div class="w3-main" style="margin-left:300px;margin-top:43px;">

  <!-- Header -->
  <header class="page-head" style="padding-top:22px">
  </header>

  <!--contents-->
  <!-- http://robmonie.github.io/jquery-week-calendar/full_demo/weekcalendar_full_demo.html -->

	<link rel='stylesheet' type='text/css' href='css/week_cal.css?v=0613' />
	<link rel='stylesheet' type='text/css' href='css/demo.css?v=06132' />
  <style type="text/css">
  .buttonContainer {
    margin: 0.5rem 0 0 19rem;
  }
  .small-space {
    display: none;
  }
  .input-resize {
    width: 30%;
    height: 70%;
  }
  th {
    padding-left:10px;
    border-right: 0;
  }
  @media (max-width: 767px){
    .buttonContainer {
      margin: 0.5rem 0 0 1rem;
    }
    .small-space {
      display: block;
    }
    .input-resize {
      width: 70%;
      height: 90%;
    }
    th {
      padding-left:2px;
      border-right: 0;
    }
  }
  select{
    text-align-last:center;
    padding-right: 29px;
    direction: rtl;
  }
  </style>
	<script>
    //Global variables
    var lecture_num=0;
    var k=0;
		var checkSet = new Array();
		var timeTable = new Array();
		var times;
    var first_index;
    var last_index;
    var range;
    var weekday = new Array();
    var reservedData = new Array();

    $(document).ready(function() {
			times = (24 - 8) * 2; //운영 시간을 설정
			var startHour = 8;

			for (var i = 0; i < times; i++) { //timeTable에 시간 값들을 넣음
				if (i % 2 ==0) {
					timeTable[i] = leadingZeros(startHour, 2) + ':00';
				} else {
					timeTable[i] = leadingZeros(startHour, 2) + ':30';
					startHour++;
				}
			}

			setSelectOption();

      $('select[id="startTimeSelect"]').change(function() {
        var selected;
        for (var i = 0; i < times; i++) {
          if (timeTable[i] == $('select[id="startTimeSelect"]').val())
            selected = i;
        }
        setEndOption(selected);
        document.getElementById("check_button").innerHTML = "확인하기";
        document.getElementById("reserve_button").disabled = true;
      })

      $('select[id="endTimeSelect"]').change(function() {
        document.getElementById("check_button").innerHTML = "확인하기";
        document.getElementById("reserve_button").disabled = true;
      })

      $('button[id="check_button"]').click(function(){
        checkReservation();
      });

      $('button[id="reserve_button"]').click(function(){
        /////////////////////////////////////
        var lecture_num = getLectureNum();
        /////////////////////////////////////
        //var lecture_num  = new Date().getTime();
        for (var i = 0; i < weekday.length; i++) {
          saveReservation(weekday[i], lecture_num);
        }
      });

			$('button[id="cancle_button"]').click(function(){ //페이지 새로고침
        location.href = "lecture.php";
      });

			$('#startDate').change(function() { //값이 변경되었으므로 예약 버튼 비활성화
        document.getElementById("check_button").innerHTML = "확인하기";
        document.getElementById("reserve_button").disabled = true;
        if (compareDate()) {
          endDate.value = startDate.value;
        }
			});

      $('#endDate').change(function() {
        document.getElementById("check_button").innerHTML = "확인하기";
        document.getElementById("reserve_button").disabled = true;
        if (compareDate()) {
          startDate.value = endDate.value;
        }
			});

      $('input:checkbox[id=All]').change(function() {
        if (this.checked) {
          $('input:checkbox[name="days"]').each(function() {
            this.checked = true;
          });
        } else {
          $('input:checkbox[name="days"]').each(function() {
            this.checked = false;
          });
        }
      });

      $('button[name="reserved"]').click(function() {
        alert("삭제");
        if (confirm("정말 삭제하시겠습니까?")) {
          alert("삭제");
        } else {
          return;
        }
      })
    });
//////////////////////////////////////////////
    function getLectureNum(){
      $.ajax({
        url: "inc/function.php",
        method: "POST",
        async: false,
        data: {
          mode: 'getLectureNum'
        },
        success: function(data) {
          console.log("lecture_num값");
          lecture_num=data;
          console.log(lecture_num);
        }
      });
      return lecture_num;
    }
/////////////////////////////////////////////
    function saveReservation(date, lecture_num) {
			var roomname = $('#roomList option:selected').val();
			var username = getName(); //gauth에서 값을 가져옴
			var email = getEmail();
			var professor = $('input[name="professor"]').val();

			var starttime = $('#startTimeSelect').val() + ":00";
			var endtime = $('#endTimeSelect').val() + ":00";

			if ($("input[name='purpose']").val().length == 0) {
				alert("Empty Purpose!");
				return;
			} else {
				var purpose = $("input[name='purpose']").val();
			}

			if ($("input[name='phone']").val().length == 0) {
				alert("Empty Phone Number!");
				return;
			} else {
				var phone = $("input[name='phone']").val();
			}

			var memo = $("#memo").val();
			if(starttime>=endtime){
				alert("Start time cannot be greater than end time!");
				return;
			}

			$.ajax({
				url: "./inc/function.php",
				type: "POST",
				data: {
					roomname: roomname,
					username: username,
					email: email,
					professor: professor,
					major: '전산전자공학부',
					date: date,
					starttime: starttime,
					endtime: endtime,
					purpose: purpose,
					phone: phone,
					memo: memo,
					lecture_num: lecture_num,
					mode: 'addLecture'
				},
			  success: function(data) {
				if(data==="same") {
					alert("There is a reservation already in that time!");
				}	else if(data==="error"){
					alert("Error caused while adding!");
				} else if(data==="user_not_found"){
					alert("user not found!");
				} else if(data==="room_not_found"){
					alert("room not found!");
				}	else {
				if(k==0) {
					  alert(starttime.substr(0, 5) + " - " + endtime.substr(0, 5) + "\n예약되었습니다!");
				  k++;
				}
				window.location.replace("lecture.php");
				}
			  }
			});
	  }

		function leadingZeros(n, digits) { //1의 자리 숫자에 0을 붙여줌
  		var zero = '';
  		n = n.toString();

  		if (n.length < digits) {
    		for (var i = 0; i < digits - n.length; i++)
    			zero += '0';
  		}
  		return zero + n;
		}

    function compareDate() { //시작 날짜와 끝나는 날짜를 비교
      var currentStart = startDate.value;
      var currentEnd = endDate.value;

      var startString = "";
      var endString = "";

      for (var i = 0; i < currentStart.length; i++) {
        if (i != 4 && i != 7) {
          startString += currentStart[i];
        }
      }

      for (var i = 0; i < currentEnd.length; i++) {
        if (i != 4 && i != 7) {
          endString += currentEnd[i];
        }
      }

      if (startString > endString) {
        return true;
      } else {
        return false;
      }
    }

    function getDates(startDate, stopDate) {
      var dateArray = new Array();
      var currentDate = startDate;
      while (currentDate <= stopDate) {
          dateArray.push( new Date (currentDate) )
          currentDate = currentDate.addDays(1);
      }
      return dateArray;
    }

    Date.prototype.addDays = function(days) {
      var dat = new Date(this.valueOf())
      dat.setDate(dat.getDate() + days);
      return dat;
    }

    function setDateArray(date, dateArray) { //dateArray에 date를 추가
        var year = date.getFullYear();
        var month = leadingZeros(date.getMonth() + 1, 2);
        var day = leadingZeros(date.getDate(), 2);

        var addDate = year + "-" + month + "-" + day;
        dateArray.push(addDate);
    }

    function setSelectOption() {
      for (var i = 0; i < times; i++) {
        startOption = "<option id='start" + i + "' name='startOption'>" + timeTable[i] + "</option>";
        if (i == 31) {
          endOption = "<option id='end" + i + "' name='endOption'>24:00</option>";
        } else {
          endOption = "<option id='end" + i + "' name='endOption'>" + timeTable[i + 1] + "</option>";
        }
        $('#startTimeSelect').append(startOption);
        $('#endTimeSelect').append(endOption);
      }
    }

    function setStartOption() {
			$('select#startTimeSelect option').remove();
			var defaultSelect = -1;
			for (var i = 0; i < times; i++) {
				if (!startEndSet[i]) {
					if (defaultSelect < 0) {
						option = "<option id='start" + i + "' name='startOption' selected>" + timeTable[i] + "</option>";
						defaultSelect = i;
					} else {
						option = "<option id='start" + i + "' name='startOption'>" + timeTable[i] + "</option>";
					}
				} else {
					option = "<option id='start" + i + "' name='startOption' disabled>" + timeTable[i] + "</option>";
				}
				$('#startTimeSelect').append(option);
			}
			return defaultSelect;
		}

		function setEndOption(selected) {
			$('select#endTimeSelect option').remove();
			for (var i = selected; i < times; i++) {
				if (i == 31) {
					option = "<option id='end" + i + "' name='endOption'>24:00</option>";
				} else {
					option = "<option id='end" + i + "' name='endOption'>" + timeTable[i + 1] + "</option>";
				}
        $('#endTimeSelect').append(option);
			}
		}
    function sendmail(to, content){
    	$.ajax({
    		url: "sendmail.php",
    		method: "POST",
    		data: {
    			to: to,
    			content : content
    		},
    		success: function(data) {
    			console.log(data);
    		}
    	});
    }
    function deleteReservation(id) {
      var roomId = reservedData[id].lectureRoom_id;
      var date = reservedData[id].reservation_date;
      var startTime = reservedData[id].start_time;
      var endTime = reservedData[id].end_time;
      if (confirm("정말 삭제하시겠습니까?")) {
        $.ajax ({
          url: "./inc/function.php",
  				type: "POST",
  				data: {
  					roomId: roomId,
            date: date,
            startTime: startTime,
            endTime: endTime,
  					mode: 'deleteReservation'
          },
          success: function(data) {
            var send_mail = data.split("/");
            sendmail(send_mail[0],send_mail[1]); //phpmailer
            checkReservation();
            alert("예약이 삭제되었습니다");
          }
        })
      } else {
        return;
      }
    }

    function checkReservation() {
      if ($('input:checkbox[name="days"]:checked').length == 0) { //요일이 선택되지 않았을 때
        alert("Please Select Weekday!");
      } else { //최소 1개의 요일이 선택
        var room = roomList.value;
        var start = startDate.value;
        var end = endDate.value;
        var mode = 'checkReservation';
        var valid = false;
        var dates = new Array();
        var checkedTime = new Array();

        var range = getDates(new Date(start), new Date(end)); //시작 날짜와 끝 날짜 사이의 모든 날들을 가져옴

        for (var i = 0; i < range.length; i++) { //체크된 요일에 해당하는 날짜를 dates에 넣음
          console.log(range[i]);
          if (Mon.checked && range[i].getDay() == Mon.value) {
            setDateArray(range[i], dates);
          }
          if (Tue.checked && range[i].getDay() == Tue.value) {
            setDateArray(range[i], dates);
          }
          if (Wed.checked && range[i].getDay() == Wed.value) {
            setDateArray(range[i], dates);
          }
          if (Thu.checked && range[i].getDay() == Thu.value) {
            setDateArray(range[i], dates);
          }
          if (Fri.checked && range[i].getDay() == Fri.value) {
            setDateArray(range[i], dates);
          }
          if (Sat.checked && range[i].getDay() == Sat.value) {
            setDateArray(range[i], dates);
          }
          if (Sun.checked && range[i].getDay() == Sun.value) {
            setDateArray(range[i], dates);
          }
        }

        weekday = dates; //전역 변수에 복사

        checkedTime.push($('#startTimeSelect').val() + ":00"); //시작 시간과 끝 시간을 확인
        checkedTime.push($('#endTimeSelect').val() + ":00");

        console.log(dates);

        dates = JSON.stringify(dates); //json으로 만듦
        checkedTime = JSON.stringify(checkedTime);

        $.ajax({
          url: "./inc/function.php",
          type: "POST",
          async: false,
          data: {
            mode: mode,
            room_name: room,
            start: start,
            end: end,
            dates: dates,
            time: checkedTime
          },
          success: function(data) {
            reservedData = JSON.parse(data);
            var message = "";
            var receiveCount = 0;
            if (reservedData.length > 0) {
              while(receiveCount < reservedData.length) {
                message = message.concat("<p id='p", receiveCount, "' name='errorMessage'>");
                message = message.concat(reservedData[receiveCount].reservation_date, " ");
                message = message.concat(reservedData[receiveCount].start_time.substr(0, 5), "부터 ");
                message = message.concat(reservedData[receiveCount].end_time.substr(0, 5), "까지 ");
                message = message.concat(reservedData[receiveCount].purpose, "에 대한 예약이 있습니다<br>");
                message = message.concat("삭제하시겠습니까? <button id=", receiveCount++, " name=\"reserved\" onclick=\"deleteReservation(id)\">삭제</button><br>");
                message = message.concat("</p>");
              }
              document.getElementById("error_message").innerHTML = message; //에러 메시지를 표시
              valid = false;
            } else {
              document.getElementById("error_message").innerHTML = "";
              valid = true;
            }
          }
        })
        if (valid) {
          document.getElementById("check_button").innerHTML = "확인완료";
          document.getElementById("reserve_button").disabled = false;
        } else {
          document.getElementById("check_button").innerHTML = "확인하기";
          document.getElementById("reserve_button").disabled = true;
        }
        console.log(reservedData);
      }
    }
	</script>
  <div class="w3-margin-left w3-container" style="float: left; font-family: 'Noto Sans KR', sans-serif;">
    <h2><b>수업 등록</b></h2>
      <table style="width: 100%; height:32rem;background: white;">
        <tr>
          <th style="padding-left:10px;border-right: 0;">장소</th>
          <td colspan="17" style="border-left: 0;">
            <select id="roomList" class="input-resize">
              <script language='JavaScript'>
                var mode = 'roomSelect';
                $.ajax({
                  url: "./inc/function.php",
          				type: "POST",
          				async: false,
          				data: {
          					mode: mode
          				},
          				success : function(data) {
          					document.write(data);
          				}
                })
              </script>
            </select>
  				</td>
        </tr>
        <tr>
					<th>관련교수*</th>
					<td colspan="17" style="border-left: 0;">
						<input type="text" class="input-resize" name="professor" placeholder="Ex) 용환기 교수님">
					</td>
				</tr>
        <tr>
          <th>시작일*</th>
          <td colspan="17" style="border-left: 0;"><input class="input-resize" type="date" id="startDate" name="date"
  					value=
  					<?php
           		echo date('Y-m-d');
       			?>><br></td>
        </tr>
        <tr>
          <th>종료일*</th>
          <td colspan="17" style="border-left: 0;"><input class="input-resize" type="date" id="endDate" name="date"
  					value=
  					<?php
           		echo date('Y-m-d');
       			?>><br></td>
        </tr>
        <tr>
          <th>요일*</th>
          <td colspan="17" id="weekday" style="border-left: 0;">
            All
            <input type="checkbox" class="w3-check" id="All">
            Mon
            <input type="checkbox" class="w3-check " name="days" id="Mon" value="1">
            Tue
            <input type="checkbox" class="w3-check" name="days" id="Tue" value="2">
            <br class="small-space" />
            Wed
            <input type="checkbox" class="w3-check" name="days" id="Wed" value="3">
            Thu
            <input type="checkbox" class="w3-check" name="days" id="Thu" value="4">
            Fri
            <input type="checkbox" class="w3-check" name="days" id="Fri" value="5">
            <br class="small-space" />
            Sat
            <input type="checkbox" class="w3-check" name="days" id="Sat" value="6">
            Sun
            <input type="checkbox" class="w3-check" name="days" id="Sun" value="0">
          <br></td>
        </tr>
        <tr>
  				<th>시작시간</td>
  				<td style="border-left: 0;">
  					<select id='startTimeSelect' class="input-resize">
  					</select>
  				</td>
  			</tr>
  			<tr>
  				<th>끝시간</td>
  				<td style="border-left: 0;">
  					<select id='endTimeSelect' class="input-resize">
  					</select>
  				</td>
  			</tr>
        <tr>
          <th>목적*</th>
          <td colspan="17" style="border-left: 0;"><input class="input-resize" type="text" name="purpose"><br></td>
        </tr>
        <tr>
          <th>연락처*</th>
          <td colspan="17" style="border-left: 0;"><input class="input-resize" type="text" name="phone"><br></td>
        </tr>
        <tr>
          <th>추가사항</th>
          <td colspan="17" style="border-left: 0;">
  					<textarea id="memo" style="height: 90%; width: 99%;" name="memo" placeholder="예상 인원, 구체적인 활동 내용 등"></textarea><br>
  				</td>
        </tr>
      </table>
    <div class="buttonContainer">
    <button class="w3-button w3-green" id="check_button">확인하기</button>
    <button class="w3-button w3-blue" id="reserve_button" disabled>예약하기</button>
  	<button class="w3-button w3-red" id="cancle_button">새로고침</button><br>
    </div>
    <p id="error_message"><p>
  </div>
<?php
	include "./inc/footer.php";
?>
