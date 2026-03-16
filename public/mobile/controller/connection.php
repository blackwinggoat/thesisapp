<?php
  $conn = new mysqli("localhost", "thesisapp_blackwinggoat", "@Albert88", "thesisapp_51mt4db");
  
  if ($conn->connect_error){
    die("Koneksi Gagal: " . $conn->connect_error);
  }
?>