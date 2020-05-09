<?php

namespace App\Http\Controllers\moduloFinanzas;

use App\Models\Database\Factsisoft\PredialPredio;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Database\Factsisoft\ImpuestoPreliquidacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class PredioController extends Controller
{
    /*
     * trae la lista de matriculas impuesto_preliquidaciones
     * */
    public function listPredios()
    {
        $predios=ImpuestoPreliquidacion::select('predial_predio as id', 'referencia_catastral as text')
            ->orderBy('vigencia', 'DESC')
            ->paginate(10);
        
        $morePages = ($request->page ?? 1 * $predios->perPage()) < $predios->total();

        return Response::json([
            'incomplete_results' => false,
            'more' => $morePages,
            'total_count' => $predios->total(),
            'results' => $predios
        ], 200, [], JSON_PRETTY_PRINT);
    }
    /*
     * busca el predio por coincidencias de referencia catastral (ref)
     * */
    public function buscarPredios(Request $request)
    {//'referencia_catastral as text'
        $predios=ImpuestoPreliquidacion::select('predial_predio as id', 'referencia_catastral as text')
            ->where('referencia_catastral', 'like', '%' . $request->ref . '%')
            ->orderBy('vigencia', 'DESC')
            ->paginate(10);

        $morePages = ($request->page ?? 1 * $predios->perPage()) < $predios->total();

        return Response::json([
            'incomplete_results' => false,
            'more' => $morePages,
            'total_count' => $predios->total(),
            'results' => $predios
        ], 200, [], JSON_PRETTY_PRINT);
    }

    public function getPredial(Request $request)
    {
        $predio=PredialPredio::select('predial_predio', 'referencia_catastral')
            ->where('predial_predio', '=', $request->predial)
            ->get();
        return Response::json(['data'=>$predio],200);
    }
    /*
     * me trae las matriculas inmoviliarias de un predio, direcciones, predial vigencia (el año es descripcion)
     * recibe como parametro predio_predial
     * */
    /*public function getMatricula(Request $request){
        $direccion=9002;
        $matricula=DB::table('predial_predio AS pp')
        ->select('pp.predial_predio', 'pp.referencia_catastral', 'rl1.valor AS matricula_inmobiliaria', 'rl2.valor AS direccion', 'rl2.predial_vigencia', 'pv.descripcion')
        ->join('predial_predio_campos AS rl1', function ($join) {
            $join->on('rl1.predial_predio', '=', 'pp.predial_predio')
                ->where('rl1.campo', '=', 9001);
        })
        ->leftjoin('predial_predio_campos AS rl2', function ($join) use ($direccion)  {
            $join->on('rl2.predial_predio', '=', 'pp.predial_predio');
            $join->on('rl2.predial_vigencia', '=', 'rl1.predial_vigencia')
            ->where('rl2.campo', '=', $direccion);
        })
        ->join('predial_vigencia AS pv', function ($join){
            $join->on('pv.predial_vigencia', '=', 'rl2.predial_vigencia');
        })
        ->where('pp.predial_predio', '=', $request->predial)
            ->orderBy('pv.predial_vigencia', 'desc')
        ->get();

        return Response::json([
            'incomplete_result'=>false,
            'result'=>$matricula
        ], 200, [], JSON_PRETTY_PRINT);
    }*/
    /*
    trae la matricula y la direccion, son campos de texto
    */
    public function getMatricula(Request $request){
        $direccion=9002;
        //'pp.predial_predio', 'pp.referencia_catastral', 'rl1.valor AS matricula_inmobiliaria', 'rl2.valor AS direccion', 'rl2.predial_vigencia', 'pv.descripcion'
        $matricula=DB::table('predial_predio AS pp')
        ->select('rl1.predial_predio_campos as id', 'rl1.valor AS matricula', 'rl2.valor AS direccion')
        ->join('predial_predio_campos AS rl1', function ($join) {
            $join->on('rl1.predial_predio', '=', 'pp.predial_predio')
                ->where('rl1.campo', '=', 9001);
        })
        ->leftjoin('predial_predio_campos AS rl2', function ($join) use ($direccion)  {
            $join->on('rl2.predial_predio', '=', 'pp.predial_predio');
            $join->on('rl2.predial_vigencia', '=', 'rl1.predial_vigencia')
            ->where('rl2.campo', '=', $direccion);
        })
        ->join('predial_vigencia AS pv', function ($join){
            $join->on('rl1.predial_vigencia', '=', 'pv.predial_vigencia');
        })
        ->where('pp.predial_predio', '=', $request->predial)
        ->orderBy('pv.predial_vigencia', 'desc')
        ->limit(1)
        ->get();

        return Response::json([
            'incomplete_result'=>false,
            'result'=>$matricula
        ], 200, [], JSON_PRETTY_PRINT);
    }

    /*
     * busca los dueños de un predio, predial_predio, el primero de la lista es el dueño actual
     * */
    public function terceroPredio(Request $request){
        $tercero=DB::table('predial_tercero_predio AS t1')//'t.nombre', 't.apellido', 't.identificacion'
        ->select('t.tercero AS id', 't.identificacion AS text')
        ->join('tercero AS t', 't1.tercero', '=', 't.tercero')
        ->where('t1.predial_predio','=', $request->predial)
        ->orderBy('t1.predial_vigencia', 'desc')
        ->orderBy('t1.predial_tipo_vinculacion', 'asc')
        ->limit(1)
        ->get();
        return Response::json([
            'incomplete_result'=>false,
            'result'=>$tercero
        ], 200, [], JSON_PRETTY_PRINT);
    }

    public function listTerceroPredio(){
        $tercero=DB::table('predial_tercero_predio AS t1')
        ->select('t.tercero AS id', 't.identificacion AS text')
        ->join('tercero AS t', 't1.tercero', '=', 't.tercero')
        ->orderBy('t1.predial_vigencia', 'desc')
        ->orderBy('t1.predial_tipo_vinculacion', 'asc')
        ->paginate(10);
        $morePages = ($request->page ?? 1 * $tercero->perPage()) < $tercero->total();
        
        return Response::json([
            'incomplete_result'=>false,
            'more_page'=>$morePages,
            'total_count'=>$tercero->total(),
            'results'=>$tercero
        ], 200, [], JSON_PRETTY_PRINT);
    }
    public function likeTerceroPredio(Request $request){
        //t.identificacion
        $tercero=DB::table('predial_tercero_predio AS t1')
        ->select('t.tercero AS id', 't.identificacion AS text')
        ->join('tercero AS t', 't1.tercero', '=', 't.tercero')
        ->where('t.identificacion', 'like', '%'.$request->cc.'%')
        ->orderBy('t1.predial_vigencia', 'desc')
        ->orderBy('t1.predial_tipo_vinculacion', 'asc')
        ->paginate(10);
        $morePages = ($request->page ?? 1 * $tercero->perPage()) < $tercero->total();
        
        return Response::json([
            'incomplete_result'=>false,
            'more_page'=>$morePages,
            'total_count'=>$tercero->total(),
            'results'=>$tercero
        ], 200, [], JSON_PRETTY_PRINT);
    }

    /*
    public function terceroPredio(Request $request){
        $tercero=DB::table('predial_tercero_predio AS t1')
        ->select('t.tercero', 't.nombre', 't.apellido', 't.identificacion', 't1.predial_vigencia', 'pv.descripcion', 't1.predial_tipo_vinculacion')
        ->join('tercero AS t', 't1.tercero', '=', 't.tercero')
        ->join('predial_vigencia AS pv','t1.predial_vigencia', '=', 'pv.predial_vigencia')
        ->where('t1.predial_predio','=', $request->predial)
        ->orderBy('t1.predial_vigencia', 'desc')
        ->orderBy('t1.predial_tipo_vinculacion', 'asc')
        ->get();
        return Response::json([
            'incomplete_result'=>false,
            'result'=>$tercero
        ], 200, [], JSON_PRETTY_PRINT);
    }
    */
    /*
     * para traer terceros para poseedor
     * */
    public function listPoseedor(Request $request){
        $tercero=DB::table('tercero AS t')
        ->select('t.tercero as id', 't.identificacion as text')
        ->paginate(10);

        return Response::json([
            'incomplete_results' => false,
            'results'            => $tercero
        ], 200, [], JSON_PRETTY_PRINT);
    }
    public function poseedor(Request $request){
        $tercero=DB::table('tercero AS t')
        ->select('t.tercero as id', 't.identificacion as text')
        ->where('t.identificacion', 'like', '%' . $request->cc . '%')
        ->paginate(10);

        return Response::json([
            'incomplete_results' => false,
            'results'            => $tercero
        ], 200, [], JSON_PRETTY_PRINT);
    }

    public function facturasPredio(Request $request){
        $facturas=DB::table('predial_liquidacion AS pl')
        ->select('pl.predial_liquidacion as id', 'pl.numero_factura AS text')
        //->join('predial_vigencia AS pv', 'pv.predial_vigencia', '=', 'pl.predial_vigencia')
        ->where('pl.predial_predio', '=', $request->predial)
        ->orderBy('pl.predial_vigencia', 'desc')
        ->paginate(10);
        return Response::json([
            'incomplete_result'=>false,
            'results'=>$facturas
        ], 200, [], JSON_PRETTY_PRINT);
    }
    public function facturasPredioLike(Request $request){
        $facturas=DB::table('predial_liquidacion AS pl')
        ->select('pl.predial_liquidacion as id', 'pl.numero_factura AS text')
        ->where('pl.predial_predio', '=', $request->predial)
        ->where('pl.numero_factura', 'like', '%'.$request->factura.'%')
        ->orderBy('pl.predial_vigencia', 'desc')
        ->paginate(10);
        return Response::json([
            'incomplete_result'=>false,
            'results'=>$facturas
        ], 200, [], JSON_PRETTY_PRINT);
    }
    
    /*
    campos para despues
    ,'plv.predial_liquidacion',
            'plv.predial_vigencia','plv.numero_factura','plv.generado',
            'plv.tarifa','plv.impuesto','plv.interes',
            'plv.sobretasa','plv.interes_sobretasa','plv.descuento',
            'plv.valor_total','plv.avaluo','plv.descuento_tributario',
            'plv.descuento_interes_sobretasa','plv.sobretasa_bomberil','plv.interes_sobretasa_bomberil','plv.descuento_sobretasa_bomberil',
            'plv.interes_corriente','pv.predial_vigencia','pv.descripcion'
    */
    public function vigenciasFactura(Request $request){
        $vigencias=DB::table('predial_liquidacion_vigencia AS plv')
        ->select('plv.predial_liquidacion_vigencia AS id', 'pv.descripcion AS text')
        ->join('predial_vigencia AS pv', 'pv.predial_vigencia', '=', 'plv.predial_vigencia')
        ->where('plv.predial_liquidacion', '=', $request->pkfactura)
        ->orderBy('plv.predial_vigencia', 'desc')
        ->get();

        return Response::json([
            'incomplete_result'=>false,
            'results'=>$vigencias
        ],200, [], JSON_PRETTY_PRINT);
    }
    //'', '', '', '', '',
    public function valoresVigenciasFactura(Request $request){
        $vigencias=DB::table('predial_liquidacion_vigencia AS plv')
        ->select('pv.descripcion', 'plv.impuesto', 'plv.interes', 
        'plv.sobretasa', 'plv.interes_sobretasa')
        ->join('predial_vigencia AS pv', 'pv.predial_vigencia', '=', 'plv.predial_vigencia')
        ->where('plv.predial_liquidacion', '=', $request->pkfactura)
        ->orderBy('plv.predial_vigencia', 'desc')
        ->get();

        return Response::json([
            'incomplete_result'=>false,
            'results'=>$vigencias
        ],200, [], JSON_PRETTY_PRINT);
    }

    public function vigenciasPredio(Request $request){
        $vigencias=DB::table('impuesto_preliquidaciones as ip')
        ->select('ip.id', 'ip.vigencia AS text')
        ->where('ip.predial_predio', '=', $request->predial)
        ->orderByDesc('ip.vigencia')
        ->get();
        return Response::json([
            'incomplete_result'=>false,
            'results'=>$vigencias
        ], 200, [], JSON_PRETTY_PRINT);
    }

    public function valorVigenciasPredio(Request $request){
        $vigencias=DB::table('impuesto_preliquidaciones as ip')
        ->select('ip.id','ip.vigencia', 'ip.impuesto', 'ip.interes', 'ip.sobretasa',
        'ip.interes_sobretasa')
        ->where('ip.predial_predio', '=', $request->predial)
        ->orderByDesc('ip.vigencia')
        ->get();
        return Response::json([
            'incomplete_result'=>false,
            'results'=>$vigencias
        ], 200, [], JSON_PRETTY_PRINT);
    }

    public function getListAcuerdos(){
        $result=DB::table('predial_acuerdo_pago AS pap')
        ->select('pap.predial_acuerdo_pago', 'pap.numero_acuerdo_predial',
        'pp.referencia_catastral',
         DB::raw("concat(tr.nombre, ' ', tr.apellido) AS nombre"),
        'papc4.valor as numero_cuotas',
        'pacuota.total', 'vigencias'
        )
        ->join('predial_acuerdo_pago_campos AS papc1', function($join){
            $join->on('pap.predial_acuerdo_pago', '=', 'papc1.predial_acuerdo_pago')
            ->where('papc1.campo', '=', '9131');
        })
        ->join('predial_predio AS pp', function($join){
            $join->on('pp.predial_predio', '=', DB::raw('cast(papc1.valor AS integer)'));
        })
        ->join('predial_acuerdo_pago_campos AS papc3', function($join){
            $join->on('pap.predial_acuerdo_pago', '=', 'papc3.predial_acuerdo_pago')
            ->where('papc3.campo','=', 9111);
        })
        ->join('tercero AS tr', 'tr.tercero', '=', DB::raw('cast(papc3.valor AS integer)'))
        ->join('predial_acuerdo_pago_campos AS papc4', function($join){
            $join->on('pap.predial_acuerdo_pago', '=', 'papc4.predial_acuerdo_pago')
            ->where('papc4.campo', '=', 9114);
        })
        ->join(DB::raw("(select pa.predial_acuerdo_pago, 
        sum(cast(pa.valor_cuota as float8)) as total 
        from predial_acuerdo_pago_cuota AS pa group by pa.predial_acuerdo_pago) AS pacuota"), 
        function($join){
            $join->on('pacuota.predial_acuerdo_pago', '=', 'pap.predial_acuerdo_pago');
        })
        ->join(DB::raw(" (select papc.predial_acuerdo_pago, 
        string_agg(papc.valor, ',') AS vigencias 
        from predial_acuerdo_pago_campos AS papc
        where papc.campo = 9112
        group by papc.predial_acuerdo_pago
        ) AS papc2"), function($join){
            $join->on('papc2.predial_acuerdo_pago', '=', 'pap.predial_acuerdo_pago');
        })
        ->orderBy('pap.predial_acuerdo_pago', 'DESC')
        ->limit(10)
        ->get();

        return Response::json([
            'incomplete_result'=>false,
            'result'=>$result
        ], 200, [], JSON_PRETTY_PRINT);
    }
    
    public function insertPrediaAcuerdo(Request $request)
    {
        if (is_array($request->input('pago'))) {
            DB::table('predial_acuerdo_pago')->insert($request->input('pago'));
        }
        if (is_array($request->input('pagocampos'))) {
            DB::table('predial_acuerdo_pago_campos')->insert($request->input('pagocampos'));
        }
        if (is_array($request->input('pagocuota'))) {
            DB::table('predial_acuerdo_pago_cuota')->insert($request->input('pagocuota'));
        }
        if (is_array($request->input('pagovigencias'))) {
            DB::table('predial_acuerdo_pago_vigencia')->insert($request->input('pagovigencias'));
        }
        return Response::json([
            'pago'=>$request->input('pago'),
            'pagocampos'=>$request->input('pagocampos'),
            'pagocuota'=>$request->input('pagocuota'),
            'pagovigencias'=>$request->input('pagovigencias'),
        ], 200, [], JSON_PRETTY_PRINT);
    }

}