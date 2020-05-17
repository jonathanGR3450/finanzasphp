<?php

    $id=json_decode($_GET['json'], true);
    $curl=curl_init("http://localhost:8080/api/public/api/v1/getCuotasAcuerdo?&acuerdo=".$id[0]['id']);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_HTTPGET, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result=curl_exec($curl);
    curl_close($curl);
    $res=json_decode($result, true);
    echo json_encode($res['results']);
?>