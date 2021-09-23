<?php
include "./inc/config.php";

$excel_file_name = encoding_check("complete_reservation");

  header( "Content-type: application/vnd.ms-excel; charset=euc-kr");
  header( "Content-Disposition: attachment; filename = " .$excel_file_name. ".xls" );
  header( "Content-Description: PHP4 Generated Data" );
  print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel;charset=euc-kr\">");
  echo "<meta http-equiv='Content-Type' content='application/vnd.ms-excel; charset=euc-kr'> ";
  global $conn;

  $sql = "SELECT Reservation.id, permission, room_name,purpose,memo,name,mail,Reservation.regdate, reservation_date, start_time, end_time
          FROM User JOIN Reservation ON User.id = Reservation.User_id
                    JOIN LectureRoom ON Reservation.LectureRoom_id = LectureRoom.id
          WHERE permission = 1";
  $result = mysqli_query($conn, $sql);
  $num=1;

  $v5 = encoding_check("강의실");
  $v6 = encoding_check("사용목적");
  $v7 = encoding_check("메모");
  $v8 = encoding_check("신청인");
  $v9 = encoding_check("사용날짜");
  $v10 = encoding_check("사용시간");
  $v11 = encoding_check("신청일");

  $EXCEL_STR = "  <table border='1'>
                  <tr>
                    <td>No</td>
                    <td>".$v5."</td>
                    <td>".$v6."</td>
                    <td>".$v7."</td>
                    <td>".$v8."</td>
                    <td>".$v9."</td>
                    <td>".$v10."</td>
                    <td>".$v11."</td>
                  </tr>";

                  // $EXCEL_STR = "  <table border='1'>
                  //                 <tr>
                  //                   <td>No</td>
                  //                   <td>강의실</td>
                  //                   <td>사용목적</td>
                  //                   <td>메모</td>
                  //                   <td>신청인</td>
                  //                   <td>사용날짜</td>
                  //                   <td>사용시간</td>
                  //                   <td>신청일</td>
                  //                 </tr>";

  while($row = mysqli_fetch_assoc($result)) {
    // iconv('UTF-8','EUC-KR',$row['room_name']);
    // iconv('UTF-8','EUC-KR',$row['purpose']);
    // iconv('UTF-8','EUC-KR',$row['memo']);
    // iconv('UTF-8','EUC-KR',$row['name']);
    $v1 = encoding_check($row['room_name']);
    $v2 = encoding_check($row['memo']);
    $v3 = encoding_check($row['purpose']);
    $v4 = encoding_check($row['name']);
    $EXCEL_STR .= "	<tr>
                      <td>".$num."</td>
                      <td>".$v1."</td>
                      <td>".$v3."</td>
                      <td>".$v2."</td>
                      <td>".$v4."</td>
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

 <?php
function encoding_check($str){

  $encode = array('ASCII','UTF-8','EUC-KR', 'SJIS');
  $str_encode = mb_detect_encoding($str, $encode);

 if(strtoupper($str_encode) != 'EUC-KR') {
   $str = iconv($str_encode, 'EUC-KR', $str);
 }
  return $str;
}

  ?>
