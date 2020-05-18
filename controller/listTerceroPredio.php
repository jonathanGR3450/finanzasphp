<?php
    $curl="";
    $number=$_GET['page'];
    isset($_GET['q']) ? $ref=$_GET['q']: $ref='';
    $curl=curl_init("http://localhost:8080/api/public/api/v1/liketerceroPredio?page=".$number."&cc=".$ref);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_HTTPGET, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $results=curl_exec($curl);
    curl_close($curl);
    $res=json_decode($results);
    echo json_encode($res->{'results'});
?>