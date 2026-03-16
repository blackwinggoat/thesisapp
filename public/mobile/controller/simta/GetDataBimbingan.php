<?php 

class Bimbingan{
	public function __construct($stb, $nama, $judul, $jml){
		$this->stb   = $stb;
		$this->nama  = $nama;
		$this->judul = $judul;
		$this->jml   = $jml;
	}
}

include "../ConSimta.php";


$sql = "call get_list_bimbingan(?)";

$ps = $conn->stmt_init();
$ps->prepare($sql);
$ps->bind_param("s", $nidn);
$nidn = $_POST["nidn"];

$ps->execute();
$hasil = $ps->get_result();

$respon = array();
$i = 0;

if ($hasil->num_rows > 0){
	while($row = $hasil->fetch_assoc()){
		$respon[$i] = new Bimbingan(
			utf8_encode($row['C_NPM']),
			utf8_encode($row['NAMA_MAHASISWA']),
			utf8_encode($row['judul']),
			utf8_encode($row['jml'])
		);
		$i++;
	}
}

$ps->close();
$conn->close();

echo json_encode($respon);

?>