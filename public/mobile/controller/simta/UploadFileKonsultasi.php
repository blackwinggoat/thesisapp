<?php

if($_SERVER['REQUEST_METHOD']=='POST'){
    include "../ConSimta.php";
        
    //$_FILES['image']['name']   give original name from parameter where 'image' == parametername eg. city.jpg
    //$_FILES['image']['tmp_name']  temporary system generated name

    $originalImgName= $_FILES['filename']['name'];
    $tempName= $_FILES['filename']['tmp_name'];
    $folder="uploadedFiles/";
    $url = "https://apps.fikom.umi.ac.id/mobile/controller/simta/uploadedFiles/".$originalImgName; //update path as per your directory structure 
    $response = array();

    if(move_uploaded_file($tempName,$folder.$originalImgName)){
            $sql = "
                INSERT INTO tb_pesan (id_pengirim, id_penerima, isi_pesan, lampiran, waktu)
                VALUES (?,?,?,?,NOW())
            ";

            $ps = $conn->stmt_init();
            $ps->prepare($sql);
            $ps->bind_param("ssss", $p1, $p2, $p3, $p4);
            $p1 = $_POST["id_pengirim"];
            $p2 = $_POST["id_penerima"];
            $p3 = $originalImgName;
            $p4 = $url;

            if($ps->execute())
                $response['result'] = "OK";
            else
                $response['result'] = "ERROR";


            // kirim notif
            $sql = "
                SELECT
                    token_fcm, NOW() as waktu
                FROM
                    tb_fcm_token 
                WHERE
                    username = ?
            ";

            $ps = $conn->stmt_init();
            $ps->prepare($sql);
            $ps->bind_param("s", $p2);

            $ps->execute();
            $hasil = $ps->get_result();

            $ps->close();
            $conn->close();

            if ($hasil->num_rows > 0){
                $row = $hasil->fetch_assoc();
                $tokenHp = $row['token_fcm'];
                $waktu = $row['waktu'];
            }
            else{
                $response['result'] = "ERROR";
                echo json_encode($response);
                return;
            }

            $_POST = json_decode(file_get_contents('php://input'), true);


            $ch = curl_init('https://fcm.googleapis.com/fcm/send');
            $header = array('Content-Type:application/json', 
                'Authorization: key=AAAAzpW7WeQ:APA91bFn7IN7CT1dTM0R1p10lRwo2yOWSK4Vsp7AFWfLjA_TOPXNnsGItUMXOJHKmU-XKoVThoddLF16rnw0cfwwZjRkkTMCz6wBdzDzUqJdDu8M3cnN9KLdCikGw2TXp-CrQ9l8Ljf3');
            $data = json_encode(
                array(
                    "to" => $tokenHp,
                    "data" => array(
                                    "judul" => 'KONSUL TA',
                                    "pengirim" => $p1,
                                    "pesan" => $p3,
                                    "waktu" => $waktu,
                                    "lampiran" => $p4
                                )
                )
            );

            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            if (!curl_exec($ch)) curl_exec($ch);
            // end kirim notif


            $ps->close();
            $conn->close();

    }else{
        $response['result'] = "ERROR";
    }
}

echo json_encode($response);


?>