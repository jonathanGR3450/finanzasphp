<?php
    $municipio="";
    if (isset($_GET['q'])){
        $municipio=$_GET['q'];
    }
    $number=$_GET['page'];
    $curl=curl_init("http://localhost:8080/api/public/api/v1/municipiosLike?municipio=".$municipio."&page=".$number);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_HTTPGET,1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result=curl_exec($curl);
    curl_close($curl);
    $res=json_decode($result);
    echo json_encode($res->{'results'});

?>