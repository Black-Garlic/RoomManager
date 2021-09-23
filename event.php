
<!-- 김준서 강의실 별로 예약데이터 불러오기까지 완료 -->
<?php
	$menu  = 2;
	include "./inc/top.php";
	include "./inc/gauth.php";
?>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css">
<link rel="stylesheet" href="./css/list.css">

<!-- Overlay effect when opening sidebar on small screens -->
<div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>

<!-- !PAGE CONTENT! -->
<div class="w3-main" style="margin-left:300px;margin-top:43px;">

  <!-- Header -->
<header class="w3-container" style="padding-top:22px">
  <h4><b>조회/예약하기</b></h4>
</header>

<div class="w3-container">

  <!--contents-->
	<link rel="stylesheet" href="css/monthly.css?v=06051">
	<style type="text/css">
		body {
			font-family: Calibri;
			background-color: #f0f0f0;
			padding: 0em 1em;
		}
		/*강의실 버튼*/
		.room1234567{
			background-color:#ffff;
			margin-right:  3px;
			outline-style: none;
		}

		#mycalendar {
			width: 100%;
			margin: 3em auto 0 auto;
			max-width: 100em;
			border: 1px solid #666;
		}

		#info, #pass_information {
			display: inline;
			float: left;
		}

		.color_descript {
			display: inline;
			float: left;
			padding: 5px;
			margin-left: 1%;
			margin-right: 1%;
			width: 23%;
			text-align: center;
		}
	</style>
	<meta charset="utf-8">
	<script type="text/javascript" src="js/monthly.js?v=0605"></script>
	<script type="text/javascript">
	var num=0;
		$(window).load( function() {
			$(function() {
				var value = "<?php
				if(isset($_GET['room_name'])) {
					echo $_GET['room_name'];
				}
				else{
					echo "뉴턴 220호";
				}
				?>";
				$('input[value="'+ value +'"]').click();
			});
		});

		$(document).ready(function() {
			roominfo();
			$('#reserve_button').click(function() {
				if (getName() == "") {
					alert("로그인 후 이용해주세요");
				} else {
					info();
					setFormData(getName(), getEmail());
					if (admin) {
						document.getElementById('isAdmin').value = true;
					} else {
						document.getElementById('isAdmin').value = false;
					}
					document.getElementById("pass_information").submit();
				}
			});
			$('input[type="checkbox"][name="roomSelect"]').change(function() {
				console.log("button");
			})
		});

		function roominfo() { //현재 저장된 강의실 목록 불러오기
			$.ajax({
				url: "inc/function.php",
				method: "POST",
				data: {
					mode: 'readRoom',
				},
				success: function(data) {
					//$('#room').html(data).trigger("create"); //저장된 강의실 별로 체크박스 생성
					$('#room_check').html(data); //저장된 강의실 별로 체크박스 생성
					listbyroom();
				}
			});
		}

		function select_room(r_name) {
			$.ajax({
				url: "./inc/function.php",
				type: "POST",
				async:false,
				data: {
					mode: 'loadReservationRoom',
					r_name: r_name
				},
				success: function(data) {
					$('#mycalendar').empty();
					$('#mycalendar').monthly({
						mode: 'event',
						dataType: 'json',
						events: data,
						condition: num
					});
					num++;
				}
			});
			document.getElementById('room_pass').value = r_name;
		}

		function roomInformation(r_name){
			$.ajax({
				url: "./inc/function.php",
				type: "POST",
				data: {
					 name: r_name,
					 mode: 'roomDetail'
				 },
				success: function(data) {
					$('#room_detail').html(data).trigger("create");
					select_room(r_name);
					setValidDate(r_name);
				}
			});
		}

		function listbyroom(){
			var r_name = $(':checked').val();
			console.log($(':checked').val());

			$.ajax({
				url: "./inc/function.php",
				type: "POST",
				data: {
					 name: r_name,
					 mode: 'roomDetail'
				 },
				success: function(data) {
					$('#room_detail').html(data).trigger("create");
					select_room(r_name);
					setValidDate(r_name);
				}
			});
		}

		function setFormData(name, email) {
			document.getElementById('name_pass').value = name;
			document.getElementById('email_pass').value = email;
		}

		function setValidDate(r_name) {
			$.ajax({
				url: "./inc/function.php",
				type: "POST",
				data: {
					 name: r_name,
					 mode: 'getValidDate'
				 },
				success: function(data) {
					var validDate = JSON.parse(data);
					document.getElementById('start_date').value = validDate[0];
					document.getElementById('end_date').value = validDate[1];
				}
			});
		}
		function info(){
			alert("<이용안내>"
						+"\n1. 각 공간의 이용시간을 준수합니다."
						+"\n2. 공간예약은 사용 1일전 신청바랍니다. 당일예약은 학부사무실 별도 문의"
						+"\n3. 예약취소는 학부사무실 전화 취소만 가능"
						+"\n4. 공간 사용 시 안전사고가 발생하지 않도록 주의합니다."
						+"\n5. 공간 사용 후 기구 및 집기를 정리합니다."

						+"\n\n<주의사항>"
						+"\n1. 예약자 이름을 바꿔 연속으로 사용 불가합니다."
						+"\n2. 장소에서 취식은 금지합니다."
						+"\n3. 시설물을 훼손 및 파손하였을 경우 실비로 변상하여야 합니다."

						+"\n\n<문의처>"
						+"\n학부사무실 : 054-260-1414, 1378");
		}
	</script>

	<div id="room_check"></div>
	<div id="buttons">
		<input type="button" id="info" class="w3-button" value="이용안내" onclick="info()" style="width: 50%;"/>
		<form id="pass_information" action="./reserve.php" method="post" style="width: 50%;">
			<input type="hidden" id="room_pass"name="room_name" value="Default" style="hidden"/>
			<input type="hidden" id="name_pass" name="user_name" value="Default" style="hidden"/>
			<input type="hidden" id="email_pass" name="user_email" value="Default" style="hidden"/>
			<input type="hidden" id="start_date" name="start_date" value="Default" style="hidden"/>
			<input type="hidden" id="end_date" name="end_date" value="Default" style="hidden"/>
			<input type="hidden" id="isAdmin" name="isAdmin" value="false" style="hidden"/>
			<input type="button" id="reserve_button" class="w3-button" value="예약하기"  style="width: 100%;"/>
		</form>
	</div>
	<br><br>
	<!--<div id="select_room"></div>-->
	<div id="room_detail"></div>
	<div id="top_menu">
		<div class="color_descript" style="background-color: #FAF58C;">승인대기 </div>
		<div class="color_descript" style="background-color: #47FF9C;">승인</div>
		<div class="color_descript" style="background-color: #9DE4FF;">수업</div>
		<div class="color_descript" style="background-color: #AAAAAA;">지난예약</div>
	</div>
	<div class="monthly" id="mycalendar"></div>
</div>
<?php
	include "./inc/footer.php";
?>
