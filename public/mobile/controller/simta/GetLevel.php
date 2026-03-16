<?php 
	

include "../ConSimta.php";

$_POST = json_decode(file_get_contents('php://input'), true);

$sql = "
	SELECT level 
	FROM
		users 
	WHERE
		name=?
";

$ps = $conn->stmt_init();
$ps->prepare($sql);
$ps->bind_param("s", $username);
$username = $_POST["username"];
$ps->execute();
$hasil = $ps->get_result();

$response = array();
$row = $hasil->fetch_assoc();
$response['level_user'] = $row['level'];

$ps->close();
$conn->close();

echo json_encode($response);


?>