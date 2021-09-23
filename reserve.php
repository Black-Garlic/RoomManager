<?php
	$menu = 0;
	include "./inc/top.php";
	include "./inc/gauth.php";
?>
<!--메뉴 위해 추가 start-->
<script>
  function userInfo(email,name){}
  function hide(){ $("#admin").hide();}
  function show(){ $("#admin").show();}
</script>
<?php
  include "./inc/config.php";
  $email = $_POST['user_email']; //로그인 시 email 받아옴
  global $conn;
  $sql = "SELECT * FROM User WHERE User.mail ='".$email."' AND User.isAdmin = 1";//관리자인지 확인
  $result = mysqli_query($conn, $sql);
  $total_rows = mysqli_num_rows($result);

 	if($total_rows > 0){ //관리자 계정일 경우
		echo "<script>show();</script>";
  } else {
		echo "<script>hide();</script>";
  }

	$minDate;
	if (date('N') == 5) { // 금요일
		if (date('H') >= 17) { // 오후 5시 이후
			if (strtotime($_POST['start_date']) > strtotime(date('Y-m-d', strtotime("+3 days")))) {
				$minDate = $_POST['start_date'];
			} else {
				$minDate = date('Y-m-d', strtotime("+3 days"));
			}
		} else {
			if (strtotime($_POST['start_date']) > strtotime(date('Y-m-d', strtotime("+1 days")))) {
				$minDate = $_POST['start_date'];
			} else {
				$minDate = date('Y-m-d', strtotime("+1 days"));
			}
		}
	} else if (date('N') == 6) { // 토요일
		if (strtotime($_POST['start_date']) > strtotime(date('Y-m-d', strtotime("+2 days")))) {
			$minDate = $_POST['start_date'];
		} else {
			$minDate = date('Y-m-d', strtotime("+2 days"));
		}
	} else if (date('N') == 7) { // 일요일
		if (strtotime($_POST['start_date']) > strtotime(date('Y-m-d', strtotime("+1 days")))) {
			$minDate = $_POST['start_date'];
		} else {
			$minDate = date('Y-m-d', strtotime("+1 days"));
		}
	} else { // 월, 화, 수, 목요일
		if (date('H') >= 17) { // 오후 5시 이후
			if (strtotime($_POST['start_date']) > strtotime(date('Y-m-d', strtotime("+2 days")))) {
				$minDate = $_POST['start_date'];
			} else {
				$minDate = date('Y-m-d', strtotime("+2 days"));
			}
		} else {
			if (strtotime($_POST['start_date']) > strtotime(date('Y-m-d', strtotime("+1 days")))) {
				$minDate = $_POST['start_date'];
			} else {
				$minDate = date('Y-m-d', strtotime("+1 days"));
			}
		}
	}
?>
<!--추가 end-->

<link rel="stylesheet" href="./css/reserve.css">
<!-- Overlay effect when opening sidebar on small screens -->
<div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>

<!-- !PAGE CONTENT! -->
<div class="w3-main" style="margin-left:300px;margin-top:43px;">

  <!-- Header -->
  <header class="page-head" style="padding-top:22px">
  </header>

  <!--contents-->
  <!-- http://robmonie.github.io/jquery-week-calendar/full_demo/weekcalendar_full_demo.html -->
	<link rel="stylesheet" href="css/calendar.css" />
	<script type="text/javascript" src="js/pureJSCalendar.js"></script>

	<link rel='stylesheet' type='text/css' href='css/week_cal.css?v=0613' />
	<link rel='stylesheet' type='text/css' href='http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/start/jquery-ui.css' />
	<link rel='stylesheet' type='text/css' href='css/jquery.weekcalendar.css?v=06132' />
	<!--<link rel='stylesheet' type='text/css' href='css/demo.css?v=06132' />-->
	<link rel='stylesheet' type='text/css' href='css/roomlist.css' />

	<style type='text/css'>
		.buttonContainer {
			margin: 0.5rem 0 0 17rem;
		}
		th{
			 padding-left:20px;
			 border-right: 0;
		}
		.input-resize {
	    width: 70%;
	    height: 80%;
	  }
		@media (max-width: 767px){
	    .buttonContainer {
	      margin: 0.5rem 0 0 8rem;
	    }
			th{
				 padding-left:2px;
				 border-right: 0;
			}
			.input-resize {
		    width: 80%;
		    height: 90%;
		  }
	  }
		select{
	    text-align-last:center;
	    padding-right: 29px;
	    direction: rtl;
	  }
	</style>

	<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js'></script>
	<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js'></script>
	<script type='text/javascript' src='js/jquery.weekcalendar.js'></script>
	<script type='text/javascript' src='js/weekcalendar.js'></script>
	<script src="https://apis.google.com/js/platform.js?onload=init" async defer></script>
	<script>
		var startEndSet = new Array();
		var timeTable = new Array();
		var times;

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

			getEventData(); //기본 값 예약 정보를 불러옴

			var option;
			var defaultSelect =	setStartOption();

			setEndOption(defaultSelect);

			$('select[id="startTimeSelect"]').change(function() {
				var selected;
				for (var i = 0; i < times; i++) {
					if (timeTable[i] == $('select[id="startTimeSelect"]').val())
					selected = i;
				}
				setEndOption(selected);
			})

			$('button[id="reserve_button"]').click(function(){
				saveReservation();
			});

			$('button[id="cancle_button"]').click(function(){
				location.href = "event.php";
			});

			var min_date = '<?php echo $minDate?>'
			var max_date = '<?php echo $_POST['end_date']?>'

			$('#date').change(function() { //바뀐 날짜의 예약 정보를 가져옴
				if ('<?php echo $_POST['isAdmin']?>' == 'false') {
					if (this.value < min_date) {
						alert("예약 가능한 기간은\n" + min_date + " ~ " + max_date + " 입니다\n\n당일 예약이 필요한 경우 학부에 문의해주시기 바랍니다\n학부사무실 : 054-260-1414, 1378");
						this.value = min_date;
					} else if (this.value > max_date) {
						alert("예약 가능한 기간은\n" + min_date + " ~ " + max_date + " 입니다\n\n당일 예약이 필요한 경우 학부에 문의해주시기 바랍니다\n학부사무실 : 054-260-1414, 1378");
						this.value = max_date;
					}
				}

				getEventData();

				var defaultSelect =	setStartOption();

				setEndOption(defaultSelect);
			});
		});

		//강의실, 날짜를 사용해 예약 정보를 가져옴
		function getEventData() {
			//예약된 시간대를 저장하는 변수를 초기화
			for (var i = 0; i < times; i++) {
				startEndSet[i] = false;
			}
			var room = '<?php echo $_POST['room_name']?>';
			var date = $("#date").val();

			$.ajax({ //default date reservation information
				url: "./inc/function.php",
				type: "POST",
				async: false,
				data: {
					mode: "getReservationForDate",
					room_name: room,
					date: date
				},
				success : function(data) {
					getEventTime(data ,startEndSet);
				}
			});
		}

		//startEndSet에 해당하는 시간대를 체크
		function getEventTime(data, startEndSet) {
			var eventData = JSON.parse(data);
			var tableHour;
			var tableMin;
			for (var i = 0; i < eventData.Reservation.length; i++) {
				var startHour = parseInt(eventData.Reservation[i].starttime.substr(0, 2));
				var startMin = parseInt(eventData.Reservation[i].starttime.substr(3, 2));
				var endHour = parseInt(eventData.Reservation[i].endtime.substr(0, 2));
				if (endHour == 0) endHour = 24;
				var endMin = parseInt(eventData.Reservation[i].endtime.substr(3, 2));

				//조건에 맞춰 체크
				for (var j = 0; j < times; j++) {
					tableHour = parseInt(timeTable[j].substr(0, 2));
					tableMin = parseInt(timeTable[j].substr(3, 2));
					if (tableHour >= startHour && tableHour <= endHour) {
						if (startHour == endHour) {
							if (tableHour == endHour && tableMin < endMin) {
								startEndSet[j] = true;
							}
						} else {
							if (tableHour == endHour && tableMin >= endMin) {
							} else if (tableHour == startHour && tableMin < startMin) {
							} else {
								startEndSet[j] = true;
							}
						}
					}
				}
			}
		}

		function saveReservation() {
			var roomname = String($('table td').eq(0).html()).replace("<br>","").replace(/\n/g, '').replace(/\t/g, '');
			var username = String($('table td').eq(1).html()).replace("<br>","").replace(/\s/g,'');
			var email = String($('table td').eq(2).html()).replace("<br>","").replace(/\s/g,'');
			var major = $("select[name=major]").val();
			var date = $('input[type="date"]').val();
			var starttime = $('#startTimeSelect').val() + ":00";
			var endtime = $('#endTimeSelect').val() + ":00";

			console.log(major);

			if ($('input[name="professor"]').val().length == 0) {
				alert("관련 교수를 적어주세요!");
				return;
			} else {
				var professor = $('input[name="professor"]').val();
			}

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

			$.ajax({
				url: "./inc/function.php",
				type: "POST",
				data: {
					roomname: roomname,
					username: username,
					email: email,
					professor: professor,
					major: major,
					date: date,
					starttime: starttime,
					endtime: endtime,
					purpose: purpose,
					phone: phone,
					memo: memo,
					permission: 0,
					mode: 'addEvent'
				},
				success: function(data) {
					if(data==="same") {
						alert("이미 예약이 되어있습니다");

						getEventData();

						var defaultSelect =	setStartOption();

						setEndOption(defaultSelect);

					}	else if(data==="error"){
						alert("예약중 문제가 발생하였습니다");
						location.href = "event.php";
					} else if(data==="user_not_found"){
						alert("유효하지 않은 사용자입니다");
						location.href = "event.php";
					} else if(data==="room_not_found"){
						alert("유효하지 않은 강의실입니다");
						location.href = "event.php";
					}	else {
						alert("예약되었습니다");
						location.href = ("event.php?room_name="+ roomname);
					}
				}
			});
		}

		function leadingZeros(n, digits) {
			var zero = '';
			n = n.toString();

			if (n.length < digits) {
				for (var i = 0; i < digits - n.length; i++)
				zero += '0';
			}
			return zero + n;
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
					option = "<option id='start" + i + "' name='startOption' disabled>" + timeTable[i] + " (불가)</option>";
				}
				$('#startTimeSelect').append(option);
			}
			return defaultSelect;
		}

		function setEndOption(selected) {
			$('select#endTimeSelect option').remove();

			if (selected >-1){
				for (var i = selected; i < selected + 6; i++) {
					if (i == 31) {
						if (!startEndSet[i]) {
							option = "<option id='end" + i + "' name='endOption'>24:00</option>";
						} else {
							break;
						}
					} else {
						if (!startEndSet[i]) {
							option = "<option id='end" + i + "' name='endOption'>" + timeTable[i + 1] + "</option>";
						} else {
							break;
						}
					}

					if (i < 32) {
						$('#endTimeSelect').append(option);
					} else {
						break;
					}

				}

			}


		}
		function clause(){

			var f=document.forms[0];

			if(f.ch.checked)

			f.btn.disabled=false;

			else

			f.btn.disabled=true;

		}
	</script>



	<div class="w3-container" style="float: left;margin-bottom: 2rem;font-family: 'Noto Sans KR', sans-serif;">
		<h2><b>예약 정보 입력</b></h2>
			<table style="width: 100%; height: 30rem; background-color: white;">
				<tr>
					<th>장소</th>
					<td colspan="17" style="border-left: 0;">
						<?php echo $_POST['room_name']?><br>
					</td>
				</tr>
				<tr>
					<th>이름</th>
					<td colspan="17" style="border-left: 0;">
						<?php echo $_POST['user_name']?><br>
					</td>
				</tr>
				<tr>
					<th>이메일</th>
					<td colspan="17" style="border-left: 0;">
						<?php echo $_POST['user_email']?><br>
					</td>
				</tr>
				<tr>
					<th>관련 교수 *</th>
					<td colspan="17" style="border-left: 0;">
						<input type="text" class="input-resize" name="professor" placeholder="Ex) 용환기 교수님">
					</td>
				</tr>
				<tr>
					<th>학부</th>
					<td colspan="17" style="border-left: 0;">
						<select name="major">
							<option value="글로벌리더십학부">글로벌리더십학부</option>
							<option value="국제어문학부">국제어문학부</option>
							<option value="경영경제학부">경영경제학부</option>
							<option value="법학부">법학부</option>
							<option value="언론정보문화학부">언론정보문화학부</option>
							<option value="공간환경시스템공학부">공간환경시스템공학부</option>
							<option value="기계제어공학부">기계제어공학부</option>
							<option value="콘텐츠융합디자인학부">콘텐츠융합디자인학부</option>
							<option value="생명과학부">생명과학부</option>
							<option value="전산전자공학부" selected>전산전자공학부</option>
							<option value="상담심리사회복지학부">상담심리사회복지학부</option>
							<option value="ICT창업학부">ICT창업학부</option>
							<option value="창의융합교육원">창의융합교육원</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>예약 가능한 기간</th>
					<td colspan="17" style="border-left: 0;">
						<?php echo $minDate;?> ~ <?php echo $_POST['end_date']?>
					</td>
				</tr>
				<tr>
					<th>날짜 *</th>
					<td colspan="17" style="border-left: 0;"><input class="input-resize" id="date" type="date" name="date" value=<?php echo $minDate;?>><br></td>
				</tr>
				<tr>
					<th>시작 시간</th>
					<td style="border-left: 0;">
						<select id='startTimeSelect' class="input-resize">
						</select>
					</td>
				</tr>
				<tr>
					<th>종료 시간</th>
					<td style="border-left: 0;">
						<select id='endTimeSelect' class="input-resize">
						</select>
					</td>
				</tr>
				<tr>
					<th>목적 *</th>
					<td colspan="17" style="border-left: 0;">
						<input type="text" class="input-resize" name="purpose"></input>
					</td>
				</tr>
				<tr>
					<th>연락처 *</th>
					<td colspan="17" style="border-left: 0;">
						<input type="text" class="input-resize" name="phone"></input>
					</td>
				</tr>
				<tr>
					<th>추가사항</th>
					<td colspan="17" style="border-left: 0;">
						<textarea id="memo" name="memo" style="height: 90%; width: 99%" placeholder="예상 인원, 구체적인 활동 내용 등"></textarea>
					</td>
				</tr>
			</table>
		</br>
			<div>
		<form onsubmit="saveReservation();">
		<div>
			<h3><font size="4">개인정보 수집·이용·제공 동의</font></h3>
			<textarea readonly="readonly" style="width:100%;height:100px;">
한동대학교 전산전자공학부는 귀하께서 한동대학교 CSEE 강의실 예약 사이트의 이용약관의 내용에 대해 「동의한다」버튼 또는 「동의하지 않는다」버튼을 클릭할 수 있는 절차를 마련하여, 「동의한다」버튼을 클릭하면 개인정보 수집에 대해 동의한 것으로 간주합니다.

※ 본 서비스(사이트)는 만 14세 미만의 아동에 대한 개인정보를 수집하고 있지 않으며, 홈페이지에 아동에게 유해한 정보를 게시하거나 제공하고 있지 않습니다.

한동대학교 CSEE 강의실 예약 사이트는 회원가입 시 서비스 이용을 위해 필요한 최소한의 개인정보만을 수집합니다.
귀하가 한동대학교 CSEE 강의실 예약 사이트의 서비스를 이용하기 위해서는 회원가입 시 (이름, 휴대폰번호, E-mail 주소, 학번, 학부)를 필수적으로 입력하셔야 합니다.
개인정보 항목별 구체적인 수집목적 및 이용목적은 다음과 같습니다.

- 성명, 이메일주소 : 회원제 서비스 이용에 따른 본인 식별 절차에 이용
- 이메일주소, 전화번호 : 고지사항 전달, 불만처리 등을 위한 원활한 의사소통 경로의 확보, 새로운 서비스 및 뉴스, 이벤트 정보 등의 안내

- 휴대폰번호 : 뉴스 및 이벤트 정보 전달을 위한 확보

- 기타 선택항목 : 개인맞춤 서비스를 제공하기 위한 자료


□ 수집하는 개인정보 항목
① 필수 개인정보 항목
이름, 휴대폰번호,  E-mail 주소, 학번, 학부


□ 개인정보의 보유기간 및 이용기간
한동대학교 CSEE 강의실 예약 사이트는 수집된 개인정의 보유기간은 회원가입 하신후 해지(탈퇴신청등)시까지 입니다. 또한 해지시 한동대학교 CSEE 강의실 예약 사이트는 회원님의 개인정보를 재생이 불가능한 방법으로 즉시 파기하며 (개인정보가 제3자에게 제공된 경우에는 제3자에게도 파기하도록 지시합니다.) 다만 다음 각호의 경우에는 각 호에 명시한 기간동안 개인정보를 보유합니다.

① 상법 등 법령의 규정에 의하여 보존할 필요성이 있는 경우에는 법령에서 규정한 보존기간 동안 거래내역과 최소한의 기본정보를 보유함

② 보유기간을 회원님에게 미리 고지하고 그 보유기간이 경과하지 아니한 경우와 개별적으로 회원님의 동의를 받을 경우에는 약속한 보유기간 동안 보유함

□ 동의 거부 권리 및 동의 거부 시 불이익 내용
귀하는 개인정보의 수집목적 및 이용목적에 대한 동의를 거부할 수 있으며, 동의 거부시 한동대학교 CSEE 강의실 예약 사이트에 회원가입이 되지 않으며, 한동대학교 CSEE 강의실 예약 사이트에서 제공하는 제한적인 서비스를 이용할 수 없습니다.

				 </textarea>
		</div>

		<input type="checkbox" name="ch" onclick="clause();">

		약관에 동의하시겠습니까?

		<br/>
	<div class="buttonContainer">
	<input type="button" id="reserve_button" class="w3-button w3-blue" name="btn" disabled="disabled" value="예약하기" onclick="saveReservation();">
	<input type="button" id="cancle_button" class="w3-button w3-red" value="취소하기" onclick="location.href='./event.php'">
</div>
</div>
</form>
	</div>
<?php
	include "./inc/footer.php";
?>
