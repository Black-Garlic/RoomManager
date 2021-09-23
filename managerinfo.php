<?php
	session_start();
	$menu  = 7;
	include "./inc/top.php";
	include "./inc/gauth.php";
	include "./inc/config.php";
?>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css">
<!-- Overlay effect when opening sidebar on small screens -->
<div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>

<!-- !PAGE CONTENT! -->
<div class="w3-main" style="margin-left:300px;margin-top:43px;">

  <!-- Header -->
  <header class="w3-container" style="padding-top:22px">
    <h5><b>사용자 관리</b></h5>
  </header>

	<link rel='stylesheet' type='text/css' href='css/managerinfo.css' />

<!-- content -->
<script>
	$(document).ready(function() {
	  readManager();
	  $('#newbutton').click(function(){ createManager(); });
	  $('#modifybutton').click(function(){
		  $("#usercontrol").html("사용자 추가");
			createManager();
		});
	  $('#modify').hide();
	  $('#cancel').click(function(){
			$("#usercontrol").html("사용자 추가");
			setAdd(1);
			$('#userid').val("");
			$('#username').val("");
			$("#email").val("");
			$('#X').prop('checked', false);
			$('#O').prop('checked', false);
	  });
	  //$('#mod').click(function(){ alert("ok"); readRoomInfo(); });
	});
	function delManager(no){
		if(!confirm("정말로 삭제할래요?")) return;
		$.ajax({
			url: "inc/function.php",
			method: "POST",
			data: {
				mode: 'managerDel',
				index : no
			},
			success: function(data) {
				readManager();
			}
	   });
	}
	function fnMove(){
		var offset = $("#u-list").offset();
		$('html, body').animate({scrollTop : offset.top}, 400);
}

	function readManagerInfo(no) {
		fnMove();
		  $("#usercontrol").html("사용자 정보수정");
		  $.ajax({
			url: "inc/function.php",
			method: "POST",
			data: {
				mode: 'managerRead',
				index : no
			},
			success: function(data) {
				if(data != 'error'){
					var datalist = data.split("|");
					$('#userid').val(datalist[0]);
					$('#username').val(datalist[1]);
					$('#email').val(datalist[2]);
					if(datalist[3] == 0) {
						$('#X').prop('checked', true);
					} else {
						$('#O').prop('checked', true);
					}
					setAdd('');
				}
			}
		  });
	}

	function readManager() {
		  $.ajax({
			url: "inc/function.php",
			method: "POST",
			data: {
				mode: 'managerList'
			},
			success: function(data) {
			  $('#userlist').html(data).trigger("create");
			}
		  });
	}

	function createManager() {
   	  var no = $("#userid").val();
	  var username = $("#username").val();
	  var email = $("#email").val();
		if(email.indexOf("@handong.edu")===-1){
			alert("Only handong email can be registered!");
			return;
		}
	  var isAdmin = $("input[name=isAdmin]:checked").val();
	  var mode = "managerAdd";
	  if(no) mode = "managerModi";
	  if(!username) {
		alert("유저 이름을 입력하세요");
		$("#username").focus();
		return false;
	  }
	  $.ajax({
		url: "inc/function.php",
		method: "POST",
		data: {
			index: no,
			userName: username,
			email: email,
			isAdmin: isAdmin,
			mode: mode
		},
		success: function(data) {
			//$('#roomlist').html(data).trigger("create");
			if(data =="OK1") readManager();
			resetform();
			if(no) setAdd(1);
		}
	  });
	}

	function setAdd(f){
		if(f){
			$('#add').show();
			$('#modify').hide();
			$("#userid").val('');
		}
		else{
			$('#add').hide();
			$('#modify').show();
		}
	}

	function resetform(){
		$("#username").val('');
		$("#email").val('');
		$('#X').prop('checked', false);
		$('#O').prop('checked', false);
	}

</script>

  <div class="w3-container">
    <h5>사용자 목록</h5>
    <table id='userlist' class="w3-table w3-striped w3-bordered w3-border w3-hoverable w3-white">
      <tr>
				<th>No</th>
        <th>이름</th>
				<th>Email</th>
				<th>권한</th>
      </tr>
    </table><br>

		<?php
		global $conn;
		// paging 추가 (2019. 07. 30 권현우)
		$page='';
		$PHP_SELF = $_SERVER['PHP_SELF'];

		if(isset($_GET['page'])) $page = $_GET['page'];
		if (isset($_SESSION['manager_num'])){
			$total_article = $_SESSION['manager_num']; // 총 article 개수가 들어감
		} else {
			$sql = "SELECT * FROM User";
			$result = mysqli_query($conn, $sql);
			$total_article = mysqli_num_rows($result); // 총 article 개수가 들어감
			$_SESSION['manager_num'] = $total_article;
		}

		$view_article = 12; // 한 페이지에 보여 줄 데이터의 개수
		if (!$page) $page = 1; // 초기값은 1. (접속시 1페이지부터 보여줌)
		$start = ($page-1)*$view_article;

		// requestcontroller.php 에 데이터를 넘겨주려고 세션에 저장
		$_SESSION['start_manager'] = $start;
		$_SESSION['view_article_manager'] = $view_article;

		$total_page = ceil($total_article/$view_article);
		// 페이지 인덱스, 시작, 종료 범위 구현
		if($page%10){
			$start_page = $page - $page%10+1; // 시작페이지
		} else {
			$start_page = $page - 9;
		}
		$end_page = $start_page+10; // 끝페이지

		// 그룹 이동. 현재 에서 바로 다음 페이지로, 바로 앞 페이지로.
		// 이전 그룹으로 이동 로직
		$prev_group = $page - 1;
		if ($prev_group < 1) $prev_group = 1;

		// 다음 그룹으로 이동 로직
		$next_group = $page+1;
		if ($next_group > $total_page) $next_group = $total_page;


			// 제일 처음 페이지로 이동
			if ($page != 1) echo "<a href=$PHP_SELF?page=1>처음</a>&nbsp;";
			else echo "처음&nbsp;";

			// 이전 그룹 이동 보이게 구현한부분
			//if($page!= 1) echo "<a href=$PHP_SELF?page=$prev_group$href>◀</a>";

			// 아래쪽에 실제로 보이는 page인 1, 2, 3, ... 출력해주기
			for ($i=$start_page; $i<$end_page; $i++){
				if ($i>$total_page)break;
				if ($i==$page) echo $i."&nbsp;";
				else echo "<a href=$PHP_SELF?page=$i>$i</a> &nbsp;";
			}


			// 맨 뒤 페이지로 이동
			if ($page != $total_page) echo "<a href=$PHP_SELF?page=$total_page$href>끝</a>";
			else echo "&nbsp;끝";
			echo "<br><br>";

			 ?>

  </div>

  <div id="u-list" class="w3-container">
		<INPUT TYPE="hidden" id="userid">
    <h5 id="usercontrol">사용자 추가</h5>
		<table id='managerForm' cellspacing="10">
			<tr>
				<th class="tableName">
	        강의실 이름
				</th>
				<td class="tableName">
					<INPUT TYPE="text" id="username" placeholder="예) 이준섭학부생" style='width:210px' required><br>
				</td>
			</tr>
			<tr>
				<th>
	        유저 이메일
				</th>
				<td>
					<INPUT TYPE="text" id="email" placeholder="예) 21400552@handong.edu"  style='width:210px' required><br>
				</td>
			</tr>
			<tr>
				<th>
	        관리자 권한
				</th>
				<td>
					O <INPUT TYPE="radio" name="isAdmin" id="O" value="1" >
					X <INPUT TYPE="radio" name="isAdmin" id="X" value="0" >
					<br>
				</td>
			</tr>
		</table>
	<br>
	<div id='add'>
		<button class="w3-button w3-dark-grey" id='newbutton'>Add  <i class="fa fa-arrow-right"></i></button>
	</div>
	<div id='modify'>
		<button class="w3-button w3-dark-grey" id='modifybutton'>Modify  <i class="fa fa-arrow-right"></i></button>
		<button class="w3-button" style='border:1px solid #ddd;' id='cancel'>Cancel  <i class="fa fa-arrow-right"></i></button>
	</div>
  </div>
  <hr>

<?php
	include "./inc/footer.php";
?>
