<?php
include "config.php";
$mode = $_POST['mode'];
session_start(); // 변수를 쉽게 넘겨주려고 세션 사용
//index
if($mode == 'user') countUserInfo();
else if($mode == 'admin') countAdminInfo();
else if($mode == 'mylist') readMyReservation();
else if($mode == 'waitinglist') readReservation();
else if($mode == 'checkuser') checkUser();

//event
else if($mode == 'readRoom') readRoom();
else if($mode == 'loadReservationRoom') loadReservation();
else if($mode == 'roomDetail') roomDetail();
else if($mode == 'addEvent') addReservation();
else if($mode == 'getReservationForDate') getReservationForDate();
else if($mode == 'getValidDate') getValidDate();

//myreservation
else if($mode == 'myreservation') getMyReservation(); //저장된 예약 정보 불러와서 출력
else if($mode == 'cancle') cancleReservation();
else if($mode == 'roomInfoCheck') roomInfoCheck();//저장된 강의실 목록 불러와 체크박스 생성

//roominfo
else if($mode == 'list') readRoomList();
else if($mode == 'add') createRoom();
else if($mode == 'read') readRoomInfo();
else if($mode == 'modi')  createRoom();
else if($mode == 'del')  deleteRoom();

//request
else if($mode == 'readReadyReservation') readReadyReservation(); //저장된 예약 정보 불러와서 출력
else if($mode == 'accept') acceptRequest(); //예약 승인
else if($mode == 'reject') rejectRequest(); //예약 거절

//lecture
else if($mode == 'roomSelect') readRoomSelect(); //강의실 목록을 select로 echo
else if($mode == 'checkReservation') checkReservation(); //선택된 시간에 다른 예약이 있는지 확인
else if($mode == 'addLecture') addLecture();
else if($mode == 'deleteReservation') deleteReservation();

//managerinfo
else if($mode == 'managerList') readManagerList();
else if($mode == 'managerAdd') createManager();
else if($mode == 'managerRead') readManagerInfo();
else if($mode == 'managerModi')  createManager();
else if($mode == 'managerDel')  deleteManager();

else if($mode == 'getLectureNum') getLectureNum();

///////////////////////////////////////////
function getLectureNum(){
	global $conn;

	$lecture_num = date("YmdH");
	$sql ="SELECT max(id) from Reservation";
    $result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_row($result);
	if($row)	$lecture_num .= ($row[0] + 1);
	echo  $lecture_num;
}
/*
function getLectureNum(){
	global $conn;

	$lecture_num = date("YmdH");
	$sql ="SELECT max(id) from Reservation";
    $result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_row($result);
	if($row)	$lecture_num .= ($row[0] + 1);
	return $lecture_num;
}*/
////////////////////////////////////////////



//index
function countUserInfo(){
  date_default_timezone_set('Asia/Seoul');
  global $conn;
  $mail = $_POST['email'];
  $message = "";

  $sql ="SELECT Reservation.id, permission,room_name,purpose,memo,name,mail,Reservation.regdate, reservation_date, start_time, end_time
         FROM User JOIN Reservation ON User.id = Reservation.User_id
                  JOIN LectureRoom ON Reservation.LectureRoom_id = LectureRoom.id";
  $sql .= " WHERE mail='".$mail."' and reservation_date='".date("Y/m/d")."' and permission!='3'";

  $result = mysqli_query($conn, $sql);
  $total_rows = mysqli_num_rows($result);
  $count = 0;

    if ($total_rows >0 ){
			while($row = mysqli_fetch_assoc($result)) {
        $count++;
			}
		}

    $message .= $count;

    $sql ="SELECT Reservation.id, permission,room_name,purpose,memo,name,mail,Reservation.regdate, reservation_date, start_time, end_time
           FROM User JOIN Reservation ON User.id = Reservation.User_id
                    JOIN LectureRoom ON Reservation.LectureRoom_id = LectureRoom.id";
    $sql .= " WHERE mail='".$mail."' and permission='1'";

    $result = mysqli_query($conn, $sql);
    $total_rows = mysqli_num_rows($result);
    $count = 0;

      if ($total_rows > 0 ){
  			while($row = mysqli_fetch_assoc($result)) {
          $count++;
  			}
  		}

    $message .= "|".$count;

    $sql ="SELECT Reservation.id, permission,room_name,purpose,memo,name,mail,Reservation.regdate, reservation_date, start_time, end_time
           FROM User JOIN Reservation ON User.id = Reservation.User_id
                    JOIN LectureRoom ON Reservation.LectureRoom_id = LectureRoom.id";
    $sql .= " WHERE mail='".$mail."' and permission='2'";

    $result = mysqli_query($conn, $sql);
    $total_rows = mysqli_num_rows($result);
    $count = 0;

      if ($total_rows > 0 ){
  			while($row = mysqli_fetch_assoc($result)) {
          $count++;
  			}
  		}

    $message .= "|".$count;

    $sql ="SELECT Reservation.id, permission,room_name,purpose,memo,name,mail,Reservation.regdate, reservation_date, start_time, end_time
           FROM User JOIN Reservation ON User.id = Reservation.User_id
                    JOIN LectureRoom ON Reservation.LectureRoom_id = LectureRoom.id";
    $sql .= " WHERE mail='".$mail."' and permission='0'";

    $result = mysqli_query($conn, $sql);
    $total_rows = mysqli_num_rows($result);
    $count = 0;
      if ($total_rows > 0 ){
  			while($row = mysqli_fetch_assoc($result)) {
          $count++;
  			}
  		}

    $message .= "|".$count;
    echo $message;
}

function countAdminInfo(){
  global $conn;
  date_default_timezone_set('Asia/Seoul');
  $mail = $_POST['email'];
  $message = "";

  $sql ="SELECT Reservation.id, permission,room_name,purpose,memo,name,mail,Reservation.regdate, reservation_date, start_time, end_time
         FROM User JOIN Reservation ON User.id = Reservation.User_id
                  JOIN LectureRoom ON Reservation.LectureRoom_id = LectureRoom.id";
  $sql .= " WHERE permission='0'";

  $result = mysqli_query($conn, $sql);
  $total_rows = mysqli_num_rows($result);
  $count = 0;

    if ($total_rows >0 ){
			while($row = mysqli_fetch_assoc($result)) {
        $count++;
			}
		}

    $message.= $count;

    $sql ="SELECT Reservation.id, permission,room_name,purpose,memo,name,mail,Reservation.regdate, reservation_date, start_time, end_time
           FROM User JOIN Reservation ON User.id = Reservation.User_id
                    JOIN LectureRoom ON Reservation.LectureRoom_id = LectureRoom.id";
    $sql .= " WHERE reservation_date='".date("Y/m/d")."' and permission!='3'";

    $result = mysqli_query($conn, $sql);
    $total_rows = mysqli_num_rows($result);
    $count = 0;

      if ($total_rows >0 ){
  			while($row = mysqli_fetch_assoc($result)) {
          $count++;
  			}
  		}

    $message.= "|".$count;

    $sql ="SELECT Reservation.id, permission,room_name,purpose,memo,name,mail,Reservation.regdate, reservation_date, start_time, end_time
           FROM User JOIN Reservation ON User.id = Reservation.User_id
                    JOIN LectureRoom ON Reservation.LectureRoom_id = LectureRoom.id";
    $sql .= " WHERE permission='1'";

    $result = mysqli_query($conn, $sql);
    $total_rows = mysqli_num_rows($result);
    $count = 0;

      if ($total_rows >0 ){
  			while($row = mysqli_fetch_assoc($result)) {
          $count++;
  			}
  		}

    $message.= "|".$count;

    $sql ="SELECT Reservation.id, permission,room_name,purpose,memo,name,mail,Reservation.regdate, reservation_date, start_time, end_time
           FROM User JOIN Reservation ON User.id = Reservation.User_id
                    JOIN LectureRoom ON Reservation.LectureRoom_id = LectureRoom.id";
    $sql .= " WHERE permission='2'";

    $result = mysqli_query($conn, $sql);
    $total_rows = mysqli_num_rows($result);
    $count = 0;

      if ($total_rows >0 ){
  			while($row = mysqli_fetch_assoc($result)) {
          $count++;
  			}
  		}

    $message.= "|".$count;

    echo $message;
}

function readMyReservation(){
  date_default_timezone_set('Asia/Seoul');
  global $conn;
  $mail = $_POST['email'];
  $type = $_POST['type'];

  $sql ="SELECT Reservation.id, permission,room_name,purpose,memo,name,mail,Reservation.regdate, reservation_date, start_time, end_time
         FROM User JOIN Reservation ON User.id = Reservation.User_id
                  JOIN LectureRoom ON Reservation.LectureRoom_id = LectureRoom.id";
  $sql .= " WHERE mail='".$mail."'";

  if($type=="today"){
    $sql .= " and reservation_date='".date("Y/m/d")."' and permission!='3'";
  }
  else if($type=="success"){
    $sql .= " and permission='1'";
  }
  else if($type=="reject"){
    $sql .= " and permission='2'";
  }
  else if($type=="waiting"){
    $sql .= " and permission='0'";
  }

  $sql .= " ORDER BY reservation_date, start_time asc";

  $result = mysqli_query($conn, $sql);
  $total_rows = mysqli_num_rows($result);
  $sql .= " LIMIT 12";
  $result = mysqli_query($conn, $sql);

    if ($total_rows >0 ){
      echo "
      <div class='container-table'>
      <ul class='responsive-table'>
      <li class='table-header' style='font-weight: bold;'>
        <div class='col col-2'>강의실</div>
        <div class='col col-3'>사용목적</div>
        <div class='col col-5'>사용날짜</div>
        <div class='col col-7'>사용시간</div>
        <div class='col col-8'>예약상태</div>
      </li>";
			while($row = mysqli_fetch_assoc($result)) {
        $startTime = $row['start_time'];
        $startTimeVal = substr($startTime,0,5);
        $endTime = $row['end_time'];
        $endTimeVal = substr($endTime,0,5);

        echo "
          <li class='table-row'>
            <div class='col col-2' data-label='강의실'>".$row['room_name']."</div>
            <div class='col col-3' data-label='사용목적'>".$row['purpose']."</div>
            <div class='col col-5' data-label='사용날짜'>".$row['reservation_date']."</div>
            <div class='col col-7' data-label='사용시간'>".$startTimeVal."~".$endTimeVal."</div>";
          if($row['permission']==0){ //대기중인 경우
            echo "<div class='col col-8' data-label='예약상태'>승인대기</div>
            </li>";
          }else if($row['permission']==1){ // 승인된 경우
            echo "<div class='col col-8' data-label='예약상태'>승인</div>
            </li>";
          }else if($row['permission']==2){ //거절된 경우
            echo "<div class='col col-8' data-label='예약상태'>거절</div>
            </li>";
          }
			}
      echo "</ul></div>";
		}

    if($total_rows > 12){
      echo "<a style='float: right' class='w3-button w3-grey' href='myreservation.php'>더 보기  <i class='fa fa-arrow-right w3-large'></i></a>";
    }

}

function readReservation(){
  global $conn;
  date_default_timezone_set('Asia/Seoul');
  $type = $_POST['type'];

  $sql ="SELECT Reservation.id, permission,room_name,purpose,memo,name,mail,Reservation.regdate, reservation_date, start_time, end_time
         FROM User JOIN Reservation ON User.id = Reservation.User_id
                  JOIN LectureRoom ON Reservation.LectureRoom_id = LectureRoom.id";

    if($type=="today"){
      $sql .= " WHERE reservation_date='".date("Y/m/d")."' and permission!='3'";
    }
    else if($type=="success"){
      $sql .= " WHERE permission='1'";
    }
    else if($type=="reject"){
      $sql .= " WHERE permission='2'";
    }
    else if($type=="waiting"){
      $sql .= " WHERE permission='0'";
    }

    $sql .= " ORDER BY reservation_date, start_time asc";

  $result = mysqli_query($conn, $sql);
  $total_rows = mysqli_num_rows($result);
  $sql .= " LIMIT 12";
  $result = mysqli_query($conn, $sql);


    if ($total_rows >0 ){
      echo "
      <div class='container-table'>
      <ul class='responsive-table'>
        <li class='table-header' style='font-weight: bold;'>
          <div class='col col-2'>강의실</div>
          <div class='col col-3'>사용목적</div>
          <div class='col col-5'>신청인</div>
          <div class='col col-6'>사용날짜</div>
          <div class='col col-7'>사용시간</div>
          <div class='col col-8'>예약상태</div>
        </li>";
			while($row = mysqli_fetch_assoc($result)) {
        $startTime = $row['start_time'];
        $startTimeVal = substr($startTime,0,5);
        $endTime = $row['end_time'];
        $endTimeVal = substr($endTime,0,5);

        echo "
          <li class='table-row'>
            <div class='col col-2' data-label='강의실'>".$row['room_name']."</div>
            <div class='col col-3' data-label='사용목적'>".$row['purpose']."</div>
            <div class='col col-5' data-label='신청인'>".$row['name']."</div>
            <div class='col col-6' data-label='사용날짜'>".$row['reservation_date']."</div>
            <div class='col col-7' data-label='사용시간'>".$startTimeVal."~".$endTimeVal."</div>";
        if($row['permission']==0){ //대기중인 경우
          echo "<div class='col col-8' data-label='예약상태'>승인대기</div>
                </li>";
        }else if($row['permission']==1){ // 승인된 경우
          echo "<div class='col col-8' data-label='예약상태'>승인</div>
                </li>";
        }else if($row['permission']==2){ //거절된 경우
          echo "<div class='col col-8' data-label='예약상태'>거절</div>
                </li>";
        }
      }
      echo "</ul></div>";
		}

    if($total_rows > 12){
      echo "<a style='float: right' class='w3-button w3-grey' href='test.php'>더 보기  <i class='fa fa-arrow-right w3-large'></i></a>";
    }

}

function checkUser() {
  $email = $_POST['email']; //로그인 시 email 받아옴
  $name = $_POST['name']; //로그인 시 name 받아옴

  global $conn;

  $sql = "SELECT * FROM User WHERE User.mail ='".$email."' AND User.isAdmin = 1";//관리자인지 확인
  $result = mysqli_query($conn, $sql);
  $total_rows = mysqli_num_rows($result);

  if($total_rows > 0){ //관리자 계정일 경우
    echo 1;  //관리자 메뉴 보이게 하기
  } else {
    echo 0;
  }

  $sql = "SELECT mail FROM User";
  $sql .=" WHERE User.mail='".$email."'"; //db에 존재하는지 확인하기 위함

  $result = mysqli_query($conn, $sql);
  $total_rows = mysqli_num_rows($result);

  if ($total_rows > 0) { //이미 존재하면 업데이트
    $sql = "UPDATE User SET name = '".$name."' WHERE mail='".$email."' ";
    $result = mysqli_query($conn, $sql);

  } else { //db에 없는 경우 db에 추가
    $sql = "INSERT INTO User (mail, name) VALUES ('$email','$name')";
    $result = mysqli_query($conn, $sql);
  }
}


//event
function readRoom(){ //저장된 강의실 목록 불러와 체크박스 생성
	global $conn;
  $sql = "SELECT * FROM LectureRoom ";
  $sql .= " ORDER BY LectureRoom.id asc";
  $result = mysqli_query($conn, $sql);
  $total_rows = mysqli_num_rows($result);

  echo '<div class="container">';
  echo '<ul class="ks-cboxtags" >';
  echo '<h7><i class="fa fa-school"></i>&nbsp;강의실 선택</h7></br>';
  if ($total_rows >0 ){
    $i = 0;
    while($row = mysqli_fetch_assoc($result)) {
      $index = $total_rows-$i;
      echo '
      <li>
      <input type="radio"
      id="'.$row['room_name'].'"
      class="roomSelect"
			name="roomSelect"
      value="'.$row['room_name'].'"';
			if ($i == 0) echo 'checked="true"';
			echo '
      onclick="listbyroom()">
      <label for="'.$row['room_name'].'">'.$row['room_name'].'</label>
      </li>';

      $i++;
    }
    echo ' </ul>
    </div>';
  }
}

function roomDetail(){
  global $conn;
  $projector;
  $name = $_POST['name'];
  $sql = "SELECT * FROM LectureRoom ";
  $sql .=" WHERE room_name = '".$name."'";
  $result = mysqli_query($conn, $sql);

  $total_rows = mysqli_num_rows($result);
  if ($total_rows >0 ){
    $i = 0;
    while($row = mysqli_fetch_assoc($result)) {
      $nameT=preg_replace("/\s+/", "", $name);
      $index = $total_rows-$i;
      $capacity = $row['capacity'];
      $description = $row['description'];
      if($row['projector']==1){
        $projector ="o";
      } else {
        $projector ="x";
      }
      //각각 버튼을 클릭하면 event.php의 select_room 함수 호출
      echo "<br>
            <table class='w3-table-all' style='text-align: left;'>
              <tr>
                <th style='min-width:60px;'>강의실</th>
                <td>$name</td>
              </tr>
              <tr>
                <th>수용 가능 인원</th>
                <td>$capacity 명</td>
              </tr>
              <tr>
                <th>프로젝터 여부</th>
                <td>$projector</td>
              </tr>
              <tr>
                <th>추가 설명</th>
                <td>$description</td>
              </tr>
            </table><br>";

      echo ("<script>
        $(document).ready(function() {
          $(\".$nameT\").css({\"background-color\":\"grey\",\"color\":\"white\"});
          $(\".room1234567\").not(\".$nameT\").css({\"background-color\":\"#ffffff\",\"color\":\"black\"});
        });
      </script>");
      $i++;
    }
  }
}

function loadReservation(){
  header('Content-Type: application/json');
  global $conn;
  $data= array();
  $r_name = $_POST['r_name'];

  $sql = "SELECT Reservation.id, room_name,purpose,name,mail,Reservation.regdate, reservation_date, memo, start_time, end_time, permission, professor FROM User JOIN Reservation ON User.id = Reservation.User_id JOIN LectureRoom ON Reservation.LectureRoom_id = LectureRoom.id";
  $sql .=" WHERE room_name = '".$r_name."' ORDER BY start_time ASC";
  $result = mysqli_query($conn, $sql);

  if ($result){
    while($row = mysqli_fetch_assoc($result)){
	  $memo = $row['professor'];
	  if(isset($row['memo'])) $memo.="<br>".$row['memo'];

      array_push($data, array(
        "id"=>$row['id'],
        "purpose"=>$row['purpose'],
        "name"=>$row['name'],
        "startdate"=>$row['reservation_date'],
        "enddate"=>$row['reservation_date'],
        "starttime"=>$row['start_time'],
        "endtime"=>$row['end_time'],
        "color"=>"#AAA",
        "memo"=>$memo,
        "room"=>$row['room_name'],
        "permission"=>$row['permission'],
        "url"=>""
      ));
    }
  }

  $Reservation['monthly'] = $data;
  $output = json_encode($Reservation, JSON_NUMERIC_CHECK);
  //  $output = json_encode($data, JSON_NUMERIC_CHECK);
  echo $output;
}

function getReservationForDate() {
  header('Content-Type: application/json');
  global $conn;
  $room = $_POST['room_name'];
  $date = $_POST['date'];
  $data= array();

  $sql = "SELECT start_time, end_time
  FROM Reservation JOIN LectureRoom ON Reservation.LectureRoom_id = LectureRoom.id
  WHERE room_name = '" . $room . "' AND reservation_date = '" . $date . "' AND Reservation.permission != 2
  ORDER BY start_time ASC";
  $result = mysqli_query($conn, $sql);

  if ($result){
    while($row = mysqli_fetch_assoc($result)){
      array_push($data, array(
        "starttime"=>$row['start_time'],
        "endtime"=>$row['end_time'],
      ));
    }
  }

  $Reservation['Reservation'] = $data;
  $output = json_encode($Reservation, JSON_NUMERIC_CHECK);
  //  $output = json_encode($data, JSON_NUMERIC_CHECK);
  echo $output;
}

function addReservation() {
  global $conn;

  $roomname = $_POST['roomname'];
  $username = $_POST['username'];
  $email = $_POST['email'];
  $professor = $_POST['professor'];
  $major = $_POST['major'];
  $date = $_POST['date'];
  $starttime = $_POST['starttime'];
  $endtime = $_POST['endtime'];
  $purpose = $_POST['purpose'];
  $phone = $_POST['phone'];
  $memo = $_POST['memo'];
  $permission = $_POST['permission'];

  //$user_sql = "SELECT * FROM User WHERE mail='". $email ."' AND name='". $username ."'";
  $user_sql = "SELECT * FROM User WHERE mail='". $email ."';";
  $user_result = mysqli_query($conn, $user_sql);

  $user_total_rows = mysqli_num_rows($user_result);

  if ($user_total_rows > 0 ){
    $user_row = mysqli_fetch_assoc($user_result);
    $userid = $user_row['id'];

    $room_sql = "SELECT * FROM LectureRoom WHERE room_name='". $roomname ."'";
    $room_result = mysqli_query($conn, $room_sql);

    $room_total_rows = mysqli_num_rows($room_result);

    if ($room_total_rows > 0 ){
      $room_row = mysqli_fetch_assoc($room_result);
      $roomid = $room_row['id'];

      $sql = "SELECT * FROM Reservation JOIN LectureRoom ON Reservation.lectureRoom_id = LectureRoom.id WHERE LectureRoom.room_name='".$roomname."' AND reservation_date='".$date."' AND (start_time<='".$starttime."' AND end_time>'".$starttime."' OR start_time<'".$endtime."' AND end_time>='".$endtime."') AND Reservation.permission != 2";

      $result = mysqli_query($conn, $sql);
      $total_rows = mysqli_num_rows($result);

	  // modified by jerry (190830)
	  $lecture_num = getLectureNum();
      if ($total_rows > 0){
        echo "same";
      } else {
        $sql = "INSERT INTO Reservation (user_id, lecture_num, lectureRoom_id, reservation_date, start_time, end_time, purpose, phone, professor, major, memo, permission, regdate) VALUES ('".$userid."','$lecture_num','$roomid','$date','$starttime','$endtime','$purpose','$phone','$professor','$major', '$memo', '$permission',now())";

        $result = mysqli_query($conn, $sql);

        if($result) {
          echo "saved";
        } else {
          echo "error";
        }
      }
    } else {
      echo "room_not_found";
    }
  } else {
    echo "user_not_found";
  }
}

function getValidDate() {
  global $conn;
  $room = $_POST['name'];
  $data = array();

  $sql = "SELECT * FROM LectureRoom WHERE room_name='".$room."'";
  $result = mysqli_query($conn, $sql);
  //  $total_rows = mysqli_num_rows($result);
  if ($result){
    //$i = 0;
    while($row = mysqli_fetch_assoc($result)) {
      //$index = $total_rows-$i;
      array_push($data, $row['available_start']);
      array_push($data, $row['available_end']);
      //$i++;
    }
  }
  $data = json_encode($data);
  echo $data;
}

//myreservation
function getMyReservation(){
	date_default_timezone_set('Asia/Seoul');
	global $conn;
	$mail = $_POST['email'];
	$sort = $_POST['sort'];

	$isCheck = array();
	$keyword ='';
	$page = 1;
	if(isset($_POST['isCheck'])) $isCheck = $_POST['isCheck']; //체크된 강의실 목록
	if(isset($_POST['keyword'])) $keyword = $_POST['keyword'];
	if(isset($_POST['page'])) $page = $_POST['page'];
	$numCheck = count($isCheck); // 체크된 강의실 갯수
	$view_article = 12; // 한 페이지에 보여 줄 데이터의 개수
	$start_data = ($page-1)*$view_article;

	$sql ="SELECT Reservation.id,lecture_num, permission,room_name,purpose,memo,name,mail,Reservation.regdate, reservation_date, start_time, end_time
	FROM User JOIN Reservation ON User.id = Reservation.User_id
	JOIN LectureRoom ON Reservation.LectureRoom_id = LectureRoom.id";
	$sql .= " WHERE mail='".$mail."'";

  if($numCheck==0){ //강의실 체크박스가 체크되지 않았으면 모든 리스트 출력

  } else { //특정 강의실을 선택했을 경우
    $i =0;
    $sql .=" AND";
    while($i <= $numCheck-1){ //특정 강의실들에 해당되는 모든 예약 출력,
      if($i == 0) $sql .= " (";
      $sql .= " room_name = '".$isCheck[$i]."'";
      $i++;
      if(!($i==$numCheck)){
        $sql .=" OR";
      } else{
        $sql .=")";
      }
    }
  }

  if($keyword) {
    $sql .= " AND purpose LIKE '%".$keyword."%'";
  }

  $sql .= " GROUP BY room_name, purpose, memo, name, start_time, end_time";

  if(!strcmp($sort,"request_day")){ //sort값에 따라 신청일 or 사용일 별로 정렬
    $sql .= " ORDER BY Reservation.regdate desc";
  }else if(!strcmp($sort,"use_day")){
    $sql .= " ORDER BY reservation_date desc";
  }

  $result = mysqli_query($conn, $sql);
  $total_rows = mysqli_num_rows($result);

  ////////////////////////////////////////
  if (isset($start_data)){
    //echo "<script> alert('start존재'); </script>";
    $sql .= " LIMIT {$start_data}, {$view_article}";
  }

  $result = mysqli_query($conn, $sql); // 중복으로 적힌것, 잘못 적은게 아님. 위에서는 총 row의 개수를 구하기 위해 한번 쿼리를 돌렸고,
  // 여기에서는 limit걸린것의 결과값이 필요해서 한번더 쿼리를 돌린 것임.
  //////////////////////////////////////////
  echo '
  <div class="container-table">
    <ul class="responsive-table">
      <li class="table-header">
        <div class="col col-1">No</div>
        <div class="col col-2">강의실</div>
        <div class="col col-3">승인여부</div>
        <div class="col col-4">사용날짜</div>
        <div class="col col-5">사용시간</div>
        <div class="col col-6">사용목적</div>
        <div class="col col-7">신청인</div>
        <div class="col col-8">신청일</div>
        <div class="col col-9">예약상태</div>
      </li>';

      if ($total_rows >0 ){
        $i = 0;		$k = 0;
        $dataNumber = $start_data + 1;

        while($row = mysqli_fetch_assoc($result)) {
          $title = $row['purpose'];
          $today = date("Y-m-d");
          $starttime = date("H:i:s",time());
          $index = $total_rows-$i;
          $useday_s = $row['reservation_date'];
          //사용시간-초안나오게, 신청일 날짜만
          $startTime = $row['start_time'];
          $startTimeVal = substr($startTime,0,5);
          $endTime = $row['end_time'];
          $endTimeVal = substr($endTime,0,5);
          $regdate = $row['regdate'];
          $regdateVal = substr($regdate,0,10);
		  $lecture = $row['lecture_num'];

          if($row['permission']==3){
            $k++;
            $useday_s = findFirst($title);
            $useday_e = findEnd($title);
          }else{
            $k=0;
            $useday_e = $useday_s;
          }
          if($row['permission']==0){
            echo '<li class="table-row" style="background-color:#F5F5DC;">';
          }else if($row['permission']==1){
            echo '<li class="table-row" style="background-color:#F0FFF0;">';
          }else if($row['permission']==2){
            echo '<li class="table-row" style="background-color:#FFE4E1;">';
          }else if($row['permission']==3){
            echo '<li class="table-row" style="background-color:#F0FFFF;">';
          }
          echo' <div class="col col-1" data-label="No">'.$dataNumber.'</div>
          <div class="col col-2" data-label="강의실">'.$row['room_name'].'</div>';
          if($row['permission']==0){
            echo '<div class="col col-3" data-label="승인여부">대기</div>';
          }else if($row['permission']==1){
            echo '<div class="col col-3" data-label="승인여부">승인</div>';
          }else if($row['permission']==2){
            echo '<div class="col col-3" data-label="승인여부">거절</div>';
          }else if($row['permission']==3){
            echo '<div class="col col-3" data-label="승인여부">수업</div>';
          }

          echo' <div class="col col-4" data-label="사용날짜">'.$useday_s.'~'.$useday_e.'</div>
          <div class="col col-5" data-label="사용시간">'.$startTimeVal.'~'.$endTimeVal.'</div>
          <div class="col col-6" data-label="사용목적">'.$title.'</div>
          <div class="col col-7" data-label="신청인">'.$row['name'].'</div>
          <div class="col col-8" data-label="신청일">'.$regdateVal.'</div>';

          if($row['permission']==0){ //대기중인 경우
            echo '<div class="col col-9" data-label="예약상태">
              <input type="button" value="취소" style="cursor:pointer;" name="cancle" onclick="cancle('.$row['id'].')" title="waiting">
            </div>';
          }else if($row['permission']==1){ // 승인된 경우
            echo '<div class="col col-9" data-label="예약상태">
              <i data-no="'.$row['id'].'" class="fa fa-check-circle" aria-hidden="true" style="color:green;" title="accept"> </i>
            </div>';
          }else if($row['permission']==2){ //거절된 경우
            echo '<div class="col col-9" data-label="예약상태">
              <i data-no="'.$row['id'].'" class="fa fa-times del" aria-hidden="true" style="color:red;" title="reject"> </i>
            </div>';
          } else if($row['permission']==3){ //관리자만 보임 수업
            echo '<div class="col col-9" data-label="예약상태">
				<input type="button" value="취소" style="cursor:pointer;" name="lectureCancle" onclick="lectureCancle('.$lecture.')" title="waiting">
            </div>';
          }
          echo '</li>';
          $i++;
          $dataNumber++;
        }
        echo '  </ul>
              </div>';

      /////////////////////////////////////////////////////////////////////////////
      // paging 부분 수정 2019.08. 02 권현우

      $total_page = ceil($total_rows/$view_article);
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
      $next_group = $page + 1;
      if ($next_group > $total_page) $next_group = $total_page;


      // --- 실제로 페이지가 보여지게 구현한 부분 ---
      // 제일 처음 페이지로 이동
      if ($page != 1) {
        echo "<button type=\"button\" value=\"1\" onclick=\"listbyroom(1)\">첫 페이지</button>";
      }
      else echo "<button type=\"button\" disabled>첫 페이지</button>";

      // 아래쪽에 실제로 보이는 page인 1, 2, 3, ... 출력해주기
      for ($i=$start_page; $i<$end_page; $i++){
        if ($i>$total_page)break;
        if ($i==$page) echo "<button type=\"button\" disabled> {$i} </button>";
        else echo "<button type=\"button\" value=\"{$i}\" onclick=\"listbyroom(".$i.")\">{$i}</button>";
      }

      // 맨 뒤 페이지로 이동
      if ($page != $total_page) echo "<button type=\"button\" value=\"{$total_page}\" onclick=\"listbyroom(this.value)\">끝 페이지</button>";
      else echo "<button type=\"button\" disabled>끝 페이지</button>";
      //////////////////////////////////////////////////////////////////////
    }
  }

function roomInfoCheck(){ //저장된 강의실 목록 불러와 체크박스 생성
  global $conn;
  $sql = "SELECT * FROM LectureRoom ";
  $sql .= " ORDER BY LectureRoom.id asc";
  $result = mysqli_query($conn, $sql);
  $total_rows = mysqli_num_rows($result);


  echo '<div class="container">';
  echo '<ul class="ks-cboxtags" >';
  echo '<h7><i class="fa fa-school"></i>&nbsp;강의실 선택</h7></br>';
  if ($total_rows >0 ){
    $i = 0;
    while($row = mysqli_fetch_assoc($result)) {
      $index = $total_rows-$i;
      echo '
      <li>
      <input type="checkbox"
      id="'.$row['room_name'].'"
      class="roomSelect"
      value="'.$row['room_name'].'"
      onclick="listbyroom()">
      <label for="'.$row['room_name'].'">'.$row['room_name'].'</label>
      </li>';

      $i++;
    }
    echo ' </ul>
    </div>';
  }
}

function cancleReservation(){
  global $conn;
	$id = $_POST['id'];
  $lecture = $_POST['lecture'];
  $sql = "DELETE FROM Reservation ";
	if(isset($id)){
		$sql.= " WHERE Reservation.id='".$id."'";
	}
  else if(isset($lecture)){
		$sql.= " WHERE Reservation.lecture_num='".$lecture."'";
	}
	$result = mysqli_query($conn, $sql);
  echo $sql;
}

function findFirst($title){
  global $conn;
  $sql = "SELECT reservation_date FROM Reservation WHERE Reservation.purpose='".$title."'";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
  $min = $row['reservation_date'];
  while($row = mysqli_fetch_assoc($result)){
    if($min>$row['reservation_date']){
      $min=$row['reservation_date'];
    }
  }
  return $min;
}

function findEnd($title){
  global $conn;
  $sql = "SELECT reservation_date FROM Reservation WHERE Reservation.purpose='".$title."'";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
  $max = $row['reservation_date'];
  while($row = mysqli_fetch_assoc($result)){
    if($max<$row['reservation_date']){
      $max=$row['reservation_date'];
    }
  }
  return $max;
}

//roominfo

function deleteRoom(){
  global $conn;
  $index = $_POST['index'];
  $msg = "error";

  if($index){
    $sql = "DELETE FROM LectureRoom WHERE LectureRoom.id='".$index."' ";
    $result = mysqli_query($conn, $sql);
  }
}

function createRoom(){
  global $conn;
  $index = $_POST['index'];
  $roomName = $_POST['roomName'];
  $capacity = $_POST['capacity'];
  $projector = $_POST['projector'];
  $startDate = $_POST['startDate'];
  $endDate = $_POST['endDate'];
  $description = $_POST['description'];

  if($index)
    $sql = "UPDATE LectureRoom SET room_name='".$roomName."', capacity='".$capacity."', projector='".$projector."', available_start='".$startDate."', available_end='".$endDate."', description='".$description."' where LectureRoom.id='".$index."' ";
  else
    $sql = "INSERT INTO LectureRoom (room_name, capacity, projector, available_start, available_end, description) VALUES ('$roomName', '$capacity', '$projector', '$startDate', '$endDate', '$description')";

  $result = mysqli_query($conn, $sql);

  if($result) echo "OK1";
  else echo "ERROR1";
}

function readRoomInfo(){
  global $conn;
  $index = $_POST['index'];
  $msg = "error";

  if($index){
    $sql = "SELECT * FROM LectureRoom WHERE LectureRoom.id='". $index ."'";
    $result = mysqli_query($conn, $sql);

    if($result){
      $row = mysqli_fetch_assoc($result);
      $msg = $row['id']."|".$row['room_name']."|".$row['capacity']."|".$row['projector']."|".$row['available_start']."|".$row['available_end']."|".$row['description'];
    }
  }

  echo $msg;
}

function readRoomList(){
  global $conn;
  $sql = "SELECT * FROM LectureRoom ";
  $sql .= " ORDER BY LectureRoom.id asc";
  $result = mysqli_query($conn, $sql);
  $total_rows = mysqli_num_rows($result);

  echo '
    <div class="container-table" id="roomlist">
    <ul class="responsive-table">
      <li class="table-header">
        <div class="col col-1-roominfo">No</div>
        <div class="col col-2-roominfo">강의실</div>
        <div class="col col-3-roominfo">수용인원</div>
        <div class="col col-4-roominfo">프로젝터</div>
        <div class="col col-5-roominfo">예약가능시작일</div>
        <div class="col col-6-roominfo">예약가능종료일</div>
        <div class="col col-7-description">추가설명</div>
        <div class="col col-8"></div>
      </li>
      ';

  if ($total_rows >0 ){
    $i = $total_rows;

    while($row = mysqli_fetch_assoc($result)) {
      $index = $total_rows-$i + 1;
      echo '
      <li class="table-row">
      <div class="col col-1-roominfo" data-label="No">'.$index.'</div>
      <div class="col col-2-roominfo" data-label="강의실">'.$row['room_name'].'</div>
      <div class="col col-3-roominfo" data-label="수용인원">'.$row['capacity'].'</div>
      ';
      if ($row['projector'] == 0) {
        echo '<div class="col col-4-roominfo" data-label="프로젝터">X</div>';
      } else {
        echo '<div class="col col-4-roominfo" data-label="프로젝터">O</div>';
      }
      echo '
      <div class="col col-5-roominfo" data-label="예약가능시작일">'.$row['available_start'].'</div>
      <div class="col col-6-roominfo" data-label="예약가능종료일">'.$row['available_end'].'</div>
      <div class="col col-7-description" data-label="추가설명">'.$row['description'].'</div>
      <div class="col col-8" data-label="수정/삭제"><i class="fa fa-pencil-square" aria-hidden="true" onclick="readRoomInfo('.$row['id'].')" style="color:blue;cursor:pointer" title="modify"></i></button>&nbsp;<i data-no="'.$row['id'].'" class="fa fa-times del" aria-hidden="true" style="color:red;cursor:pointer" onclick="delRoom('.$row['id'].')" title="delete"> </i></div>
      </li>
      ';
      $i--;
    }
    echo'      </ul>
          </div>';
  }
}

//test
function readReadyReservation(){
  date_default_timezone_set('Asia/Seoul');
  global $conn;

  $prev_sql = '';
  if(isset($_POST['sql']))$prev_sql = $_POST['sql'] ;
  $start = $_POST['start'];
  $end = $_POST['end'];
  $list = $_POST['list'];
  $sort = $_POST['sort'];
  $isCheck = array();
  if(isset($_POST['isCheck']))$isCheck = $_POST['isCheck'] ; //체크된 강의실 목록
  $numCheck = count($isCheck); // 체크된 강의실 갯수
  $keyword = $_POST['keyword'];
  $option = $_POST['option'];

  $page = 1;
  if(isset($_POST['page']))$page = $_POST['page'] ;

  $view_article = 12; // 한 페이지에 보여 줄 데이터의 개수
  $start_data = ($page-1)*$view_article; // 데이터를 가져올때, 몇번째 데이터부터 가져올지 (페이지가 2고 한번에 12개를 보여주면, 12번 데이터(13번째)부터 12개를 가져올것) 아래에 sql문에 limit조건을 더해줄때 사용됨.
  //////////////////////////////////////////////////////////////////////////////////////////////

  $sql ="SELECT Reservation.id, permission,room_name,purpose,memo,name,mail,Reservation.regdate, reservation_date, start_time, end_time, User.name, phone, major, professor
         FROM User JOIN Reservation ON User.id = Reservation.User_id
                  JOIN LectureRoom ON Reservation.LectureRoom_id = LectureRoom.id";

  if(!strcmp($list,"waiting")){
    $sql .= " WHERE permission=0";
  }else if(!strcmp($list,"approved")){
    $sql .= " WHERE permission=1";
  }else if(!strcmp($list,"rejected")){
    $sql .= " WHERE permission=2";
  }

  if($numCheck==0){ //강의실 체크박스가 체크되지 않았으면 모든 리스트 출력

  } else { //특정 강의실을 선택했을 경우
    $i =0;
    $sql .=" AND";
    while($i <= $numCheck-1){ //특정 강의실들에 해당되는 모든 예약 출력,
      if($i == 0) $sql .= " (";
      $sql .= " room_name = '".$isCheck[$i]."'";
      $i++;
      if(!($i==$numCheck)){
        $sql .=" OR";
      } else{
        $sql .=")";
      }
    }
  }

  if($keyword && $option){
    $sql .=" AND ".$option." LIKE '%".$keyword."%'";
  }

  // if(isset($start) && isset($end)){
  if ($start && $end){
    $sql .=" AND reservation_date BETWEEN '".$start."' AND '".$end."'";
  }

  if(!strcmp($sort,"request_day")){ //sort값에 따라 신청일 or 사용일 별로 정렬
    $sql .= " ORDER BY Reservation.regdate desc";
  }else if(!strcmp($sort,"use_day")){
    $sql .= " ORDER BY reservation_date asc, start_time asc";
  }
  $result = mysqli_query($conn, $sql);
  $total_rows = mysqli_num_rows($result);
  ////////////////////////////////////////////////////
  if (isset($start_data)){
    //echo "<script> alert('start존재'); </script>";
    $sql .= " LIMIT {$start_data}, {$view_article}";
  }
  //echo "<script> alert('{$sql}'); </script>";
  ////////////////////////////////////////////////////
  $result = mysqli_query($conn, $sql); // 중복으로 적힌것, 잘못 적은게 아님. 위에서는 총 row의 개수를 구하기 위해 한번 쿼리를 돌렸고,
  // 여기에서는 limit걸린것의 결과값이 필요해서 한번더 쿼리를 돌린 것임.

  echo '
  <div class="container-table">
    <ul class="responsive-table">
      <li class="table-header">
  ';

  if(!strcmp($list,"waiting")){
    echo '<div class="col col-0"><input type="checkbox" id="checkall" onclick="chk_all()"></div>';
  }
  echo '

    <div class="col col-1-requestlist">No</div>
    <div class="col col-2-requestlist">강의실</div>
    <div class="col col-3-requestlist">사용날짜</div>
    <div class="col col-4-requestlist">사용시간</div>
    <div class="col col-5-requestlist">사용목적</div>
    <div class="col col-6-requestlist">메모</div>
    <div class="col col-7-requestlist">신청인</div>
    <div class="col col-8-requestlist">연락처</div>
    <div class="col col-9-requestlist">학부</div>
    <div class="col col-10-requestlist">관련교수</div>
    <div class="col col-11-requestlist">신청일</div>
    <div class="col col-12-requestlist">승인여부</div>';
    if(strcmp($list,"all")){
      echo  '<div class="col col-13-requestlist">예약상태</div>';
    }
    echo  '</li>';

  /*if(!strcmp($list,"all")){
    echo '
          <div class="col col-1">No</div>
          <div class="col col-2">강의실</div>
          <div class="col col-6">사용날짜</div>
          <div class="col col-7">사용시간</div>
          <div class="col col-3">사용목적</div>
          <div class="col col-4">메모</div>
          <div class="col col-5">신청인</div>
          <div class="col col-8">신청일</div>
        </li>';
  } else {
    echo '
          <div class="col col-1">No</div>
          <div class="col col-2">강의실</div>
          <div class="col col-6">사용날짜</div>
          <div class="col col-7">사용시간</div>
          <div class="col col-3">사용목적</div>
          <div class="col col-4">메모</div>
          <div class="col col-5">신청인</div>
          <div class="col col-8">신청일</div>
          <div class="col col-9">예약상태</div>
        </li>';
  }*/


    if ($total_rows >0 ){
			$i = 0;
      $dataNumber = $start_data + 1;

			while($row = mysqli_fetch_assoc($result)) {
				$index = $total_rows-$i;
        //사용시간-초안나오게, 신청일 날짜만
        $startTime = $row['start_time'];
        $startTimeVal = substr($startTime,0,5);
        $endTime = $row['end_time'];
        $endTimeVal = substr($endTime,0,5);
        $regdate = $row['regdate'];
        $regdateVal = substr($regdate,0,10);
        //$row['start_time']->$startTimeVal
        //$row['end_time']->$endTimeVal
        //$row['regdate']->$regdateVal
        if(!strcmp($list,"waiting")){ //대기중인 경우
          echo '<li class="table-row" style="background-color:#F5F5DC;">';
        }else if(!strcmp($list,"approved")){ // 승인된 경우
          echo '<li class="table-row" style="background-color:#F0FFF0;">';
        }else if(!strcmp($list,"rejected")){ //거절된 경우
          echo '<li class="table-row" style="background-color:#FFE4E1;">';
        } else {
          if($row['permission']==0){
            echo '<li class="table-row" style="background-color:#F5F5DC;">';
          }else if($row['permission']==1){
            echo '<li class="table-row" style="background-color:#F0FFF0;">';
          }else if($row['permission']==2){
            echo '<li class="table-row" style="background-color:#FFE4E1;">';
          }else if($row['permission']==3){
            echo '<li class="table-row" style="background-color:#F0FFFF;">';
          }
        }
          if(!strcmp($list,"waiting")){
            echo '<div class="col col-0"><input type="checkbox" name="check" value="'.$row['id'].'" onclick="undo_chk()"></div>';
          }
          echo '
          <div class="col col-1-requestlist" data-label="No">'.$dataNumber.'</div>
          <div class="col col-2-requestlist" data-label="강의실">'.$row['room_name'].'</div>
          <div class="col col-3-requestlist" data-label="사용날짜">'.$row['reservation_date'].'</div>
          <div class="col col-4-requestlist" data-label="사용시간">'.$startTimeVal.'~'.$endTimeVal.'</div>
          <div class="col col-5-requestlist" data-label="사용목적">'.$row['purpose'].'</div>
          <div class="col col-6-requestlist" data-label="메모"><i class="fa fa-sticky-note" title="'.$row['memo'].'"></i></div>
          <div class="col col-7-requestlist" data-label="신청인">'.$row['name'].'</div>
          <div class="col col-8-requestlist" data-label="연락처"><i class="fa fa-phone" title="'.$row['phone'].'"></i></div>
          <div class="col col-9-requestlist" data-label="학부">'.$row['major'].'</div>
          <div class="col col-10-requestlist" data-label="관련교수">'.$row['professor'].'</div>
          <div class="col col-11-requestlist" data-label="신청일">'.$regdateVal.'</div>
          ';
          if($row['permission']==0){
            echo '<div class="col col-12-requestlist" data-label="승인여부">대기</div>';
          }else if($row['permission']==1){
            echo '<div class="col col-12-requestlist" data-label="승인여부">승인</div>';
          }else if($row['permission']==2){
            echo '<div class="col col-12-requestlist" data-label="승인여부">거절</div>';
          }else if($row['permission']==3){
            echo '<div class="col col-12-requestlist" data-label="승인여부">수업</div>';
          }

            if(!strcmp($list,"waiting")){ //대기중인 경우
              echo '<div class="col col-13-requestlist" data-label="예약상태">
              <input type="button" value="승인" onclick="approval('.$row['id'].')"><input type="button" value="거절" name="reject" onclick="reject('.$row['id'].')">
              </div>';
            }else if(!strcmp($list,"approved")){ // 승인된 경우
              echo '<div class="col col-13-requestlist" data-label="예약상태">
              <input type="button" value="예약취소" onclick="reject('.$row['id'].')">
              </div>';
            }else if(!strcmp($list,"rejected")){ //거절된 경우
              echo '<div class="col col-13-requestlist" data-label="예약상태">
              <i data-no="'.$row['id'].'" class="fa fa-times del" aria-hidden="true" style="color:red;" title="reject"> </i>
              </div>';
            }
				  echo '</li>';
				$i++;
        $dataNumber++;
			}

    /*  if(!strcmp($list,"waiting")){
        echo "<td></td>";
      }
      echo "
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        ";
        */
        /////////////////////////////////////////////////////////////////////////
      //  echo "<td>";
        if(!strcmp($list,"waiting")){ //대기중인 경우
          echo "<input type='button' id='check_btn' value='체크전체승인' onclick='chk_approval()'><br><br>";
        }else if(!strcmp($list,"approved")){ // 승인된 경우
          echo "<input type='button' id='save_excel_btn' value='엑셀로저장' onclick='save_excel()'><br><br>";
        }else if(!strcmp($list,"rejected")){ //거절된 경우
          echo "<input type='button' id='save_excel_btn' value='엑셀로저장' onclick='reject_excel()'><br><br>";
        }
      //  echo "</td>";

        // 테이블 제일 아래에 한 행 추가, 안에 페이징 삽입
      //  echo "<tr><td>";
      /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $total_page = ceil($total_rows/$view_article); /////////////////////////// 올림 함수인데, 이상하게 +1을 안해주면 작동이 안될때가 있음. 확인 요망

        // 페이지 인덱스, 시작, 종료 범위 로직
        if($page%10){
        	$start_page = $page - $page%10 + 1; // 시작페이지. 예를들어 현재 페이지가 12이면, 12 - 2 + 1로 11가 시작 페이지가 된다. 현재 15페이지라면, 15-5+1로 여전히11이 시작 페이지가 된다.
        } else { // 만약 현재 페이지가 10으로 나누어 떨어진다면, 예를 들어 10페이지라면
        	$start_page = $page - 9; // 시작 페이지는 10 - 9 인 1이 된다.
        }
        $end_page = $start_page+10; // 총 페이지는 10개를 보여줄 것이므로, 끝 페이지는 시작 페이지 + 10이 된다.

        // 그룹 이동. 현재 에서 바로 다음 페이지로, 바로 앞 페이지로.
        // 이전 그룹으로 이동 로직
        $prev_group = $page - 1;
        if ($prev_group < 1) $prev_group = 1;

        // 다음 그룹으로 이동 로직
        $next_group = $page+1;
        if ($next_group > $total_page) $next_group = $total_page;

        // 페이징 구현한부분
        // 제일 처음 페이지로 이동
        if ($page != 1) {
          echo "<button type=\"button\" value=\"1\" onclick=\"listbyroom(1)\">첫 페이지</button>";
        }
        else echo "<button type=\"button\" disabled>첫 페이지</button>";

        // 이전 그룹 이동 보이게 구현한부분
        //if($page!= 1) echo "<button type=\"button\" value=\"{$prev_group}\" onclick=\"readReservation(this.value)\">prev</button>";

        // 아래쪽에 실제로 보이는 page인 1, 2, 3, ... 출력해주기
        for ($i=$start_page; $i<$end_page; $i++){
          if ($i>$total_page)break;
          if ($i==$page) echo "<button type=\"button\" disabled> {$i} </button>";
          else echo "<button type=\"button\" value=\"{$i}\" onclick=\"listbyroom(".$i.")\">{$i}</button>";
        }

        // 다음 그룹 이동 보이게 구현한부분
        //if ($page != ($end_page)) echo "<button type=\"button\" value=\"{$next_group}\" onclick=\"readReservation(this.value)\">next</button>";

        // 맨 뒤 페이지로 이동
        if ($page != $total_page) echo "<button type=\"button\" value=\"{$total_page}\" onclick=\"listbyroom(this.value)\">끝 페이지</button>";
        else echo "<button type=\"button\" disabled>끝 페이지</button>";
      //  echo "</td></tr>";
		}
    /////////////////////////////////////////////////////////////////////////
}

function acceptRequest(){ //승인 시켜주는 기능
  global $conn;
  $index = $_POST['id'];

  $sql = "SELECT Reservation.id, room_name,name,mail,reservation_date, start_time, end_time
          FROM User JOIN Reservation ON User.id = Reservation.User_id
                    JOIN LectureRoom ON Reservation.LectureRoom_id = LectureRoom.id
          WHERE Reservation.id='".$index."'";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);

  $sql = "UPDATE Reservation SET permission=1 WHERE Reservation.id='".$index."' ";
  $result = mysqli_query($conn, $sql);
  //사용시간-초안나오게
  $startTime = $row['start_time'];
  $startTimeVal = substr($startTime,0,5);
  $endTime = $row['end_time'];
  $endTimeVal = substr($endTime,0,5);
  //$row['start_time']->$startTimeVal
  //$row['end_time']->$endTimeVal
  $to      = $row['mail'];
  $message = $row['name']."님이 ".$row['reservation_date']." ".$startTimeVal."~".$endTimeVal." 에 예약하신 ".$row['room_name']." 강의실 예약이 승인되었습니다.";

  echo $to.'/'.$message;
}

function rejectRequest(){ //거절하는 기능, 사유를 알리기 위해 이메일을 보내는 창 띄워줌
  global $conn;
  $index = $_POST['id'];
  //	if($autoid)$index = $autoid;
  $sql = "SELECT Reservation.id, room_name,name,mail,reservation_date, start_time, end_time
          FROM User JOIN Reservation ON User.id = Reservation.User_id
                    JOIN LectureRoom ON Reservation.LectureRoom_id = LectureRoom.id
          WHERE Reservation.id='".$index."'";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);

  //거절 -> permission=2
  $sql = "UPDATE Reservation SET permission=2 WHERE Reservation.id='".$index."' ";
  //$sql = "DELETE FROM Reservation WHERE Reservation.id='".$index."'";
  $result = mysqli_query($conn, $sql);
  //사용시간-초안나오게, 신청일 날짜만
  $startTime = $row['start_time'];
  $startTimeVal = substr($startTime,0,5);
  $endTime = $row['end_time'];
  $endTimeVal = substr($endTime,0,5);
  //$row['start_time']->$startTimeVal
  //$row['end_time']->$endTimeVal
  $to      = $row['mail'];
  $message = $row['name']."님이 ".$row['reservation_date']." ".$startTimeVal."~".$endTimeVal." 에 예약하신 ".$row['room_name']." 강의실 예약이 거절되었습니다.";

  echo $to.'/'.$message;

}

//lecture
function readRoomSelect() { //저장된 강의실 목록 불러와 체크박스 생성
  global $conn;
  $sql = "SELECT * FROM LectureRoom ";
  $sql .= " ORDER BY room_name asc";
  $result = mysqli_query($conn, $sql);
  $total_rows = mysqli_num_rows($result);
  if ($total_rows > 0){
    $i = 0;
    while($row = mysqli_fetch_assoc($result)) {
      $index = $total_rows-$i;
      echo "<option id=".$i." value=\"".$row['room_name']."\">".$row['room_name']."</option>";
      $i++;
    }
  }
}

function checkReservation() {
  global $conn;

  $room = $_POST['room_name'];
  $startDate = $_POST['start'];
  $endDate = $_POST['end'];
  $dates = json_decode($_POST['dates']);
  $time = json_decode($_POST['time']);

  $errorDate = array();
  $valid = false;

  $sql = "SELECT * FROM Reservation JOIN LectureRoom ON Reservation.lectureRoom_id = LectureRoom.id";
  $sql .= " WHERE LectureRoom.room_name = '" . $room . "'";
  $sql .= " AND reservation_date BETWEEN '" . $startDate . "' AND '" . $endDate . "'";
  $sql .= " AND permission != 2";
  $sql .= " ORDER BY reservation_date, start_time ASC";
  $result = mysqli_query($conn, $sql);
  $total_rows = mysqli_num_rows($result);
  if ($total_rows > 0){
    while($row = mysqli_fetch_assoc($result)) {
      for ($i = 0; $i < count($dates); $i++) {
        if ($row['reservation_date'] == $dates[$i]) {
          if ($time[0] <= $row['start_time'] && $time[1] > $row['start_time']) {
            array_push($errorDate, $row);
            /*
            array_push($errorDate, $row['reservation_date']);
            array_push($errorDate, $row['start_time']);
            array_push($errorDate, $row['end_time']);
            array_push($errorDate, $row['purpose']);
            array_push($errorDate, $row['lectureRoom_id']);
            */
          } else if ($time[0] >= $row['start_time'] && $time[0] < $row['end_time']) {
            array_push($errorDate, $row);
            /*
            array_push($errorDate, $row['reservation_date']);
            array_push($errorDate, $row['start_time']);
            array_push($errorDate, $row['end_time']);
            array_push($errorDate, $row['purpose']);
            array_push($errorDate, $row['lectureRoom_id']);
            */
          }
        }
      }
    }
    $errorDate = json_encode($errorDate);
    echo $errorDate;
  } else {
    echo true;
  }
}

function addLecture() {
  global $conn;

  $roomname = $_POST['roomname'];
  $username = $_POST['username'];
  $email = $_POST['email'];
  $professor = $_POST['professor'];
  $major = $_POST['major'];
  $date = $_POST['date'];
  $starttime = $_POST['starttime'];
  $endtime = $_POST['endtime'];
  $purpose = $_POST['purpose'];
  $phone = $_POST['phone'];
  $memo = $_POST['memo'];
	///////////////////////////////////////
	$lecture_num = $_POST['lecture_num'];
	///////////////////////////////////////

  //$user_sql = "SELECT * FROM User WHERE mail='". $email ."' AND name='". $username ."'";
  $user_sql = "SELECT * FROM User WHERE mail='". $email ."';";
  $user_result = mysqli_query($conn, $user_sql);
  $user_total_rows = mysqli_num_rows($user_result);

  if ($user_total_rows > 0 ){
    $user_row = mysqli_fetch_assoc($user_result);
    $userid = $user_row['id'];

    $room_sql = "SELECT * FROM LectureRoom WHERE room_name='". $roomname ."'";
    $room_result = mysqli_query($conn, $room_sql);

    $room_total_rows = mysqli_num_rows($room_result);

    if ($room_total_rows > 0 ){
      $room_row = mysqli_fetch_assoc($room_result);
      $roomid = $room_row['id'];

      $sql = "SELECT * FROM Reservation JOIN LectureRoom ON Reservation.lectureRoom_id = LectureRoom.id WHERE LectureRoom.room_name='".$roomname."' AND reservation_date='".$date."' AND (start_time<='".$starttime."' AND end_time>'".$starttime."' OR start_time<'".$endtime."' AND end_time>='".$endtime."') AND Reservation.permission != 2";

      $result = mysqli_query($conn, $sql);
      $total_rows = mysqli_num_rows($result);

/////////////////////////////////////////////
	  //$lecture_num = getLectureNum();
		/////////////////////////////////////////
      if ($total_rows > 0){
        echo "same";
      } else {
		  //modified by jerry ( 190830)

        $sql = "INSERT INTO Reservation (user_id, lectureRoom_id, lecture_num, reservation_date, start_time, end_time, purpose, phone, professor, major, memo, permission, regdate) VALUES ('$userid','$roomid','$lecture_num','$date','$starttime','$endtime','$purpose','$phone','$professor','$major','$memo','3',now())";

        $result = mysqli_query($conn, $sql);

        if($result) {
          echo "saved";
        } else {
          echo "error";
        }
      }
    } else {
      echo "room_not_found";
    }
  } else {
    echo "user_not_found";
  }
}

function deleteReservation() {
  global $conn;
  $room = $_POST['roomId'];
  $date = $_POST['date'];
  $startTime = $_POST['startTime'];
  $endTime = $_POST['endTime'];
  $msg = "error";

  $sql = "SELECT * FROM User JOIN Reservation ON User.id = Reservation.User_id
           JOIN LectureRoom ON Reservation.LectureRoom_id = LectureRoom.id WHERE LectureRoom.id = '".$room."' AND start_time = '".$startTime."' AND end_time = '".$endTime."' AND reservation_date ='".$date."' AND NOT permission=2 ORDER BY Reservation.regdate DESC";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);

  $startTime = $row['start_time'];
  $startTimeVal = substr($startTime,0,5);
  $endTime = $row['end_time'];
  $endTimeVal = substr($endTime,0,5);

  if($room){
    $sql = "UPDATE Reservation SET permission = 2 WHERE Reservation.lectureRoom_id='".$room."' AND reservation_date='".$date."' AND start_time='".$startTime."' AND end_time='".$endTime."'";
    $result = mysqli_query($conn, $sql);
  }
  $to      = $row['mail'];
  $message = $row['name']."님이 ".$row['reservation_date']." ".$startTimeVal."~".$endTimeVal." 에 예약하신 ".$row['room_name']." 강의실 예약이 거절되었습니다.";
  echo $to.'/'.$message;
}

//manager

function deleteManager(){
  global $conn;
  $index = $_POST['index'];
  $msg = "error";
  if($index){
    $sql = "DELETE FROM User WHERE User.id='".$index."' ";
    $result = mysqli_query($conn, $sql);
  }
}

function createManager(){
  global $conn;
  $index = $_POST['index'];
  $userName = $_POST['userName'];
  $email = $_POST['email'];
  $isAdmin = $_POST['isAdmin'];
  if($index)
    $sql = "UPDATE User SET name='".$userName."', mail='".$email."', isAdmin='".$isAdmin."' where User.id='".$index."' ";
  else
    $sql = "INSERT INTO User (name, mail, isAdmin, regdate) VALUES ('$userName', '$email', '$isAdmin', now())";
  $result = mysqli_query($conn, $sql);
  if($result) echo "OK1";
  else echo "ERROR1";
}

function readManagerInfo(){
  global $conn;
  $index = $_POST['index'];
  $msg = "error";
  if($index){
    $sql = "SELECT * FROM User WHERE User.id='". $index ."'";

    $result = mysqli_query($conn, $sql);
    if($result){
      $row = mysqli_fetch_assoc($result);
      $msg = $row['id']."|".$row['name']."|".$row['mail']."|".$row['isAdmin'];
    }
  }
  echo $msg;
}

function readManagerList(){
  global $conn;
  $sql = "SELECT * FROM User";
  $sql .= " ORDER BY User.id asc";

  $result = mysqli_query($conn, $sql);
  $total_rows = mysqli_num_rows($result);

  if (isset($_SESSION['start_manager']) && isset($_SESSION['view_article_manager'])){
        $sql .= " LIMIT ". $_SESSION['start_manager'] . " , " . $_SESSION['view_article_manager'];
  }

  $result = mysqli_query($conn, $sql);

  // 쿼리로 가져온 데이터의 개수를 managerinfo.php에 넘겨주려고 세션 사용
  $_SESSION['manager_num'] = $total_rows;
  $_SESSION['sql'] = $sql;
  echo "
    <tr>
    <th>No</th>
    <th>이름</th>
    <th>Email</th>
    <th>권한</th>
    <th></th>
    </tr>";
  if ($total_rows >0 ){
    $i = $total_rows;
    $index = $_SESSION['start_manager'] + 1;
    while($row = mysqli_fetch_assoc($result)) {
      //$index = $total_rows-$i + 1;
      echo "
        <tr>
        <td width='10%'>".$index."</td>
        <td width='40%'>".$row['name']."</td>
        <td width='35%'>".$row['mail']."</td>";
      if ($row['isAdmin'] == 0) {
        echo "<td width='10%'>X</td>";
      } else {
        echo "<td width='10%'>O</td>";
      }
        //<td>".$row['projector']."</td>
      echo "<td><i class='fa fa-pencil-square w3-xlarge' aria-hidden='true' onclick='readManagerInfo(".$row['id'].")' style='color:blue;cursor:pointer' title='modify'></i></button>&nbsp;<i data-no='".$row['id']."' class='fa fa-times del w3-xlarge' aria-hidden='true' style='color:red;cursor:pointer; margin-left: 0.7rem;' onclick='delManager(".$row['id'].")' title='delete'> </i></td>
        </tr>";
      $i--;
      $index++;
    }
  }
}
?>
