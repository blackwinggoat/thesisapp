<?php
	$_POST = json_decode(file_get_contents('php://input'), true);


	$tokenHp = 'cMhXGyYOSv-7Jk-_C1KMtf:APA91bGNoR4WsKpamlBWvzx_0zGm1iyThoy1bJwGyF_ZXZ1rQ_kSmnmJ5rvObGpcSfdm08yMexXHx0hR5EWn3geiLFwI9Sfvm3YhYPGmMUJTeBatd5vLsPdvkeHlfbE6Nv00lYxqKh3s';

	$ch = curl_init('https://fcm.googleapis.com/fcm/send');
	$header = array('Content-Type:application/json', 
		'Authorization: key=AAAAzpW7WeQ:APA91bFn7IN7CT1dTM0R1p10lRwo2yOWSK4Vsp7AFWfLjA_TOPXNnsGItUMXOJHKmU-XKoVThoddLF16rnw0cfwwZjRkkTMCz6wBdzDzUqJdDu8M3cnN9KLdCikGw2TXp-CrQ9l8Ljf3');
	$data = json_encode(
		array(
			"to" => $tokenHp,
			"data" => array(
							"judul" => $_POST['judul'],
							"pengirim" => $_POST['pengirim'],
							"pesan" => $_POST['pesan'],
							"waktu" => $_POST['waktu'],
							"lampiran" => $_POST['lampiran']
						)
		)
	);

	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	if (!curl_exec($ch)) curl_exec($ch);

?>

