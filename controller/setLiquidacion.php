<?php
    date_default_timezone_set('America/Bogota');
    if (isset($_GET['json'])){
        $datos=json_decode($_GET['json'], true);
        $pkliquidacion=30780+rand(0,900);
        $liquidacion=[];
        $liquidacion[]=[
                'predial_acuerdo_pago_liquidacion'=>$pkliquidacion,
                'predial_acuerdo_pago_cuota'=>$datos[0]['cuotas'][0],
                'vigencia'=>date('Y'),
                'numero_liquidacion_predial_acuerdo'=>$pkliquidacion,
                'fecha_generacion'=>date('Y-m-d'),
                'fecha_limite_pago'=>$datos[0]['fechapago'],
                'estado_pago'=>'d',
                'tercero'=>'2000002933',
                'tipo_impuesto'=>'3',
                'archivo'=>'xxxxxxxxxxxxxxxx',
                'fecha_sistema'=>date('Y-m-d H:i:s'),
                'pago'=>$datos[0]['monto']

        ];
        $json=json_encode(array('liquidacion'=>$liquidacion));
        $curl=curl_init("http://localhost:8080/api/public/api/v1/setLiquidacion");
        curl_setopt($curl, CURLOPT_POSTFIELDS, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result=curl_exec($curl);
        curl_close($curl);
        $res=json_decode($result, true);
        echo json_encode($res['results']);
    }

?>