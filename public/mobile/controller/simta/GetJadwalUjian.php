<?php 
	

include "../ConSimta.php";
include "../../model/simta/JadwalUjian.php";

//$_POST = json_decode(file_get_contents('php://input'), true);

$sql = "call get_jadwal_ujian(?)";

$ps = $conn->stmt_init();
$ps->prepare($sql);
$ps->bind_param("s", $nidn);
$nidn = $_POST["nidn"];
$ps->execute();
$hasil = $ps->get_result();

$response = array();
$i = 0;

if ($hasil->num_rows > 0){
	while($row = $hasil->fetch_assoc()){
		$response[$i] = new JadwalUjian($row['C_NPM'], $row['NAMA_MAHASISWA'], $row['tgl_ujian_f'], $row['jam_ujian'], $row['nama_ruangan'], $row['jns_ujian']);
		$i++;
	}
}
else
	$stsLogin = false;

$ps->close();
$conn->close();

echo json_encode($response);


?>