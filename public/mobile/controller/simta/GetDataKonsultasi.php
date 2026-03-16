<?php 

class Pesan{
	function __construct($isiPesan, $aktor, $waktu, $lampiran){
		$this->isiPesan=$isiPesan;
		$this->aktor=$aktor;
		$this->waktu=$waktu;
		$this->lampiran=$lampiran;
	}
}

include "../ConSimta.php";


$sql = "
	select *, 'aktor1' as aktor from tb_pesan WHERE id_pengirim=? and id_penerima=?
	union all
	select *, 'aktor2' as aktor from tb_pesan WHERE id_pengirim=? and id_penerima=?
	ORDER BY waktu
";

$ps = $conn->stmt_init();
$ps->prepare($sql);
$ps->bind_param("ssss", $id1, $id2, $id2, $id1);
$id1 = $_POST["id1"];
$id2 = $_POST["id2"];

$ps->execute();
$hasil = $ps->get_result();

$response = array();
$i = 0;

if ($hasil->num_rows > 0){
	while($row = $hasil->fetch_assoc()){
		$response[$i] = new Pesan($row['isi_pesan'], $row['aktor'], $row['waktu'], $row['lampiran']);
		$i++;
	}
}

$ps->close();
$conn->close();

echo json_encode($response);


?>