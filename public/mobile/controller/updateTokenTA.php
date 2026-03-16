<?php

	include_once "ConSimta.php";
	$_POST = json_decode(file_get_contents('php://input'), true);
	$hasil = array();
	
	$sql = "update tb_fcm_token set token_fcm = ? where username = ?";
	$ps = $conn->stmt_init();
	$ps->prepare($sql);
	$ps->bind_param("ss", $token, $user);
	$token =$_POST["token"]; 
	$user = $_POST["username"];
	$hasil['sts_update'] = $ps->execute();

	$ps->close();
	$conn->close();

	echo json_encode($hasil);
?>