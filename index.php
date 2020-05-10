<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Modulo Finanzas</title>
    <link rel="stylesheet" href="statics/css/style.css">
    <link rel="stylesheet" href="statics/css/bootstrap.min.css">
    <link rel="stylesheet" href="statics/css/select2.min.css">
</head>
<body onload="listaSolicitud('', 1)">
<div class="container mt-5">
    <h1 class="mb-5">Acuerdos de pago</h1>
    <div class="form-group row">
        <label for="buscar" class="col-md-1 col-form-label">Buscar</label>
        <div class="col-md-4">
            <input type="text" class="form-control" id="buscar">
        </div>
    </div>
    <div class="tabla">
        <table class='table'>
            <thead>
            <tr>
                <th scope='col'>Numero de Acuerdo</th>
                <th scope='col'>Referencia Catastral</th>
                <th scope='col'>Tercero</th>
                <th scope='col'>Vigencias</th>
                <th scope='col'>Cuotas</th>
                <th scope='col'>Total</th>
            </tr>
            </thead>
            <tbody id="listasolicitud"></tbody>
        </table>
    </div>
    <div class="justify-content-center">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center" id="paginacion">
                <li class="page-item">
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
                </li>
            </ul>
        </nav>
    </div>

</div>

<script src="statics/js/jquery-3.5.0.js"></script>
<script src="statics/js/select2.min.js"></script>
<script src="statics/js/es.js"></script>
<script src="statics/js/home.js"></script>
<script src="statics/js/bootstrap.min.js"></script>
</body>
</html>