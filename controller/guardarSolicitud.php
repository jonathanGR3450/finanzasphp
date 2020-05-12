<?php
    if ($_POST['predios']){
        $fecha = date('Y-m-d H:i:s');
        $acuerdopago=500000+rand(0, 99);
        $pago = [
            ['predial_acuerdo_pago'=>$acuerdopago,
            'empresa'=> 3,
            'tipo_impuesto'=>3,
            'tercero'=>'2000002933',
            'numero_acuerdo_predial'=>$acuerdopago,
            'vigencia'=>date('Y'),
            'fecha_sistema'=>$fecha,
            'historico'=>'n',
            'verificado'=>'n',
            'usuario'=>'prueba',
            'ip_wan'=>'182.192.1.1',
            'ip_local'=>'172.168.1.16']
        ];
        //dejar pendiente para crear esa tabla //para poseedor crear nuevo campo 5000
        isset($_POST['contribuyente']) ? $contribuyente=$_POST['contribuyente'] : $contribuyente="";
        isset($_POST['poseedor']) ? $poseedor=$_POST['poseedor'] : $poseedor="";
        $municipio=$_POST['municipio'];
        $datoscampos=[
            '9131'=>$_POST['predios'], //pk predios
            '9111'=>$contribuyente, //array de contribuyente o tercero
            '11004'=>$poseedor, //array de poseedor
            '11005'=>$municipio, //municipio
            '11006'=>$_POST['direccion'], //direccion de notificacion
            '11007'=>$_POST['matricula'], //matricula del predio
            '9112'=>getvigencia($_POST['vigencia']), //array de vigencias
            '9114'=>$_POST['numerocuotas'], //numero de cuotas
            '9115'=>"", //fecha de resolucion
            '9116'=>'', //numero de resolucion
            '9129'=>'i', //estado acuerdo pagado (ellos lo tienen con f)
        ];
        $pagocampos=[];
        $pkpagocampos=$acuerdopago+50000;
        $count=0;
        foreach ($datoscampos as $datos=>$x){
            if (is_array($x)){
                foreach ($x as $val){
                    $pagocampos[]=[
                        'predial_acuerdo_pago_campos'=>$pkpagocampos+$count,
                        'predial_acuerdo_pago'=>$acuerdopago,
                        'campo'=>$datos,
                        'valor'=>$val
                    ];
                    $count++;
                }
            }else{
                $pagocampos[]=[
                    'predial_acuerdo_pago_campos'=>$pkpagocampos+$count,
                    'predial_acuerdo_pago'=>$acuerdopago,
                    'campo'=>$datos,
                    'valor'=>$x
                ];
                $count++;
            }
        }

        //crear tabla la tabla de vigencias en predial_acuerdo_vigencias
        $pkacuerdovigencias=$acuerdopago+10000;
        $pagovigencias=[];
        $count=0;
        foreach ($_POST['vigencia'] as $vigencia){
            if (end($_POST['vigencia'])!==$vigencia){
                $pagovigencias[]=[
                    "predial_acuerdo_pago_vigencia"=>$pkacuerdovigencias+$count,
                    "predial_acuerdo_pago"=>$acuerdopago,
                    "campo"=>'9120',
                    "vigencia"=>$vigencia[0],
                    "impuesto"=>$vigencia[1],
                    "interes"=>$vigencia[2],
                    "descuento"=>0,
                    "total"=>$vigencia[6],
                    "fecha_sistema"=>$fecha,
                    "sobretasa"=>$vigencia[3],
                    "interes_sobretasa"=>$vigencia[4],
                    "otros"=>$vigencia[5]
                ];
                $count++;
            }
        }

        //guardar las cuotas discriminadas
        $pkacuerdocuotas=$acuerdopago+100000;
        $pagocuota=[];
        $count=0;
        foreach ($_POST['cuotas'] as $item) {
            if ($item!==end($_POST['cuotas'])){
                $pagocuota[]=[
                    "predial_acuerdo_pago_cuota"=>$pkacuerdocuotas+$count,
                    "predial_acuerdo_pago"=>$acuerdopago,
                    "campo"=>9121,
                    "fecha_pago"=>$item[9],
                    "porcentaje_cuota"=>$item[0],
                    "saldo_impuesto"=>0,
                    "impuesto"=>$item[1],
                    "sancion"=>0,
                    "interes"=>$item[2],
                    "interes_plazo"=>$item[7],
                    "valor_cuota"=>$item[8],
                    "usuario"=>"prueba",
                    "consecutivo"=>$count,
                    "fecha_sistema"=>$fecha,
                    "dias_mora"=>0,
                    "interes_mora"=>0,
                    "interes_manual"=>0,
                    "observacion"=>"",
                    "valor_sobretasa"=>$item[3],
                    "valor_sobretasa_interes"=>$item[4],
                    "otros"=>$item[5]
                ];
                $count++;
            }
        }

        $json=json_encode(array('pago'=>$pago,
            'pagovigencias'=>$pagovigencias,
            'pagocuota'=>$pagocuota,
            'pagocampos'=>$pagocampos));
        $curl=curl_init("http://localhost:8080/api/public/api/v1/insertPrediaAcuerdo");
        curl_setopt($curl, CURLOPT_POSTFIELDS, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result=curl_exec($curl);
        curl_close($curl);

    }
    header("LOCATION:http://localhost:8080/finanzasphp/");
    exit();

    function getvigencia($data){
        $result=[];
        unset($data[count($data)]);
        foreach ($data as $item){
            $result[]=$item[0];
        }
        return $result;
    }

?>