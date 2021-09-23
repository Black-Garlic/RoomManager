<?php
	$menu  = 4;
	include "./inc/top.php";
	include "./inc/gauth.php";
?>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css">

<link rel="stylesheet" href="./css/list.css">
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
<!-- Overlay effect when opening sidebar on small screens -->
<div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>


<!-- !PAGE CONTENT! -->
<div class="w3-main" style="margin-left:300px;margin-top:43px;">

  <!-- Header -->
  <header class="w3-margin-left w3-container " style="padding-top:22px">
    <h4><b>강의실 관리</b></h4>
  </header>
<link rel='stylesheet' type='text/css' href='css/roominfo.css' />
<!-- content -->
<script>
	$(document).ready(function() {
	  readRoom();
	  $('#newbutton').click(function(){ createRoom(); });
	  $('#modifybutton').click(function(){
			$("#roomcontrol").html("강의실추가");
			createRoom();
		});
	  $('#modify').hide();
	  $('#cancel').click(function(){
			$("#roomcontrol").html("강의실추가");
			setAdd(1);
			$('#roomid').val("");
			$('#roomname').val("");
			$("#capacity").val("");
			$('#O').prop('checked', true);
			$('#X').prop('checked', false);
			$("#startDate").val('<?php echo date('Y-m-d');?>');
			$("#endDate").val('<?php echo date('Y-m-d', strtotime("+1 years -1 days"));?>');
			$('#memo').val("");
	  });
	  //$('#mod').click(function(){ alert("ok"); readRoomInfo(); });
	});

	function delRoom(no){
		if(!confirm("정말로 삭제할래요?")) return;
		$.ajax({
			url: "./inc/function.php",
			method: "POST",
			data: {
				mode: 'del',
				index : no
			},
			success: function(data) {
				readRoom();
			}
	   });
	}
	function fnMove(){
			var offset = $("#room-manage").offset();
			$('html, body').animate({scrollTop : offset.top}, 400);
	}
	function readRoomInfo(no) {
		fnMove();
		$("#roomcontrol").html("강의실 수정");
		  $.ajax({
			url: "./inc/function.php",
			method: "POST",
			data: {
				mode: 'read',
				index : no
			},
			success: function(data) {
				if(data != 'error'){
					var datalist = data.split("|");
					$('#roomid').val(datalist[0]);
					$('#roomname').val(datalist[1]);
					$('#capacity').val(datalist[2]);
					if(datalist[3] == 0) {
						$('#X').prop('checked', true);
					} else {
						$('#O').prop('checked', true);
					}
					$('#startDate').val(datalist[4]);
					$('#endDate').val(datalist[5]);
					$('#memo').val(datalist[6]);
					setAdd('');
				}
			}
		  });
	}

	function readRoom() {
		  $.ajax({
			url: "./inc/function.php",
			method: "POST",
			data: { mode: 'list'},
			success: function(data) {
			  $('#roomlist').html(data).trigger("create");
			}
		  });
	}

	function createRoom() {
		var no = $("#roomid").val();
	  var roomname = $("#roomname").val();
		var capacity = $("#capacity").val();
		var projector = $("input[name=projector]:checked").val();
		var startDate = $("input[id=startDate]").val();
		var endDate = $("input[id=endDate]").val();
	  var desc = $("#memo").val();
	  var mode = "add";
	  if(no) mode = "modi";
	  if(!roomname) {
		alert("강의실 이름을 입력하세요");
		$("#roomname").focus();
		return false;
	  }
	  $.ajax({
		url: "./inc/function.php",
		method: "POST",
		data: {
			index: no,
			roomName: roomname,
			capacity: capacity,
			projector: projector,
			startDate: startDate,
			endDate: endDate,
			description: desc,
			mode: mode
		},
		success: function(data) {
			//$('#roomlist').html(data).trigger("create");
			if(data =="OK1") readRoom();
			resetform();
			if(no) setAdd(1);
		}
	  });
	}

	function setAdd(f){
		if(f){
			$('#add').show();
			$('#modify').hide();
			$("#roomid").val('');
		}
		else{
			$('#add').hide();
			$('#modify').show();
		}
	}

	function resetform(){
		$("#roomname").val('');
		$("#capacity").val('');
		$('#O').prop('checked', true);
		$('#X').prop('checked', false);
		$("#startDate").val('<?php echo date('Y-m-d');?>');
		$("#endDate").val('<?php echo date('Y-m-d', strtotime("+1 years -1 days"));?>');
		$("#memo").val('');
	}
</script>

<div class="w3-container">
	<div class="container-table" id="roomlist" >
		<li class="table-header">
      <div class="col col-1">No</div>
      <div class="col col-2">강의실</div>
      <div class="col col-3">수용인원</div>
      <div class="col col-4-roominfo">프로젝터</div>
      <div class="col col-5">예약가능시작일</div>
      <div class="col col-6">예약가능종료일</div>
      <div class="col col-7-description">추가설명</div>
			<div class="col col-8"></div>
    </li>
	</div>
</div>

  <!--<div class="w3-container">
    <table id='roomlist' class="w3-table w3-striped w3-bordered w3-border w3-hoverable w3-white">
      <tr>
				<th name="roomNumber" style="width: 5%">No</th>
        <th name="roomName" style="width: 10%">강의실</th>
        <th name="roomCapacity" style="width: 5%">수용 인원</th>
        <th name="roomProjector" style="width: 5%">프로젝터</th>
        <th name="roomStart" style="width: 10%">예약 가능 시작일</th>
        <th name="roomEnd" style="width: 10%">예약 가능 종료일</th>
        <th name="roomDescription">추가 설명</th>
        <th name="modifyCancle" style="width: 5%"></th>
      </tr>
	  <tbody>
	  </tbody>
    </table><br>
  </div>-->

  <div id="room-manage" class="w3-container">
		<INPUT TYPE="hidden" id="roomid">
			<h5 id="roomcontrol">강의실 추가</h5>
		<table id='roomForm' cellspacing="5">
			<tr>
				<th class="tableName">
	        강의실 이름
				</th>
				<td class="tableName">
					<INPUT TYPE="text" id="roomname" placeholder="예) 뉴턴홀 310호" style='width:200px' required><br>
				</td>
			</tr>
			<tr>
				<th>
	        수용 인원
				</th>
				<td>
					<INPUT TYPE="text" id="capacity" placeholder="예) 20"  style='width:200px' required><br>
				</td>
			</tr>
			<tr>
				<th>
	        프로젝터
				</th>
				<td>
					O <INPUT TYPE="radio" name="projector" id="O" value="1" checked>
					X <INPUT TYPE="radio" name="projector" id="X" value="0" >
					<br>
				</td>
			</tr>
			<tr>
				<th>
	        예약 가능 시작일
				</th>
				<td>
					<INPUT TYPE="date" id="startDate"
					value=
					<?php
						echo date('Y-m-d');
					?>><br>
				</td>
			</tr>
			<tr>
	      <th>
	        예약 가능 종료일
				</th>
				<td>
					<INPUT TYPE="date" id="endDate"
					value=
					<?php
						echo date('Y-m-d', strtotime("+1 years -1 days"));
					?>><br>
				</td>
			</tr>
	    <tr>
				<th>
	        강의실 설명
				</th>
				<td>
					<textarea id="memo" placeholder="예) PC없음, 프로젝터 고장 등"  style='width: 100%; height: 90px' required></textarea>
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
