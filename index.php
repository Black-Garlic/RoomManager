<!doctype html>
<html>
<head>
  <?php
    include "./inc/config.php";
  ?>
  <title>CSEE Lecture Room Reservation</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css">
  <link rel="stylesheet" href="./css/list.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script>
  function sidebarVisible(){  //로그인 시 메뉴 보이기
    $("#envelope").show();
    $("#info").show();
    $("#setting").show();
    $("#dashboard").show();
    $("#menu").show();
  }
  function sidebarHide(){ //로그인 되지 않았을 경우 메뉴 숨기기
    $("#envelope").hide();
    $("#info").hide();
    $("#setting").hide();
    $("#dashboard").show();
    $("#menu").show();
    $("#myreservation-menu").hide();
  }
  function userInfo(email,name){
    var u_mail = email; //user의 이메일
    var u_name = name; //user의 이름

    $.ajax({
      url: "./inc/usercontroller.php", //usercontroller.php에 연결
      method: "POST", //post 방식으로 전송
      data: {
        email: u_mail, //이메일 보냄
        name: u_name // 이름 보냄
      },
      success: function(data) {
        reservationStatus(u_mail ,data);
        if(data==1){
          //관리자메뉴보이게
          $("#admin").show();
          $("#adminstatus").show();
        } else{
          //관리자메뉴 안보이게
          $("#admin").hide();
          $("#userstatus").show();
        }
      }
    });
  }

  function reservationStatus(email, isAdmin){
    var mode = (isAdmin==0)?'user':'admin';
    $.ajax({
      url: "inc/function.php",
      method: "POST",
      data: {
        email: email,
        mode: mode
      },
      success: function(data) {
        if(mode=='user'){
          $("#usertodaycount").html(data.split("|")[0]).trigger("create");
          $("#usersuccesscount").html(data.split("|")[1]).trigger("create");
          $("#userrejectcount").html(data.split("|")[2]).trigger("create");
          $("#userwaitingcount").html(data.split("|")[3]).trigger("create");
        }
        else{
          $("#waitingcount").html(data.split("|")[0]).trigger("create");
          $("#todaycount").html(data.split("|")[1]).trigger("create");
          $("#successcount").html(data.split("|")[2]).trigger("create");
          $("#rejectcount").html(data.split("|")[3]).trigger("create");
        }
      }
    });
  }

  function showMyReservation(type){
    changeColor(type);

    $.ajax({
      url: "inc/function.php",
      method: "POST",
      data: {
        email: gauth.currentUser.get().getBasicProfile().getEmail(),
        mode: 'mylist',
        type: type
      },
      success: function(data) {
        if($(window).width() <= 585 && (type === 'success' || type === 'today')){
          $(".mylist-big").hide();
          $(".mylist-small").show();
          $(".mylist-small").html(data).trigger("create");
        }
        else{
          $(".mylist-big").show();
          $(".mylist-small").hide();
          $(".mylist-big").html(data).trigger("create");
        }
      }
    });

  }

  function showReservation(type){

    changeColor(type);

    $.ajax({
      url: "inc/function.php",
      method: "POST",
      data: {
        mode: 'waitinglist',
        type: type
      },
      success: function(data) {
        if($(window).width() <= 585 && (type === 'waiting' || type === 'today')){
          $(".adminlist-big").hide();
          $(".adminlist-small").show();
          $(".adminlist-small").html(data).trigger("create");
        }
        else{
          $(".adminlist-big").show();
          $(".adminlist-small").hide();
          $(".adminlist-big").html(data).trigger("create");
        }

      }
    });

  }

  function changeColor(type){
    if(type==='today'){
      $(".today").addClass('w3-light-blue');
      $(".success").removeClass('w3-light-green');
      $(".reject").removeClass('w3-pale-red');
      $(".waiting").removeClass('w3-pale-yellow');
      $(".waiting").removeClass('w3-text-black');
    }
    else if(type==='success'){
      $(".today").removeClass('w3-light-blue');
      $(".success").addClass('w3-light-green');
      $(".reject").removeClass('w3-pale-red');
      $(".waiting").removeClass('w3-pale-yellow');
      $(".waiting").removeClass('w3-text-black');
    }
    else if(type==='reject'){
      $(".today").removeClass('w3-light-blue');
      $(".success").removeClass('w3-light-green');
      $(".reject").addClass('w3-pale-red');
      $(".waiting").removeClass('w3-pale-yellow');
      $(".waiting").removeClass('w3-text-black');
    }
    else if(type==='waiting'){
      $(".today").removeClass('w3-light-blue');
      $(".success").removeClass('w3-light-green');
      $(".reject").removeClass('w3-pale-red');
      $(".waiting").addClass('w3-pale-yellow');
      $(".waiting").addClass('w3-text-black');
    }
  }

  function checkLoginStatus(){
    var loginBtn = document.querySelector('#loginBtn'); //id값이 loginBtn인 요소를 가져옴
    var nameTxt = document.querySelector('#name'); //id값이 name인 요소를 가져옴
    if(gauth.isSignedIn.get()){ //로그인 되어있는 경우
      loginBtn.value = 'Logout'; //버튼 값을 Logout으로 바꿈
      sidebarVisible();
      var profile = gauth.currentUser.get().getBasicProfile(); //현재 유저의 profile을 불러옴
      nameTxt.innerHTML = 'Welcome <strong>'+profile.getName()+'</strong> ';
      var email = profile.getEmail();
      var name = profile.getName();
      userInfo(email,name); //유저정보 db에 저장
      $('#mySidebar').hide();
    } else {
      location.href="./event.php";
      loginBtn.value = 'Login'; //버튼 값을 Login으로 바꿈
      nameTxt.innerHTML = '모바일은 Chrome, Safari를 사용해주세요';
      sidebarHide();
      $('#mySidebar').show();
    }
  }
  function init(){

    gapi.load('auth2', function() { //gapi = google API / 'auth2'라이브러리 로드하고, 로드가 끝나면 함수실행
    window.gauth = gapi.auth2.init({ //초기화해줌 / return값은 googleAuth 객체
      client_id:'544635722764-qpt9h2shqnjdh2049d1ubbo22gjf8672.apps.googleusercontent.com'
    })
    gauth.then(function(){ //초기화가 성공하면 첫 번쨰 인자로 받은 함수 호출
      checkLoginStatus(); //로그인 상태 확인 함수
    }, function(){ // 초기화가 실패하면 두 번째 인자로 받은 함수 호출
alert('google error');
    });
  });
}

  </script>
  <style>
    .w3-col{
      margin-top: 1rem;
    }
  </style>
</head>
<body>
	<?php
		$menu  = 1;
		include "./inc/top.php";
	?>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css">
  <!-- jQuery UI CSS파일-->
  <link rel="stylesheet" href="http://code.jquery.com/ui/1.8.18/themes/base/jquery-ui.css" type="text/css" />

  <!--jQuery UI 라이브러리 js파일-->
  <script src="http://code.jquery.com/ui/1.8.18/jquery-ui.min.js"></script>

	<!-- Overlay effect when opening sidebar on small screens -->
	<div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>

	<!-- !PAGE CONTENT! -->
	<div class="w3-main" style="margin-left:300px;margin-top:100px;">

<header class="w3-container" style="padding-top:22px">
  <h3><b>Handong CSEE Lecture Room Reservation System</b></h3>
</header>

<div id="userstatus" style="display: none;">
<div class="w3-row-padding w3-margin-bottom">
  <div class="w3-col s6 m3">
    <div class="w3-container w3-blue w3-hover-light-blue w3-padding-16 w3-round-large today" onclick="showMyReservation('today')">
      <div class="w3-left"><i class="fa fa-calendar w3-xxxlarge"></i></div>
      <div class="w3-right">
        <h1 id="usertodaycount"></h1>
      </div>
      <div class="w3-clear"></div>
      <h4>오늘 예약</h4>
    </div>
  </div>
  <div class="w3-col s6 m3">
    <div class="w3-container w3-green w3-hover-light-green w3-padding-16 w3-round-large success" onclick="showMyReservation('success')">
      <div class="w3-left"><i class="fa fa-check-square w3-xxxlarge"></i></div>
      <div class="w3-right">
        <h1 id="usersuccesscount"></h1>
      </div>
      <div class="w3-clear"></div>
      <h4>승인된 예약</h4>
    </div>
  </div>
  <div class="w3-col s12 container-table mylist-small" style="display: none;">
  </div>
  <div class="w3-col s6 m3">
    <div class="w3-container w3-red w3-hover-pale-red w3-padding-16 w3-round-large reject" onclick="showMyReservation('reject')">
      <div class="w3-left"><i class="fa fa-ban w3-xxxlarge"></i></div>
      <div class="w3-right">
        <h1 id="userrejectcount"></h1>
      </div>
      <div class="w3-clear"></div>
      <h4>거절된 예약</h4>
    </div>
  </div>
  <div class="w3-col s6 m3">
    <div class="w3-container w3-lime w3-hover-pale-yellow w3-text-white w3-padding-16 w3-round-large waiting" onclick="showMyReservation('waiting')">
      <div class="w3-left"><i class="fa fa-list-ul w3-xxxlarge"></i></div>
      <div class="w3-right">
        <h1 id="userwaitingcount"></h1>
      </div>
      <div class="w3-clear"></div>
      <h4>대기중 예약</h4>
    </div>
  </div>
</div>

<div class="w3-panel">
  <div class="w3-row-padding" style="margin:0 -16px">
      <div class="container-table mylist-big">
      </div>
  </div>
</div>
</div>

<div id="adminstatus" style="display: none;">
<div class="w3-row-padding w3-margin-bottom">
  <div class="w3-col s6 m3">
    <div class="w3-container w3-lime w3-hover-pale-yellow w3-text-white w3-padding-16 w3-round-large waiting" onclick="showReservation('waiting')">
      <div class="w3-left"><i class="fa fa-list w3-xxxlarge"></i></div>
      <div class="w3-right">
        <h1 id="waitingcount"></h1>
      </div>
      <div class="w3-clear"></div>
      <h4>승인 대기</h4>
    </div>
  </div>
  <div class="w3-col s6 m3">
    <div class="w3-container w3-blue w3-hover-light-blue w3-padding-16 w3-round-large today" onclick="showReservation('today')">
      <div class="w3-left"><i class="fa fa-calendar w3-xxxlarge"></i></div>
      <div class="w3-right">
        <h1 id="todaycount"></h1>
      </div>
      <div class="w3-clear"></div>
      <h4>오늘 예약</h4>
    </div>
  </div>
  <div class="w3-col s12 container-table adminlist-small" style="display: none;">
  </div>
  <div class="w3-col s6 m3">
    <div class="w3-container w3-green w3-hover-light-green w3-padding-16 w3-round-large success" onclick="showReservation('success')">
      <div class="w3-left"><i class="fa fa-check-square w3-xxxlarge"></i></div>
      <div class="w3-right">
        <h1 id="successcount"></h1>
      </div>
      <div class="w3-clear"></div>
      <h4>승인한 예약</h4>
    </div>
  </div>
  <div class="w3-col s6 m3">
    <div class="w3-container w3-red w3-hover-pale-red w3-padding-16 w3-round-large reject" onclick="showReservation('reject')">
      <div class="w3-left"><i class="fa fa-ban w3-xxxlarge"></i></div>
      <div class="w3-right">
        <h1 id="rejectcount"></h1>
      </div>
      <div class="w3-clear"></div>
      <h4>거절한 예약</h4>
    </div>
  </div>
</div>

<div class="w3-panel" style="">
  <div class="w3-row-padding" style="margin:0 -16px">
      <div class="container-table adminlist-big">
      </div>
  </div>
</div>
</div>






	<?php
		include "./inc/footer.php";
	?>
  <!--로딩이 끝났을 때 init이라는 함수 호출(platform.js안에 구글api 정의되어있음) -->
  <script src="https://apis.google.com/js/platform.js?onload=init" async defer></script>
</body>
</html>
