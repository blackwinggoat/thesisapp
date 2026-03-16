<?php 
	

include "../ConSimta.php";
include "../../model/simta/DetailJadwalUjian.php";

$_POST = json_decode(file_get_contents('php://input'), true);

$sql = "call get_detail_jadwal_ujian(?, ?)";

$ps = $conn->stmt_init();
$ps->prepare($sql);
$ps->bind_param("ss", $stb, $jns);
$stb = $_POST["stb"];
$jns = $_POST["jns"];

$ps->execute();
$hasil = $ps->get_result();


$i = 0;

if ($hasil->num_rows > 0){
	$row = $hasil->fetch_assoc();
	$response = new DetailJadwalUjian($row['C_NPM'], $row['NAMA_MAHASISWA'], $row['jns_ujian'], $row['judul'], $row['nm_pbb1'], $row['nm_pbb2'], $row['nm_ks'], $row['nm_penguji1'], $row['nm_penguji2'], $row['nm_penguji3'], $row['tgl_ujian'], $row['jam_ujian'], $row['nama_ruangan'], $row['nomor_sk']);
}
else
	$response = "Data tidak ditemukan";

$ps->close();
$conn->close();

echo json_encode($response);

?>