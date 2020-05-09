<?php
    $curl="";
    $id=[];
    if (isset($_GET['json'])){
        $id = json_decode($_GET['json'], true);
    }
    $curl=curl_init("http://localhost:8080/api/public/api/v1/getMatricula?predial=".$id[0]['id']);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPGET, 1);
    $res=curl_exec($curl);
    curl_close($curl);

    echo $res;
?>