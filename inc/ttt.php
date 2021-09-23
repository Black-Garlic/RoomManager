<?php
	include "config.php";
	
	$lecture_num = date("YmdH")."_";
	$sql ="SELECT max(id) from Reservation";
    $result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_row($result);
	if($row)	$lecture_num .= ($row[0] + 1);

	echo $lecture_num;

?>