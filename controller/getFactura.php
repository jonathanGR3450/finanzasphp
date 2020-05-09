<?php
    $curl="";
    $number=$_GET['page'];
    $id=$_GET['id'];
    if (isset($_GET['q'])){
        $q=$_GET['q'];
        $curl=curl_init("http://localhost:8080/api/public/api/v1/facturasPredioLike?page=".$number."&predial=".$id."&factura=".$q);
    }else{
        $curl=curl_init("http://localhost:8080/api/public/api/v1/facturasPredio?page=".$number."&predial=".$id);
    }
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_HTTPGET, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $results=curl_exec($curl);
    curl_close($curl);
    $res=json_decode($results);
    echo json_encode($res->{'results'});
?>