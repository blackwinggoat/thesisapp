<?php 

class DosenBimbingan{
	function __construct($stb, $nidn, $nama, $jns, $jmlPesanBaru){
		$this->stb=$stb;
		$this->nidn=$nidn;
		$this->nama=$nama;
		$this->jns=$jns;
		$this->jmlPesanBaru=$jmlPesanBaru;
	}
}

include "../ConSimta.php";


$sql = "call get_pbb_pngj(?)";

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
		$response[$i] = new DosenBimbingan($row['C_NPM'], $row['nidn'], $row['nm_dosen'], $row['tipe_dosen'], $row['jml_pesan']);
		$i++;
	}
}

$ps->close();
$conn->close();

echo json_encode($response);


?>