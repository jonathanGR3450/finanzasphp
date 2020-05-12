<?php
    $curl="";
    $pk="1052598";
    if (isset($_GET['json'])){
        $json=json_decode($_GET['json'], true);
        $pk=$json[0]['id'];
    }
    $curl=curl_init("http://localhost:8080/api/public/api/v1/valorFactura?pkfactura=".$pk);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_HTTPGET, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
    $results=curl_exec($curl);
    curl_close($curl);
    $res=json_decode($results, true);
    $data=$res['results'];

    $datosVigencia=[];
    foreach ($data AS $x){
        $x===reset($data) ? $x['otros']=2100 : $x['otros']=0;
        $x['suma']=suma($x);
        $aux=[];
        foreach ($x as $value){
            $aux[]=$value;
        }
        $datosVigencia[]=$aux;
    }
    $datosVigencia[]=totales($data);
    $total=array('tabla'=>$datosVigencia);
    echo json_encode($total);

    function suma($data){
        unset($data['descripcion']);
        return array_reduce($data, function ($ant, $curr){
           return $ant + $curr;
        });
    }
    function totales($data){
        $result=[];
        $result[]="Totalizado de Vigencias";
        foreach ($data as $datum) {
            unset($datum['descripcion']);
            $i=1;
            foreach ($datum as $x){
                $result[$i]+=$x;
                $i++;
            }
        }
        $result[]=2100;
        $result[]=suma($result);
        return $result;
    }

?>