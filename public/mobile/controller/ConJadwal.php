<?php
  $conn = new mysqli("localhost", "root", "root", "jadwalapps");
  
  if ($conn->connect_error){
    die("Koneksi Gagal: " . $conn->connect_error);
  }
?>