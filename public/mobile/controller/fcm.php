<?php
	$_POST = json_decode(file_get_contents('php://input'), true);


	$tokenHp = "dCPjmrVOScicesUpzyQJaH:APA91bEE0qrAB6SnL-cU_kL9o5qva0VklmeB7HwyL6UF_MDRYTzRLnpJ0coe_Gqqhmw4g2s8Lt1ShJGRmk_Bft9uFz_UPWqqwvaYMQoDEfEWiOeGzQCioGbpuoHDXkqbwXr59So_NmBU";

	$ch = curl_init("https://fcm.googleapis.com/fcm/send");
	$header = array("Content-Type:application/json", 
		"Authorization: key=AAAAym9kN0s:APA91bFOiCilBMMI-fg7Z1epzxQ-pkseN4xAVqDE8UDUCURYPF_ZBuVJHMqeXkeIV5QOVWdSm3X6QYmDOFpAHP4bm1n3fSoqZEOem8pEdIQM0XE0CXhauXB3K8EE1G9OAZmDt8e96Xzc");
	$data = json_encode(
		array(
			"to" => $tokenHp,
			"data" => array("judul" => $_POST['judul'], "pesan" => $_POST['pesan']))
	);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	if (!curl_exec($ch)) curl_exec($ch);

?>

