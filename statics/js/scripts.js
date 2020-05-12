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
        var data=$("#predios").select2('data');
        var selected=Object.values(data);
        if (selected.length>0){
            getMatricula(selected[0]['id']);
            getTerceroPredio(selected[0]['id']);
            var selfactura=$("input[name=selectfactura]:checked").val();
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
        var data=$("#predios").select2('data');
        var selected=Object.values(data);
            if ($(this).val()==='factura'){
                $("#factura").val(null).empty().trigger("change");
                $("#vigencias").val(null).empty().trigger("change");
                s2vigencias(0, '');
                $("#vigencias").prop("disabled", true).trigger("change");
                $("#factura").prop("disabled", false).trigger("change");
                if (selected.length>0){
                    console.log("hay pedio seleciono factura")
                    s2factura(selected[0]['id'], 'Seleccione el numero de factura');
                }
            }else if ($(this).val()==='vigencia'){
                s2factura(0, '');
                $("#factura").val(null).empty().trigger("change");
                $("#factura").select2({ data: null }).prop("disabled", true).trigger("change");
                $("#vigencias").select2({ data: null }).prop("disabled", false).trigger("change");
                console.log("elimina vigencias?")
                if (selected.length>0){
                    console.log("hay pedio seleciono vigencia")
                    s2vigencias(selected[0]['id'], "Seleccione el numero de vigencia");
                }
            }
    });
    $("#factura").change(function () {
        $("#vigencias").val(null).empty().trigger("change");
        var selectfactura=Object.values($(this).select2('data'));
        if (selectfactura.length>0){
            var data=[];
            data.push({"id":selectfactura[0]['id']})
            var json=JSON.stringify(data);
            __ajax("controller/vigenciaFactura.php", {"json":json})
                .done(function (info) {
                    for (var i=0; i<info.length; i++){
                        var newOption=new Option(info[i]['text'], info[i]['id'], true, true);
                        $("#vigencias").append(newOption).trigger("change");
                    }
                    if ($("#vigencias").val().length>0){
                        facturaPrint(selectfactura[0]['id']);
                    }
                });
        }else {
            clearTablas();
        }

    });
    $("#factura").select2();
    $("#vigencias").select2();
    $("#vigencias").change(function () {
        if ($(this).val().length>0){
            vigenciaPrint(this)
        }else {
            clearTablas();
        }

    });
    var timeout = null;
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
});
function clearTablas() {
    var tablaVigencias=`<tr style="background-color: #aec6ff">
                        <th scope="row">Totalizado de Vigencias</th>
                        <td>0</td> <td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>`;
    $("#datosvigencias").html(tablaVigencias);
    var tablaCuotas=`<tr style="background-color: #aec6ff">
                        <th scope="row">Total</th><td>0</td>
                        <td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td>
                        <td>0</td><td>0</td></tr>`;
    $("#datoscuotas").html(tablaCuotas);
}
function cuotas() {
    if ($("#vigencias").val().length>0){
        var totales=[];
        var aux=[];
        totales.push({"primeracuota":$("#primeracuota").val()})
        totales.push({"cuotas":$("#numerocuotas").val()});
        $("#totales").find("td").find("input").each(function () {
            aux.push($(this).val());
        });
        totales.push({"totales":aux});
        var json=JSON.stringify({"data":totales});
        __ajax("controller/printCuotas.php",{"json":json})
            .done(function (info) {
                var html=tablaCuotas(info['total'], $("#primeracuota").val(), $("#numerocuotas").val());
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
            <th scope='row'>${cuota}</th>
            <td><input type='text' value='${porcentajeCuota}' name='cuotas[${count}][]' hidden>${porcentajeCuota}</td>`;
        for(var j=0; j<totalCuotas[i].length;j++){
            html+=`<td><input type='text' value='${totalCuotas[i][j]}' name='cuotas[${count}][]' hidden>${totalCuotas[i][j]}</td>`;
        }
        html+=`<td><input type='date' name='cuotas[${count}][]'  value=''></td></tr>`;
        count++;
    }
    return html;
}

function facturaPrint(id) {
    var data=[];
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
            j===0 ? t="th" : t="td";
            html+=`<${t}><input type='text' value='${datos[i][j]}' name='vigencia[${i}][]' hidden>${datos[i][j]}</${t}>`;
        }
        html+=`</tr>`;
    }
    return html;
}
function vigenciaPrint(data) {
    if (data.length>0){
        var radioselected=$("input[name=selectfactura]:checked").val();
        if (radioselected==="vigencia"){
            var ids=$("#vigencias").val();
            var predios=Object.values($("#predios").val());
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
    $("#factura").data('placeholder', placeholderText);
    $("#factura").select2({
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
    var data=[];
    data.push({"id":id});
    var json = JSON.stringify(data)
    __ajax("controller/getVigencias.php", {"json":json})
        .done(function (info) {
            $("#vigencias").select2({
                allowClear: true,
                data: info,
            });
        });
}

function getTerceroPredio(id) {
    var data=[];
    data.push({"id":id});
    var json=JSON.stringify(data);
    __ajax("controller/getTerceroPredio.php", {"json":json})
        .done(function (info) {
            var result=Object.values(info);
            var data = {
                id: result[1][0].id,
                text: result[1][0].text
            };
            var newOption = new Option(data.text, data.id, true, true);
            $('#contribuyente').append(newOption).trigger('change');
        });
}
function getMatricula(id) {
    var data=[];
    data.push({"id":id});
    var json = JSON.stringify(data);
    __ajax("controller/listMatricula.php", {"json":json})
        .done(function (info) {
            var res=Object.values(info);
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
    })
}
