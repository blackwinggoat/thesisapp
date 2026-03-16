<?php 
	

include "../ConSimta.php";
include "../../model/simta/JadwalUjianMhs.php";

//$_POST = json_decode(file_get_contents('php://input'), true);

$sql = "call get_jadwal_ujian_mhs(?)";

$ps = $conn->stmt_init();
$ps->prepare($sql);
$ps->bind_param("s", $stb);
$stb = $_POST["stb"];
$ps->execute();
$hasil = $ps->get_result();

$response = array();
$i = 0;

if ($hasil->num_rows > 0){
	while($row = $hasil->fetch_assoc()){
		$response[$i] = new JadwalUjianMhs($row['C_NPM'], $row['nm_ks'], $row['nm_penguji1'], $row['nm_penguji2'],
			$row['nm_penguji3'], $row['jam_ujian'], $row['nama_ruangan'], $row['tgl_ujian_f'], $row['jns_ujian']);
		$i++;
	}
}

$ps->close();
$conn->close();

echo json_encode($response);


?>