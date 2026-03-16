<?php 

include "../ConSimta.php";

$_POST = json_decode(file_get_contents('php://input'), true);

$sql = "
	UPDATE tb_pesan set sts_baca='1' where id_pengirim=? and id_penerima=?
";

$ps = $conn->stmt_init();
$ps->prepare($sql);
$ps->bind_param("ss", $p1, $p2);
$p1 = $_POST["p1"];
$p2 = $_POST["p2"];

if($ps->execute())
	$hasil['result'] = "OK";
else
	$hasil['result'] = "ERROR";

$ps->close();
$conn->close();

echo json_encode($hasil);
?>