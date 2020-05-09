<?php
    $curl="";
    $id=202108;
    if (isset($_GET['json'])){
        $json=json_decode($_GET['json'], true);
        $id=$json[0]['id'];
    }
    $curl=curl_init("http://localhost:8080/api/public/api/v1/vigenciasPredio?predial=".$id);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_HTTPGET, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
    $results=curl_exec($curl);
    curl_close($curl);
    $res=json_decode($results, true);
    echo json_encode($res['results']);
?>