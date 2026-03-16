<?php

class DetailJadwalUjian{
	function __construct($stb, $namaMahasiswa, $jnsUjian, $judul, $pbb1, $pbb2, $ks, $pngj1, $pngj2, $pngj3, $tglUjian, $jam, $ruangan, $noSK){
		$this->stb           = $stb;
		$this->namaMahasiswa = $namaMahasiswa;
		$this->jnsUjian      = $jnsUjian;
		$this->judul         = $judul;
		$this->pbb1          = $pbb1;
		$this->pbb2          = $pbb2;
		$this->ks            = $ks;
		$this->pngj1         = $pngj1;
		$this->pngj2         = $pngj2;
		$this->pngj3         = $pngj3;
		$this->tglUjian      = $tglUjian;
		$this->jam           = $jam;
		$this->ruangan       = $ruangan;
		$this->noSK          = $noSK;
	}
}

?>