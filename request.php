<?php
///////////////////////////////////////////////////
// session_start();
///////////////////////////////////////////////////

	$menu  = 8;
	include "./inc/top.php";
	include "./inc/gauth.php"; //구글 로그인 유지
?>

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

<style>
input[type=date]::-webkit-inner-spin-button {
	-webkit-appearance: none;
	display: none;
}

input[type=date]::-webkit-clear-button {
	-webkit-appearance: none;
	display: none;
}
</style>

  <!-- Header -->
  <header class="w3-container" style="padding-top:22px; margin-left:30px;">
    <h4><b> 승인 대기 현황 </b></h4>
  </header>

<script src="base64js.min.js"></script>
<script>
var clientId = '544635722764-qpt9h2shqnjdh2049d1ubbo22gjf8672.apps.googleusercontent.com';
var apiKey = 'AIzaSyBCj1XjjNlAIlqOyquo0sFfMtq4DUw5fh4';
var scopes = 'https://www.googleapis.com/auth/gmail.send';

$(document).ready(function() {
	roominfo(); //현재 저장된 강의실 목록 불러와 체크박스 생성
  listbyroom();

  $('#searchDate').click(function(){
    listbyroom();
  }); //기간 선택 후 검색버튼을 누르면

  $('#toinit').click(function(){ //초기화 버튼 누르면 선택했던 날짜 초기화
    $('#startDate').val("");
    $('#endDate').val("");
    listbyroom();
  });

	$('#search').click(function(){
    listbyroom();
  });

	$('#init').click(function(){ //초기화 버튼 누르면 검색 초기화
    $('#searchTab').val("");
    listbyroom();
  });
});


function roominfo() { //현재 저장된 강의실 목록 불러오기
		$.ajax({
			url: "inc/function.php",
			method: "POST",
			data: {
				 mode: 'roomInfoCheck',
			 },
			success: function(data) {
				$('#room').html(data).trigger("create"); //저장된 강의실 별로 체크박스 생성
		}
		});
}

function listbyroom(page){
	var start = document.getElementById('startDate').value; //시작 날짜 받아옴
	var end = document.getElementById('endDate').value; //끝 날짜 받아옴
	var sorting = document.getElementById("alignment"); //정렬 방식 불러오기
	var sort = sorting.options[sorting.selectedIndex].value;
  var listing = document.getElementById("show_options"); //정렬 방식 불러오기
  var list = listing.options[listing.selectedIndex].value;
	var send_array = Array(); //강의실 이름 담을 배열 생성
	var send_cnt = 0;
	var chkbox = $(".roomSelect");
	var keyword = document.getElementById("searchTab").value;
	var options = document.getElementById("option");
	var option = options.options[options.selectedIndex].value;

	for(i=0;i<chkbox.length;i++) {
		if (chkbox[i].checked == true){ //체크된 강의실 이름 배열에 넣기
			send_array[send_cnt] = chkbox[i].value;
			send_cnt++;
	}
}
	$.ajax({
		url: "inc/function.php",
		method: "POST",
		async:false,
		data: {
			 mode: 'readReadyReservation',
			 isCheck: send_array,
			 sort: sort,
       list: list,
			 start: start,
       end: end,
			 page: page,
			 keyword: keyword,
			 option: option
		 },
		success: function(data) {
			$('#myreservationlist').html(data).trigger("create");
	}
	});
}

/////////////////////////////////////////////////////////////////////////
function approval(num){ //예약 승인 기능
  $.ajax({
    url: "inc/function.php",
    method: "POST",
    data: {
      mode: 'accept',
      id : num
    },
    success: function(data) {
      var send_mail = data.split("/");
			sendmail(send_mail[0],send_mail[1]);
      //sendEmail(send_mail[0],send_mail[1]);
			window.location.reload();
			listbyroom();
      //listbyroom(start,end);
    }
  });
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
function save_excel(){
  location.replace('save_excel.php');
}
function reject_excel(){
  location.replace('reject_excel.php');
}

function reject(num){ //예약 거절 기능
  var result = confirm("예약을 정말 거절하시겠습니까?");
  if(result){
    $.ajax({
      url: "inc/function.php",
      method: "POST",
      data: {
        mode: 'reject',
        id : num
      },
      success: function(data) {
        var send_mail = data.split("/");
				sendmail(send_mail[0],send_mail[1]);
        //sendEmail(send_mail[0],send_mail[1]);
				window.location.reload();
				listbyroom();
        //listbyroom(start,end);
      }
     });
  }
}
//////////////////////////////////////////////////////////////
function handleClientLoad() {
  gapi.client.setApiKey(apiKey);
  window.setTimeout(checkAuth, 1);
}
function checkAuth() {
  gapi.auth.authorize({
    client_id: clientId,
    scope: scopes,
    immediate: true
  }, handleAuthResult);
}
function handleAuthClick() {
  gapi.auth.authorize({
    client_id: clientId,
    scope: scopes,
    immediate: false
  }, handleAuthResult);
  return false;
}
function handleAuthResult(authResult) {
  if(authResult && !authResult.error) {
    loadGmailApi();
  } else {
      handleAuthClick();
  }
}

function loadGmailApi() {
  gapi.client.load('gmail', 'v1', function(){
  });
}
function sendEmail(to,content)
{
  content += "\n\n"+"©한동대학교 전산전자 공학부";
  sendMessage(
    {
      'To': to.replace('\n',''),
      //'To': 'hlkim@handong.edu',
      'Subject': '[CSEE Lecture Room Reservation System]'
    },
    content
  );
  return false;
}

function sendMessage(headers_obj, message, callback)
{
  console.log(message);
  var email = '';
  for(var header in headers_obj)
    email += header += ": "+headers_obj[header]+"\r\n";
  email += "\r\n" + message;
  var sendRequest = gapi.client.gmail.users.messages.send({
    'userId': 'hlkim@handong.edu', //'me' 일 경우 접속되어 있는 관리자 메일
    //'userId': 'me', //구글 클라우드 플랫폼 키 설정을 김현리 선생님 계정으로 바꿔야함
    'resource': {
      'raw': window.Base64Encode(email).replace(/\+/g, '-').replace(/\//g, '_')
    }
  });
  return sendRequest.execute(function(){
    console.log('success!');
  });
}

function Base64Encode(str, encoding = 'utf-8') {
    var bytes = new (TextEncoder || TextEncoderLite)(encoding).encode(str);
    return base64js.fromByteArray(bytes);
}

function chk_all(){ //체크박스 전체선택 기능
  var cnt=$("input[id='checkall']:checked").length;
  if(cnt==1){
    // 체크박스 전체 체크로 바꿈
      $('input[name*="check"]:checkbox').prop("checked", "checked");
  }
  else{
    //체크 박스 전체 체크해제
      $('input[name*="check"]:checkbox').removeProp("checked");
  }
}

function undo_chk(){ //한 개의 체크박스라도 체크되어 있지 않으면 전체선택 체크박스 체크 해제
  var cnt=$("input[name='check']:checked").length;
  var cnt2=$("input[name='check']").length;
  if(!((cnt2+1)==cnt)) $("input[id='checkall']:checked").removeProp("checked");
}

function chk_approval(){ //체크 된 항목들 승인 시켜줌
  $('input[name*="check"]').each(function(i){
      if($(this).is(":checked")){
          approval($(this).val());
      }
  });
}
function enterkey() {
        if (window.event.keyCode == 13) {

             // 엔터키가 눌렸을 때 실행할 내용
             searchReservation();
        }
}
///////////////////////////////////////////////////////////////////
</script>
<script src="https://apis.google.com/js/client.js?onload=handleClientLoad"></script>

<div id="room" class="filter_room w3-margin">
	<p> 강의실 별 </p>
</div>
<div style="margin-left:40px;">
	<div class="w3-left w3-margin-left">
		<select name="show_options" id="show_options" onchange="listbyroom()">
			<option value="all">전체</option>
			<option value="waiting" selected="selected">승인대기</option>
			<option value="approved" >승인</option>
			<option value="rejected" >거절</option>
		</select>
	</div>
	<div class="w3-left w3-margin-left">
		<select name="alignment" id="alignment" onchange="listbyroom()">
			<option value="">정렬 방법 선택</option>
			<option value="request_day" selected="selected">신청일 순</option>
			<option value="use_day" >사용일 순</option>
		</select>
	</div>
	<div class="w3-left w3-margin-left">
		<select id="option">
			<option value="name">사용자</option>
			<option value="purpose">목적</option>
		</select>
		<input onkeyup="enterkey();" type="text" id="searchTab" placeholder="       Search . . ." required>
		<input type="button" id="search" value="검색">
		<input type="button" id="init" value="초기화">
	</div>
	<div class="w3-left w3-margin-left">
		<input type="date" id="startDate" value="<?php echo date('Y-m-d');?>"> ~
		<input type="date" id="endDate" value="<?php echo date('Y-m-d', strtotime("+1 months"));?>">
		<!--<input type="button" id="searchDate" value="날짜검색">-->
	</div>
</div>
<div class="w3-container">
	<div class="container-table" id="myreservationlist" >
		<li class="table-header">
			<div class="col col-1">No</div>
			<div class="col col-2">강의실</div>
			<div class="col col-3">사용날짜</div>
			<div class="col col-4">사용시간</div>
			<div class="col col-5">사용목적</div>
			<div class="col col-6">메모</div>
			<div class="col col-7">신청인</div>
			<div class="col col-8">연락처</div>
			<div class="col col-9">학부</div>
			<div class="col col-10">관련교수</div>
			<div class="col col-11">신청일</div>
			<div class="col col-12">승인여부</div>
			<div class="col col-13">예약상태</div>
    </li>
	</div>
</div>

<?php
  include "./inc/footer.php";
?>
