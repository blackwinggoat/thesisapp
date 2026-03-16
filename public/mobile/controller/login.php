<?php

	function loginSimta(){
		include_once "ConSimta.php";
		$_POST = json_decode(file_get_contents('php://input'), true);
		
		$sql = "select password from users where name = ?";
		$ps = $conn->stmt_init();
		$ps->prepare($sql);
		$ps->bind_param("s", $user);
		$user = $_POST["username"];
		$ps->execute();
		$hasil = $ps->get_result();
		$stsLogin = false;

		if ($hasil->num_rows > 0){
			$row = $hasil->fetch_assoc();
			$salt = substr($row['password'], 7, 22);

			$options = [
				'salt' => $salt,
		    	'cost' => 10
			];

			$passwd = password_hash($_POST["password"], PASSWORD_BCRYPT, $options);

			$sql = "select * from users where name = ? and password = ?";
			$ps = $conn->stmt_init();
			$ps->prepare($sql);
			$ps->bind_param("ss", $user, $passwd);
			$ps->execute();
			$hasil = $ps->get_result();

			if ($hasil->num_rows > 0)
				$stsLogin = true;
		}
		else
			$stsLogin = false;

		$ps->close();
		$conn->close();

		return $stsLogin;
	}

	function loginMonitoring(){
		// include_once "ConJadwal.php";
		// $_POST = json_decode(file_get_contents('php://input'), true);
		
		// $sql = "select password from users where username = ?";
		// $ps = $conn->stmt_init();
		// $ps->prepare($sql);
		// $ps->bind_param("s", $user);
		// $user = $_POST["username"];
		// $ps->execute();
		// $hasil = $ps->get_result();
		// $stsLogin = false;

		// if ($hasil->num_rows > 0){
		// 	$row = $hasil->fetch_assoc();
		// 	$salt = substr($row['password'], 7, 22);

		// 	$options = [
		// 		'salt' => $salt,
		//     	'cost' => 10
		// 	];

		// 	$passwd = password_hash($_POST["password"], PASSWORD_BCRYPT, $options);

		// 	$sql = "select * from users where username = ? and password = ?";
		// 	$ps = $conn->stmt_init();
		// 	$ps->prepare($sql);
		// 	$ps->bind_param("ss", $user, $passwd);
		// 	$ps->execute();
		// 	$hasil = $ps->get_result();

		// 	if ($hasil->num_rows > 0)
		// 		$stsLogin = true;
		// }
		// else
		// 	$stsLogin = false;

		// $ps->close();
		// $conn->close();

		// return $stsLogin;
		return false;
	}

	function loginEArsip(){
		return false;
	}

	function loginFinance(){
		return false;	
	}

	
	$sts_login = array();
	
	$sts_login['login_simta'] = loginSimta();
	$sts_login['login_monitoring'] = loginMonitoring();
	$sts_login['login_earsip'] = loginEArsip();
	$sts_login['login_finance'] = loginFinance();

	echo json_encode($sts_login);
?>