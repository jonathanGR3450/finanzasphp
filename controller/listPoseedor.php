<?php
    $curl="";
    $number=$_GET['page'];
    if (isset($_GET['q'])){
        $curl=curl_init("http://localhost:8080/api/public/api/v1/predialposeedor?page=".$number."&cc=".$_GET['q']);
    }else{
        $curl=curl_init("http://localhost:8080/api/public/api/v1/listpredialposeedor?page=".$number);
    }
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_HTTPGET,1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result=curl_exec($curl);
    curl_close($curl);
    $res=json_decode($result);
    echo json_encode($res->{'results'});
?>