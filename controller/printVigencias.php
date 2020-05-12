<?php
    $curl="";
    $id="268931";
    $vigencias=[];
    if (isset($_GET['json'])){
        $json=json_decode($_GET['json'], true);
        $id=$json[1]['predio'];
        $vigencias=$json[0]['vigencias'];
    }

    //para vigencias
    $curl=curl_init("http://localhost:8080/api/public/api/v1/valorVigencias?predial=".$id);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_HTTPGET, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
    $results=curl_exec($curl);
    curl_close($curl);
    $res=json_decode($results, true);
    $data=$res['results'];
    $newdata=vigencias($data, $vigencias);
    $html="";
    $count=0;
    $datosVigencia=[];
    foreach ($newdata as $x){
        $x===reset($newdata) ? $x['otros']=2100 : $x['otros']=0;
        unset($x['id']);
        $x['suma']=suma($x);
        $aux=[];
        foreach ($x as $data){
            $aux[]=$data;
        }
        $datosVigencia[]=$aux;
        /*$html.="<tr>";
        foreach ($x as $datum){
            $html.="<td><input type='text' value='{$datum}' name='vigencia[{$count}][]' hidden>{$datum}</td>";
        }
        $html.="</tr>";
        $count++;*/
    }
    $datosVigencia[]=totales($newdata);
    $lol=array('tabla'=>$datosVigencia);
    echo json_encode($lol);
    /*$count+=1;
    $totalizado=totales($newdata);
    $html.="<tr id='totales' style='background-color: #aec6ff'>
                            <th scope='row'>Totalizado de Vigencias</th>";
    foreach ($totalizado as $total){
        $html.="<td><input type='text' value='{$total}' name='vigencia[{$count}][]' hidden>{$total}</td>";
    }
    $html.="</tr>";
    echo $html;*/

    function vigencias($data, $vigencias){
        $result=[];
        foreach ($vigencias as $vigencia){
            foreach ($data as $value) {
                $value['id']==$vigencia ? $result[]=$value : null ;
            }
        }
        return $result;
    }
    function suma($data){
        unset($data['vigencia'], $data['id']);
        return array_reduce($data, function ($ant, $curr){
            return $ant + $curr;
        });
    }
    function totales($data){
        $result=[];
        $result[]="Totalizado de Vigencias";
        foreach ($data as $datum) {
            unset($datum['vigencia'], $datum['id']);
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