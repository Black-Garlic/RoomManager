<?php
  header( "Content-type: application/vnd.ms-excel; charset=utf-8");
  header( "Content-Disposition: attachment; filename = 승인목록.xls" );
  header( "Content-Description: PHP4 Generated Data" );
  include "./inc/config.php";
  global $conn;

  $sql = "SELECT Reservation.id, permission, room_name,purpose,memo,name,mail,Reservation.regdate, reservation_date, start_time, end_time
          FROM User JOIN Reservation ON User.id = Reservation.User_id
                    JOIN LectureRoom ON Reservation.LectureRoom_id = LectureRoom.id
          WHERE permission = 1";
  $result = mysqli_query($conn, $sql);
  $num=1;
  $EXCEL_STR = "  <table border='1'>
                  <tr>
                    <td>No</td>
                    <td>강의실</td>
                    <td>사용목적</td>
                    <td>메모</td>
                    <td>신청인</td>
                    <td>사용날짜</td>
                    <td>사용시간</td>
                    <td>신청일</td>
                  </tr>";

  while($row = mysqli_fetch_assoc($result)) {
    iconv('UTF-8','EUC-KR',$row['room_name']);
    iconv('UTF-8','EUC-KR',$row['purpose']);
    iconv('UTF-8','EUC-KR',$row['memo']);
    iconv('UTF-8','EUC-KR',$row['name']);
    $EXCEL_STR .= "	<tr>
                      <td>".$num."</td>
                      <td>".$row['room_name']."</td>
                      <td>".$row['purpose']."</td>
                      <td>".$row['memo']."</td>
                      <td>".$row['name']."</td>
                      <td>".$row['reservation_date']."</td>
                      <td>".$row['start_time']."~".$row['end_time']."</td>
                      <td>".$row['regdate']."</td>
                    </tr>";
    $num++;
    }
    $EXCEL_STR .= "</table>";
    echo "<meta http-equiv='Content-Type' content='application/vnd.ms-excel; charset=euc-kr'> ";
    echo $EXCEL_STR;

 ?>
