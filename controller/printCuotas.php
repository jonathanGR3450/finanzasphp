<?php
    if (isset($_GET['json'])){
        $datos=json_decode($_GET['json'], true);
        $cuotaInicial=$datos['data'][0]['primeracuota'];
        $numeroCuotas=$datos['data'][1]['cuotas'];
        $totalVigencias=$datos['data'][2]['totales'];
        $sum=0;
        $totalCuotas=[];
        $cuota="";
        $acumulado=[];
        for ($i=0;$i<$numeroCuotas;$i++){
            $i==0 ? $cuota=$cuotaInicial : $cuota=(100-$cuotaInicial)/$numeroCuotas;
            $res=total($totalVigencias, $cuota, $acumulado);
            for ($k=0;$k<count($res);$k++){
                $acumulado[$k]+=$res[$k];
            }
            $sum=suma($res);
            $res[]=$sum;
            //campo interes plazo, pero es solo para la ultima cuota, poongo cero y como no suma nada, el total no cambia
            $res[]=0;
            $res[]=$sum;
            $totalCuotas[]=$res;
        }
        //la ultima cuota, envio todas las cuotas anteriores, pero me toca añadir el interes plazo
        $totalCuotas[]=ultimaCuota($totalCuotas, $totalVigencias, 500);
        //suma el total
        $totalCuotas[]=totalCuotas($totalCuotas);
        $lol=['total'=>$totalCuotas];
        echo json_encode($lol);
    }
    /*function imprimir($totalCuotas, $porcentaje, $numeroCuotas){
        $html="";
        $count=0;
        foreach ($totalCuotas as $value){
            $st="";
            if ($count==0){
                $cuota="Inicial";
                $porcentajeCuota=$porcentaje;
                $fecha="<td><input type='date' name='cuotas[{$count}][]' value='".date("Y-m-d")."'></td>";
            }else if ($count==count($totalCuotas)-1){
                $st="style='background-color: #aec6ff'";
                $cuota="Total";
                $porcentajeCuota="100";
                $fecha="";
            }else{
                $cuota=$count;
                $porcentajeCuota=number_format((100-$porcentaje)/$numeroCuotas, 2, '.','');
                $fecha="<td><input type='date' name='cuotas[{$count}][]'  value='".date("Y-m-d")."'></td>";
            }
            $html.="<tr {$st}>
                    <th scope='row'>{$cuota}</th>
                    <td><input type='text' value='{$porcentajeCuota}' name='cuotas[{$count}][]' hidden>{$porcentajeCuota}</td>";
            for($i=0; $i<count($value);$i++){
                $html.="<td><input type='text' value='{$value[$i]}' name='cuotas[{$count}][]' hidden>{$value[$i]}</td>";
            }
            $html.="{$fecha}</tr>";
            $count++;
        }
        return $html;
    }*/
    function total($totales, $cuota, $acumulado){
        $result=[];
        for ($i=0; $i<count($totales)-1; $i++){

            $acumulado[$i]<$totales[$i] ? $result[$i]=round($totales[$i]*$cuota/100, -2) : $result[$i]=0;
        }
        return $result;
    }
    function suma($res){
        return array_reduce($res, function ($ant, $curr){
            return $ant + $curr;
        });
    }
    function totalCuotas($cuotas){
        $result=[];
        foreach ($cuotas as $value){
            for($i=0; $i<count($value);$i++){
                $result[$i]+=$value[$i];
            }
        }
        return $result;
    }
    function ultimaCuota($cuotas, $total, $interes){
        $result=[];
        foreach ($cuotas as $value){
            for($i=0; $i<count($total)-1;$i++){
                $result[$i]+=$value[$i];
            }
        }
        for ($i=0;$i<count($total)-1; $i++){
            $result[$i]=$total[$i]-$result[$i];
        }
        $sum=suma($result);
        $result[]=$sum;
        $result[]=$interes;
        $result[]=$interes+$sum;
        return $result;
    }
?>