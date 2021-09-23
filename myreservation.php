<?php
	$menu  = 3;
	include "./inc/top.php";
	include "./inc/gauth.php"; //구글 로그인 유지
	include "./inc/config.php";
?>

<style>
/*디자인 정리*/
 #purposeSearch{
	 margin-left: 15px;
 }
 .filter_room {
		margin-bottom:  15px;
 }
 #alignment{
	 margin-right: 15px;
	 margin-bottom: 3px;
	 margin-top: 3px;
 }
</style>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css">

<link rel="stylesheet" href="./css/list.css">

<!-- jQuery UI CSS파일-->
<link rel="stylesheet" href="http://code.jquery.com/ui/1.8.18/themes/base/jquery-ui.css" type="text/css" />

<!--jQuery UI 라이브러리 js파일-->
<script src="http://code.jquery.com/ui/1.8.18/jquery-ui.min.js"></script>

<!-- Overlay effect when opening sidebar on small screens -->
<div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>

<!-- !PAGE CONTENT! -->
<div class="w3-main" style="margin-left:300px;margin-top:43px;">

<!-- Header -->
<header class="w3-container" style="padding-top:22px">
	<h4><b> 내 예약 목록 </b></h4>
</header>


<script>
	$(document).ready(function() {
		roominfo(); //현재 저장된 강의실 목록 불러와 체크박스 생성
	});

	function roominfo() { //현재 저장된 강의실 목록 불러오기
		$.ajax({
			url: "inc/function.php",
			method: "POST",
			data: {
				mode: 'roomInfoCheck',
			},
			success: function(data) {
				//$('#room').html(data).trigger("create"); //저장된 강의실 별로 체크박스 생성
				$('#room').html(data); //저장된 강의실 별로 체크박스 생성
			}
		});
	}
	function listbyroom(page){
		//var keyword = document.getElementById('searchTab').value;
		var sorting = document.getElementById("alignment"); //정렬 방식 불러오기
		var sort = sorting.options[sorting.selectedIndex].value;
		var send_array = Array(); //강의실 이름 담을 배열 생성
		var send_cnt = 0;
		var chkbox = $(".roomSelect");

		for(i=0;i<chkbox.length;i++) {
			if (chkbox[i].checked == true){ //체크된 강의실 이름 배열에 넣기
				send_array[send_cnt] = chkbox[i].value;
				send_cnt++;
			}
			$.ajax({
				url: "inc/function.php",
				method: "POST",
				async:false,
				data: {
					mode: 'myreservation',
					page: page,
					isCheck: send_array,
					sort: sort,
					email: getEmail()
					//keyword: keyword
				},
				success: function(data) {
					$('#myreservationlist').html(data).trigger("create");

				}
			});
		}

	}

	function readMyReservation(email){

		var sorting = document.getElementById("alignment"); //정렬 방식 불러오기
		var sort = sorting.options[sorting.selectedIndex].value;
		$.ajax({
			url: "inc/function.php",
			method: "POST",
			data: {
				mode: 'myreservation',
				email: email,
				sort: sort
			},
			success: function(data) {
				listbyroom(); //강의실 별로 불러오기
			}
		});
	}

	function cancle(id){
		var result = confirm("예약을 정말 취소하시겠습니까?");
		if(result){
			$.ajax({
				url: "inc/function.php",
				method: "POST",
				data: {
					mode: 'cancle',
					id: id
				},
				success: function(data) {
					alert("예약이 취소되었습니다");
					window.location.reload();
					readMyReservation(email);
				}
			});
		}
	}
	function lectureCancle(lecture){
		var result = confirm("수업을 정말 취소하시겠습니까?");
		if(result){
			$.ajax({
				url: "inc/function.php",
				method: "POST",
				data: {
					mode: 'cancle',
					lecture: lecture
				},
				success: function(data) {
					console.log(data);
					alert("수업이 취소되었습니다");
					window.location.reload();
					//readMyReservation(email);
				}
			});
		}
	}

</script>

<div id="room" class="filter_room">
	<p> 강의실 선택 </p>
</div>
<div style= "float: right;">
	<select name="alignment" id="alignment" onchange="readMyReservation(getEmail())">
		<option value="">정렬 방법 선택</option>
		<option value="request_day" selected="selected">신청일 순</option>
		<option value="use_day" >사용일 순</option>
	</select>
</div>
<div class="w3-container">
	<div class="container-table" id="myreservationlist" style="">
<!--		<li class="table-header">
       <div class="col col-1">No</div>
      <div class="col col-2">강의실</div>
      <div class="col col-3">사용목적</div>
      <div class="col col-4">메모</div>
      <div class="col col-5">신청인</div>
      <div class="col col-6">사용날짜</div>
      <div class="col col-7">사용시간</div>
      <div class="col col-8">신청일</div>
      <div class="col col-9">예약상태</div>
    </li>-->
	</div>
</div>

<?php
  include "./inc/footer.php";
?>
