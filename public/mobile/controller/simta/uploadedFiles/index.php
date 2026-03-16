<?php
	function menuju($alamat){
		ob_start();
		header('Location: '.$alamat);
		ob_end_flush();
		die();
	}

	menuju('https://apps.fikom.umi.ac.id');
?>
