<?php

	// cara pakai: php nm_file.php param="value"

	if (php_sapi_name() === 'cli') {
	    parse_str(implode('&', array_slice($argv, 1)), $_REQUEST);
	}

	$tokenHp = $_REQUEST['token'];

	$ch = curl_init("https://fcm.googleapis.com/fcm/send");
	$header = array("Content-Type:application/json", 
		"Authorization: key=AAAAym9kN0s:APA91bFOiCilBMMI-fg7Z1epzxQ-pkseN4xAVqDE8UDUCURYPF_ZBuVJHMqeXkeIV5QOVWdSm3X6QYmDOFpAHP4bm1n3fSoqZEOem8pEdIQM0XE0CXhauXB3K8EE1G9OAZmDt8e96Xzc");
	$data = json_encode(
		array(
			"to" => $tokenHp,
			"data" => array("judul" => $_REQUEST['judul'], "pesan" => $_REQUEST['pesan']))
	);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_exec($ch);


?>

