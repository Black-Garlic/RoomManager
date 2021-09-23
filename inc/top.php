<!DOCTYPE html>
<html>
<title>Room Manager</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<script type="text/javascript" src="js/jquery.js"></script>
<script>
// Cannot read property 'msie' of undefined 에러 해결
jQuery.browser = {};
(function () {
    jQuery.browser.msie = false;
    jQuery.browser.version = 0;
    if (navigator.userAgent.match(/MSIE ([0-9]+)\./)) {
        jQuery.browser.msie = true;
        jQuery.browser.version = RegExp.$1;
    }
})();
function readMyReservation(){}
</script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css">
<link rel="stylesheet" href="w3css/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<link href="https://fonts.googleapis.com/css?family=Noto+Sans+KR&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
html,body,h1,h2,h3,h4,h5 {font-family: "Raleway", sans-serif; font-family: 'Noto Sans KR', sans-serif;}
</style>
<body class="w3-light-grey">

<!-- Top container -->
<div class="w3-bar w3-top w3-black w3-large" style="left: 0px; z-index:4">
  <button class="w3-bar-item w3-button w3-hide-large w3-hover-none w3-hover-text-light-grey" onclick="w3_open();"><i class="fa fa-bars"></i>  Menu</button>
  <span style='font-weight:bold;font-size:8pt;' class="w3-bar-item w3-right"><span style='color:skyblue;font-weight:bold;font-size:15pt'>CS</span><span style='color:orange;font-weight:bold;font-size:15pt'>EE</span> at Handong Global University </span>
</div>

<!-- Sidebar/menu -->
<style>
	#lefttitle {
		color: #fff!important;
		background-color: #17a2b8!important;
		border-color: #17a2b8;
		display: inline-block;
		font-weight: 400;
		color: #212529;
		text-align: center;
		vertical-align: middle;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
		background-color: transparent;
		border: 1px solid transparent;
		padding: .275rem .75rem;
		font-size: 1rem;
		line-height: 1.5;
		border-radius: .25rem;
		transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
	}
</style>
<div id = "l_sidebar">
<nav class="w3-sidebar w3-collapse w3-white w3-animate-left" style="left: 0px;top: 47px;z-index:3;width:300px;" id="mySidebar"><br>
  <div class="w3-container w3-row">
    <div class="w3-col s4">
      <img src="img/office_space_chair_office_table_room.png" class="w3-circle w3-margin-right" style="height:70px">
    </div>
    <div class="w3-col s8 w3-bar" style='padding:5px 10px'>
      <span id='lefttitle'>CSEE 강의실 대여</span><br>
      <div style="border:1px solid; border-color: white; height: 8px;">
      </div>
      <span id="name" style="font-family: 'Noto Sans KR', sans-serif;"></span>

      <input style="margin-top:3px" type="button"style="margin-top:10px"  id="loginBtn" value="checking..." onclick="
		    if(this.value === 'Login'){
		      gauth.signIn({
            scope : 'https://www.googleapis.com/auth/gmail.send'
          }).then(function(){
            location.href='./index.php';
		        checkLoginStatus();
		      },function(){
            alert('한동대학교 구글 계정으로 로그인해주세요! \n ex)21500153@handong.edu');
          });
		    } else {
		      gauth.signOut().then(function(){
            location.reload();
		        checkLoginStatus();
		      });
		    }
		  ">
    </div>
  </div>
  <hr>
  <div class="w3-container" id="dashboard">
    <h5>Menu</h5>
  </div>
  <div class="w3-bar-block" id="menu">
    <a href="#" id="close-menu" class="w3-bar-item w3-button w3-padding-16 w3-hide-large w3-dark-grey w3-hover-black" onclick="w3_close()" title="close menu"><i class="fa fa-remove fa-fw"></i>  Close Menu</a>
	  <a href="index.php" id="home-menu" class="w3-bar-item w3-button w3-padding<? if($menu == 1){?> w3-blue<?}?>"><i class="fa fa-home"></i>  Home</a>
    <a href="event.php" id="event-menu" class="w3-bar-item w3-button w3-padding<? if($menu == 2){?> w3-blue<?}?>"><i class="fa fa-eye fa-fw"></i>  조회 및 예약하기</a>
    <a href="myreservation.php" id="myreservation-menu" class="w3-bar-item w3-button w3-padding<? if($menu == 3){?> w3-blue<?}?>"><i class="fa fa-calendar-check"></i>  내가 예약한 강의실</a>
    <div id="admin" style="display: none;">
      <a href="roominfo.php" class="w3-bar-item w3-button w3-padding<? if($menu == 4){?> w3-blue<?}?>"><i class="fas fa-school"></i>  강의실 관리</a>
      <a href="request.php" class="w3-bar-item w3-button w3-padding<? if($menu == 8){?> w3-blue<?}?>"><i class="fas fa-clipboard-list"></i>  승인 대기 현황</a>
      <a href="lecture.php" class="w3-bar-item w3-button w3-padding<? if($menu == 6){?> w3-blue<?}?>"><i class="fas fa-pen-fancy"></i>  수업등록</a>
      <a href="managerinfo.php" class="w3-bar-item w3-button w3-padding<? if($menu == 7){?> w3-blue<?}?>"><i class="fa fa-users fa-fw"></i>  관리자 관리</a>
    </div>
 </div>
</nav>
</div>
