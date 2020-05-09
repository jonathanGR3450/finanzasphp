$(document).ready(function () {
    //evento click
    $("#prueba").click(function () {
        loadData("http://apifinanzas.com/api/v1/Predial", myFunction);
    });

    //evento para el input
    $("#ref").keyup(function () {
        referencias(this.value);
    });
});

//llama la peticion cada vez que escribe algo
function referencias(ref) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200){
            document.getElementById("result").innerHTML = xhttp.responseText;
        }
    }
    xhttp.open("GET", "http://apifinanzas.com/api/v1/buscarPredial?ref="+ref, true);
    xhttp.send();
}

//metodo que hace la peticion ajax
function loadData(url, cFunction) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            cFunction(this);
        }
    };
    xhttp.open("GET", url, true);
    xhttp.send();
}

//este es el metodo que me pinta el html
function myFunction(xhttp) {
    document.getElementById("demo").innerHTML = xhttp.responseText;
}