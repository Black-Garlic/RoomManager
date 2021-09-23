<script>
//******로그인 확인.*******//
	var admin = false;
	function sidebarVisible(){  //로그인 시 메뉴 보이기
		$("#envelope").show();
		$("#info").show();
		$("#setting").show();
		$("#dashboard").show();
		$("#menu").show();
		$("#home-menu").show();
		$("#myreservation-menu").show();
	}

	function sidebarHide(){ //로그인 되지 않았을 경우 메뉴 숨기기
		$("#envelope").hide();
		$("#info").hide();
		$("#setting").hide();
		$("#dashboard").show();
		$("#menu").show();
		$("#home-menu").hide();
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
					console.log(data);
          if(data==1){
            //관리자메뉴보이게
            $("#admin").show();
						admin = true;
          } else{
            //관리자메뉴 안보이게
            $("#admin").hide();
				<? if($menu > 3){?>
						window.location.replace("/room.html");
				<?}?>
          }
        }
        });
  }


var email_pass = "";
var name_pass = "";

function checkLoginStatus(){
  var loginBtn = document.querySelector('#loginBtn'); //id값이 loginBtn인 요소를 가져옴
  var nameTxt = document.querySelector('#name'); //id값이 name인 요소를 가져옴
  if(gauth.isSignedIn.get()){ //로그인 되어있는 경우
    loginBtn.value = 'Logout'; //버튼 값을 Logout으로 바꿈
    sidebarVisible();
    var profile = gauth.currentUser.get().getBasicProfile(); //현재 유저의 profile을 불러옴
    email_pass = profile.getEmail();
    name_pass = profile.getName();
		readMyReservation(email_pass);
    userInfo(email_pass, name_pass);
    nameTxt.innerHTML = 'Welcome <strong>'+gauth.currentUser.get().getBasicProfile().getName()+'</strong> ';
  } else {
		loginBtn.value = 'Login'; //버튼 값을 Login으로 바꿈
    nameTxt.innerHTML = '모바일은 Chrome, Safari를 사용해주세요';
		sidebarHide();
		var link = document.location.href;
		if(link.indexOf("event.php")== -1){
			location.href="./event.php";
		}
    //location.href="./index.php"; //로그아웃된 상태면 메인 페이지로 자동이동
  }
}

function init(){
  gapi.load('auth2', function() { //gapi = google API
    window.gauth = gapi.auth2.init({ //초기화
      client_id:'544635722764-qpt9h2shqnjdh2049d1ubbo22gjf8672.apps.googleusercontent.com'
    })
    gauth.then(function(){ //초기화가 성공하면 첫 번쨰 인자로 받은 함수 호출
      checkLoginStatus(); //로그인 상태 확인 함수
    }, function(){ // 초기화가 실패하면 두 번째 인자로 받은 함수 호출

    });
  });
}
function getEmail() {
  return email_pass;
}
function getName() {
  return name_pass;
}
</script>
<script src="https://apis.google.com/js/platform.js?onload=init" async defer></script>
