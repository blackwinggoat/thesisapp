<?php

class JadwalUjian{
	function __construct($stb, $namaMahasiswa, $tglUjian, $jam, $ruangan, $jnsUjian){
		$this->tglUjian = $tglUjian;
		$this->stb = $stb;
		$this->namaMahasiswa = $namaMahasiswa;
		$this->ruangan = $ruangan;
		$this->jam = $jam;
		$this->jnsUjian = $jnsUjian;
	}
}

?>