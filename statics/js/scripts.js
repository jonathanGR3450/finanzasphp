$(document).ready(function(){
    $.fn.select2.defaults.set('language', 'es');
    $('form').bind('submit', function () {
        $(this).find(':input').prop('disabled', false);
    });
    $('#predios').select2({
        placeholder: 'Escriba el numero de predio',
        maximumSelectionLength: 1,
        allowClear: true,
        ajax: {
            url: 'controller/listPredialLike.php',
            delay: 250,
            dataType: "json",
            type: "GET",
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results:data.data,
                    pagination: {
                        more: (params.page * 10) < data.total
                    }
                };
            },
            cache: true,
        }
    });
    //cuando hay cambios en la lista de predios se asigna informacion por defecto
    $("#predios").change(function () {
        let data=$("#predios").select2('data');
        let selected=Object.values(data);
        if (selected.length>0){
            getMatricula(selected[0]['id']);
            getTerceroPredio(selected[0]['id']);
            let selfactura=$("input[name=selectfactura]:checked").val();
            if (selfactura==='factura'){
                s2factura(selected[0]['id'], 'Seleccione el numero de factura');
            }else if (selfactura==='vigencia'){
                s2vigencias(selected[0]['id'], "Seleccione el numero de vigencia");
            }
        }else if (selected.length===0){
            $("#matricula").val(null);
            $("#direccion").val(null);
            $("#contribuyente").val(null).trigger("change");
            $("#factura").val(null).empty().trigger("change");
            $("#vigencias").val(null).empty().trigger("change");
            s2factura(0, 'No hay predio seleccionado');

            s2vigencias(0, "No hay predio seleccionado");
        }
    });
    $("#contribuyente").select2({
        placeholder: 'Escriba el numero cedula',
        allowClear: true,
        ajax: {
            url: 'controller/listTerceroPredio.php',
            delay: 250,
            dataType: 'json',
            type: 'GET',
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results:data.data,
                    pagination: {
                        more: (params.page * data.per_page) < data.total
                    }
                };
            },
            cache: true,
        }

    });
    $("#poseedor").select2({
        placeholder: 'Escriba el numero de cedula',
        allowClear: true,
        ajax: {
            url: "controller/listPoseedor.php",
            delay: 250,
            dataType: "json",
            type: "GET",
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results:data.data,
                    pagination: {
                        more: (params.page * data.per_page) < data.total
                    }
                };
            },
            cache: true
        }
    });
    //para cuando cambia de facturas a vigencias
    $("input[name=selectfactura]").change(function () {
        let data=$("#predios").select2('data');
        let selected=Object.values(data);
            if ($(this).val()==='factura'){
                $("#factura").val(null).prop("disabled", false).empty().trigger("change");
                $("#vigencias").val(null).prop("disabled", true).empty().trigger("change");
                s2vigencias(0, '');
                if (selected.length>0){
                    s2factura(selected[0]['id'], 'Seleccione el numero de factura');
                }
            }else if ($(this).val()==='vigencia'){
                s2factura(0, '');
                $("#factura").select2({ data: null }).prop("disabled", true).empty().trigger("change");
                $("#vigencias").select2({ data: null }).prop("disabled", false).trigger("change");
                if (selected.length>0){
                    s2vigencias(selected[0]['id'], "Seleccione el numero de vigencia");
                }
            }
    });
    $("#factura").select2().change(function () {
        $("#vigencias").val(null).empty().trigger("change");
        let selectfactura=Object.values($(this).select2('data'));
        if (selectfactura.length>0){
            let data=[];
            data.push({"id":selectfactura[0]['id']})
            let json=JSON.stringify(data);
            __ajax("controller/vigenciaFactura.php", {"json":json})
                .done(function (info) {
                    let auxvigencia=$("#vigencias");
                    for (let i=0; i<info.length; i++){
                        let newOption=new Option(info[i]['text'], info[i]['id'], true, true);
                        auxvigencia.append(newOption).trigger("change");
                    }
                    if (auxvigencia.val().length>0){
                        facturaPrint(selectfactura[0]['id']);
                    }
                });
        }else {
            clearTablas();
        }

    });
    $("#vigencias").select2().change(function () {
        if ($(this).val().length>0){
            vigenciaPrint(this)
        }else {
            clearTablas();
        }

    });
    let timeout = null;
    $("#numerocuotas").on("input", function () {
        clearTimeout(timeout);
        timeout=setTimeout(()=>{
            if ($(this).val()>0 && $(this).val().length>0){
                cuotas();
            }
        }, 500)
    });
    $("#calcular").click(function () {
        $("#numerocuotas").val()>0 ? cuotas() : alert("el numero de cuotas debe ser mayor a cero");
    });
    $("#municipio").select2({
        placeholder: 'Seleccione un municipio',
        allowClear: true,
        maximumSelectionLength: 1,
        ajax: {
            url: "controller/getMunicipio.php",
            type: 'GET',
            dataType: 'json',
            data: function (params) {
                return {
                    'q':params.term,
                    'page': params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.data,
                    pagination: {
                        more: (params.page * data.per_page) < data.total
                    }
                };
            },
            cache:true
        }
    });

    //para facturacion
    $("#acuerdopago").select2({
        placeholder: 'Seleccione el numero de acuerdo de pago',
        allowClear: true,
        maximumSelectionLength: 1,
        cache: true,
        ajax: {
            url: 'controller/getAcuerdoPagos.php',
            type: 'GET',
            dataType: 'json',
            data: function (params) {
                return {
                    'q': params.term,
                    'page': params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.data,
                    pagination: {
                        more: (params.page * data.per_page) < data.total
                    }
                };
            }
        },
    }).change(function () {
        if ($(this).val().length>0){
            id=$(this).val();
            facturacion(id[0]);
        }else {
            $('#cuotas').empty().trigger('change');
        }
    });
    $("#cuotas").change(function () {
        if ($(this).val().length>0){
            let cuota = $(this).select2('data');
            if (cuota[0]['text']==="inicial"){
                let id=[{'id':cuota[0]['id']}];
                let json=JSON.stringify(id);
                __ajax('controller/getDebeCuota.php', {'json':json})
                    .done(function (info) {
                        debe=info[0]['debecuota'];
                        $("#monto").prop({ "placeholder":debe, "max":debe});
                    });
            }else{
                let acuerdo=$("#acuerdopago").val();
                let id=[{'id':acuerdo[0]}];
                let json=JSON.stringify(id);
                __ajax('controller/getDebeTotal.php', {'json':json})
                    .done(function (info) {
                        debe=info[0]['debetotal'];
                        $("#monto").prop({ "placeholder":debe, "max":debe});
                    });
            }
        }else {
            $("#monto").prop({ "placeholder":'0', "max":'', 'value':''});
        }
    }).select2({
        placeholder: 'Seleccione la cuota que desea abonar',
        allowClear: true,
        maximumSelectionLength: 1,
        cache: true,
    });
    $("#facturacion").click(function () {
        let montovalor=$("#monto");
        let cuotas=$("#cuotas");
        let fechapago=$("#fechapago");
        if (cuotas.val().length>0 && montovalor.val().length>0 && fechapago.val().length>0){
            let max = montovalor.attr('max');
            let monto=montovalor.val();
            if (monto<=max){
                datos=[];
                datos.push({'cuotas':cuotas.val(), 'monto':montovalor.val(), 'fechapago':fechapago.val()});
                json=JSON.stringify(datos);
                $.ajax({
                    data: {'json':json},
                    type: 'GET',
                    dataType: 'json',
                    url:'controller/setLiquidacion.php',
                }).done(function () {
                    cuotas.empty().trigger('change')
                    $("#acuerdopago").empty().trigger('change')
                    montovalor.val('');
                    fechapago.val("");
                });
            }
            else {
                alert('el monto sobre pasa el valor que debe');
                montovalor.val('');
            }

        }else alert('los campos no deben estar vacios')
    });
    $('#solicitud').click(function () {
        let vigencia=[];
        $("#datosvigencias").find("tr").each(function () {
            aux=[];
            $(this).find("td").find("input").each(function () {
                aux.push($(this).val());
            });
            vigencia.push(aux);
        });
        let cuotas=[];
        $("#datoscuotas").find('tr').each(function () {
            aux=[];
            $(this).find('td').find('input').each(function () {
                aux.push($(this).val());
            });
            cuotas.push(aux);
        });
        let datos=[];
        datos.push({
                'predios':$("#predios").val()[0],
                'contribuyente':$("#contribuyente").val(),
                'poseedor':$("#poseedor").val(),
                'municipio':$("#municipio").val()[0],
                'direccion':$("#direccion").val(),
                'matricula':$("#matricula").val(),
                'vigencias':$("#vigencias").val(),
                'primeracuota':$("#primeracuota").val(),
                'numerocuotas':$("#numerocuotas").val(),
                'vigencia':vigencia,
                'cuotas':cuotas
            });
        let json = JSON.stringify(datos);
            $.ajax({
                url: 'controller/guardarSolicitud.php',
                type: 'get',
                dataType:'json',
                data: {'json':json}
            }).
            done(function () {
                location.reload();
            });
    });

});
//para facturacion
function facturacion(id) {
    data=[];
    data.push({'id':id});
    json=JSON.stringify(data);
    __ajax('controller/getCuotasAcuerdo.php', {'json':json})
    .done(function (info) {
        info.data[0]['text']="inicial";
        $("#cuotas").select2({
            placeholder: 'Seleccione la cuota que desea abonar',
            allowClear: true,
            maximumSelectionLength: 1,
            data: info.data,
            cache: true,
        });
    });
}
function clearTablas() {
    let tablaVigencias=`<tr style="background-color: #aec6ff">
                        <td>Totalizado de Vigencias</td>
                        <td>0</td> <td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>`;
    $("#datosvigencias").html(tablaVigencias);
    let tablaCuotas=`<tr style="background-color: #aec6ff">
                        <td>Total</td><td>0</td>
                        <td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td>
                        <td>0</td><td>0</td></tr>`;
    $("#datoscuotas").html(tablaCuotas);
}
function cuotas() {
    if ($("#vigencias").val().length>0){
        let totales=[];
        let aux=[];
        totales.push({"primeracuota":$("#primeracuota").val()})
        totales.push({"cuotas":$("#numerocuotas").val()});
        $("#totales").find("td").find("input").each(function () {
            aux.push($(this).val());
        });
        aux.shift();
        totales.push({"totales":aux});
        let json=JSON.stringify({"data":totales});
        __ajax("controller/printCuotas.php",{"json":json})
            .done(function (info) {
                let html=tablaCuotas(info['total'], $("#primeracuota").val(), $("#numerocuotas").val());
                $("#datoscuotas").html(html);
            });
    }
}

function tablaCuotas(totalCuotas, porcentaje, numeroCuotas){
    html="";
    count=0;
    for (i=0;i<totalCuotas.length;i++){
        st="";
        if (count===0){
            cuota="Inicial";
            porcentajeCuota=porcentaje;
        }else if (count===totalCuotas.length-1){
            st="style='background-color: #aec6ff'";
            cuota="Total";
            porcentajeCuota="100";
        }else{
            cuota=count;
            porcentajeCuota=Number((100-porcentaje)/numeroCuotas).toFixed(2);
        }
        html+=`<tr ${st}>
            <td>${cuota}</td>
            <td><input type='text' value='${porcentajeCuota}' name='cuotas[${count}][]' hidden>${porcentajeCuota}</td>`;
        for(let j=0; j<totalCuotas[i].length;j++){
            html+=`<td><input type='text' value='${totalCuotas[i][j]}' name='cuotas[${count}][]' hidden>${totalCuotas[i][j]}</td>`;
        }
        i<totalCuotas.length-1 ? html+=`<td><input type='date' name='cuotas[${count}][]'  value=''></td></tr>` : null;
        count++;
    }
    return html;
}

function facturaPrint(id) {
    let data=[];
    data.push({"id": id});
    json=JSON.stringify(data);
    __ajax("controller/printVigenciasFactura.php", {"json":json})
        .done(function (info) {
            tabla=imprimirTablaVigencias(info['tabla']);
            $("#datosvigencias").html(tabla);
    });
}
function imprimirTablaVigencias(datos) {
    html="";
    for (i=0;i<datos.length;i++){
        i===datos.length-1 ? total=`id='totales' style='background-color: #aec6ff'` : total="";
        html+=`<tr ${total}>`;
        for (j=0;j<datos[i].length;j++){
            //j===0 ? t="th" : t="td";
            html+=`<td><input type='text' value='${datos[i][j]}' name='vigencia[${i}][]' hidden>${datos[i][j]}</td>`;
        }
        html+=`</tr>`;
    }
    return html;
}
function vigenciaPrint(data) {
    if (data.length>0){
        let radioselected=$("input[name=selectfactura]:checked").val();
        if (radioselected==="vigencia"){
            let ids=$("#vigencias").val();
            let predios=Object.values($("#predios").val());
            data=[];
            data.push({"vigencias":ids});
            data.push({"predio":predios[0]})
            json=JSON.stringify(data);
            __ajax("controller/printVigencias.php", {"json":json})
                .done(function (info) {
                    res=imprimirTablaVigencias(info['tabla']);
                    $("#datosvigencias").html(res);
                });
        }
    }

}
function s2factura(id, placeholderText) {
    //$("#factura").data('placeholder', placeholderText);
    $("#factura").data('placeholder', placeholderText).select2({
        maximumSelectionLength: 1,
        allowClear: true,
        ajax: {
            url: 'controller/getFactura.php',
            delay: 250,
            dataType: "json",
            type: "GET",
            data: function (params) {
                return {
                    q: params.term,
                    id: id,
                    page: params.page || 1
                }
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results:data.data,
                    pagination: {
                        more: (params.page * 10) < data.total
                    }
                };
            },
        }
    });
}

function s2vigencias(id, placeholderText) {
    $("#vigencias").data('placeholder', placeholderText);
    let data=[];
    data.push({"id":id});
    let json = JSON.stringify(data)
    __ajax("controller/getVigencias.php", {"json":json})
        .done(function (info) {
            $("#vigencias").select2({
                allowClear: true,
                data: info,
            });
        });
}

function getTerceroPredio(id) {
    let data=[];
    data.push({"id":id});
    let json=JSON.stringify(data);
    __ajax("controller/getTerceroPredio.php", {"json":json})
        .done(function (info) {
            let result=Object.values(info);
            let data = {
                id: result[1][0].id,
                text: result[1][0].text
            };
            let newOption = new Option(data.text, data.id, true, true);
            $('#contribuyente').append(newOption).trigger('change');
        });
}
function getMatricula(id) {
    let data=[];
    data.push({"id":id});
    let json = JSON.stringify(data);
    __ajax("controller/listMatricula.php", {"json":json})
        .done(function (info) {
            let res=Object.values(info);
            $("#matricula").val(res[1][0].matricula);
            $("#direccion").val(res[1][0].direccion);
    });
}
function __ajax(url, data) {
    return $.ajax({
        data:data,
        type:"GET",
        dataType:"json",
        url:url
    });
}
