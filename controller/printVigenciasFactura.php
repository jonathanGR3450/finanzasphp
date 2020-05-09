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
    $html="";
    $cout=0;
    foreach ($data AS $x){
        $x===reset($data) ? $x['otros']=2100 : $x['otros']=0;
        $x['suma']=suma($x);
        //modificar la consulta para traer el id de cada vigencia para el guardar
        $html.="<tr>";
        foreach ($x as $value){
            $html.="<td><input type='text' value='{$value}' name='vigencia[{$cout}][]' hidden>{$value}</td>";
        }
        $html.="</tr>";
        $cout++;
    }
    $totalizado=totales($data);
    $cout+=1;
    $html.="<tr id='totales' style='background-color: #aec6ff'>
                        <th>Totalizado de Vigencias</th>";
    foreach ($totalizado as $total){
        $html.="<td><input type='text' value='{$total}' name='vigencia[{$cout}][]' hidden>{$total}</td>";
    }
    $html.="</tr>";
    echo $html;

    function suma($data){
        unset($data['descripcion']);
        return array_reduce($data, function ($ant, $curr){
           return $ant + $curr;
        });
    }
    function totales($data){
        $result=[];
        foreach ($data as $datum) {
            unset($datum['descripcion']);
            $i=0;
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