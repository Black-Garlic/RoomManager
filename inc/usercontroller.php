<?php
  header('Content-Type: text/html; charset=UTF-8');
  include "config.php";

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
  ?>
