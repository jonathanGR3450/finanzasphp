<?php
    $curl="";
    isset($_POST['data']) ? $datos=json_decode($_POST['data'],true ) : $datos=[];
    isset($datos[0]) ? $pagina=$datos[0]['page'] : $pagina=1;
    isset($datos[1]) ? $numero=$datos[1]['numero'] : $numero="";
    $curl=curl_init("http://localhost:8080/api/public/api/v1/getListAcuerdos?numero=".$numero."&page=".$pagina);
    curl_setopt($curl, CURLOPT_HEADER,0);
    curl_setopt($curl, CURLOPT_HTTPGET, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result=curl_exec($curl);
    curl_close($curl);
    $result=json_decode($result, true);
    print_r(json_encode($result['result']));
?>