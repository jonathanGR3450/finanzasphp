$(document).ready(function () {

});

function listaSolicitud(busqueda, pagina, min=1, max=20) {
    datos=[];
    datos.push({'page':pagina});
    datos.push({'numero':busqueda})
    datos=JSON.stringify(datos);
    $.ajax({
        url: "controller/homeController.php",
        type: 'POST',
        dataType: 'json',
        data: {'data':datos}
    }).done(function (info) {
        var total=info.total;
        var datos = Object.values(info.data);
        var tabla="";
        for (var i=0;i<datos.length;i++){
            var x=Object.values(datos[i]);
            tabla+=`<tr>`;
            for (var j=0;j<x.length;j++){
                j==3 ? x[j]=justificarVigencias(x[j]) : '' ;
                tabla+=`<td>${x[j]}</td>`
            }
            tabla+=`</tr>`;
        }
        $("#listasolicitud").html(tabla);

        //para paginacion
        //para validar si esta en la ultima pagina de la lista
        var paginacion="";
        $('#buscar').val().length>0 ? buscar=$('#buscar').val() : buscar="";
        var numeropages=Math.ceil(total/datos.length);
        if(numeropages<20){
            min=1;
            max=datos.length;
        }
        //para el extremo izquierdo
        paginacion+=`<li class="page-item">
                        <a class="page-link" href="javascript:void(0)" onclick="listaSolicitud('${buscar}', '${1}', ${1}, ${20})" aria-label="Previous">
                        <span aria-hidden="true">&laquo;Inicio</span>
                    </a>
                    </li>`;
        if (min>20){
            if (max-min<20){
                auxmin=min-20;
                auxmax=min-1;
            }else {
                auxmin=min-20;
                auxmax=max-20;
            }
            paginacion+=`<li class="page-item">
                            <a class="page-link" href='javascript:void(0)' onclick="listaSolicitud('${buscar}', '${auxmin}', ${auxmin}, ${auxmax})" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>`;
        }else{
            paginacion+=`<li class="page-item disabled">
                            <a class="page-link" href='javascript:void(0)' aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>`;
        }

        for (var i=min;i<=max;i++){
            i==pagina ? acti="active" : acti="";
            paginacion+=`<li class='page-item ${acti}'><a class='page-link' href='javascript:void(0)' 
                        onclick="listaSolicitud('${buscar}', '${i}', ${min}, ${max})">${i}</a></li>`;
        }
        //para validar si va a las ultimas paginas
        var ultimo=Math.ceil(info.last_page/20);
        ultimo=ultimo*20-info.last_page;
        //max>=info.last_page ? console.log(info.last_page) : console.log("no se pasa");
        //para el extremo derecho
        if (min+20<=info.last_page){
            if (max>=info.last_page){
                auxmin1=min+20;
                auxmax1=info.last_page;
            }else {
                auxmin1=min+20;
                auxmax1=max+20;
            }
            paginacion+=`<li class="page-item">
            <a class="page-link" href="javascript:void(0)" onclick="listaSolicitud('${buscar}', '${auxmin1}', ${auxmin1}, ${auxmax1})" aria-label="Next">
            <span aria-hidden="true">&raquo;</span>
            </a></li>`;
        }else {
            paginacion+=`<li class="page-item disabled">
            <a class="page-link" href="javascript:void(0)" aria-label="Next">
            <span aria-hidden="true">&raquo;</span>
            </a></li>`;
        }
        paginacion+=`<li class="page-item">
            <a class="page-link" href="javascript:void(0)" onclick="listaSolicitud('${buscar}', '${info.last_page-ultimo}', ${info.last_page-ultimo}, ${info.last_page})" aria-label="Next">
            <span aria-hidden="true">fin&raquo;</span>
            </a>
            </li>`;
        $("#paginacion").html(paginacion);

    /*<li class="page-item">
            <a class="page-link" href="#" aria-label="Previous">
            <span aria-hidden="true">&laquo;Inicio</span>
        </a>
        </li>
        <li class="page-item">
            <a class="page-link" href="#" aria-label="Previous">
            <span aria-hidden="true">&laquo;</span>
        </a>
        </li>
        <li class="page-item"><a class="page-link" href="#">1</a></li>
        <li class="page-item"><a class="page-link" href="#">2</a></li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
        <li class="page-item">
            <a class="page-link" href="#" aria-label="Next">
            <span aria-hidden="true">&raquo;</span>
        </a>
        </li>
        <li class="page-item">
            <a class="page-link" href="#" aria-label="Next">
            <span aria-hidden="true">fin&raquo;</span>
        </a>
        </li>*/

    });

    function justificarVigencias(vigencias) {
        var vig=vigencias.split(',');
        var str="";
        for (i=1;i<=vig.length;i++){
            str+=vig[i-1]+", ";
            i%3==0 ? str+="<br>" : '';
        }
        return str;
    }
}