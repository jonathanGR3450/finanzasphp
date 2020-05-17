<?php
    
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Modulo</title>
    <link rel="stylesheet" href="statics/css/style.css">
    <link rel="stylesheet" href="statics/css/bootstrap.min.css">
    <link rel="stylesheet" href="statics/css/select2.min.css">
</head>
<body>
<div class="container mt-5">
    <form method="post" action="controller/guardarSolicitud.php">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="predios">Predio</label>
                <select class="form-control" id="predios" name="predios" multiple="multiple"></select>
            </div>
            <div class="form-group col-md-6">
                <label for="contribuyente">Contribuyente</label>
                <select class="form-control" id="contribuyente" name="contribuyente[]" multiple="multiple"></select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="poseedor">Poseedor</label>
                <select class="form-control" name="poseedor[]" id="poseedor" multiple="multiple"></select>
            </div>
            <div class="form-group col-md-6">
                <label for="municipio">Municipio</label>
                <select class="form-control" name="municipio" id="municipio" multiple="multiple"></select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="direccion">Dirección de notificacion</label>
                <input type="text" class="form-control" name="direccion" id="direccion">
            </div>
            <div class="form-group col-md-6">
                <label for="matricula">Matricula Inmobiliaria</label>
                <input type="text" id="matricula" name="matricula" value="" class="form-control">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-2">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="selectfactura" id="declaracion" value="factura" checked>
                    <label class="form-check-label" for="declaracion">
                        Numero de Factura
                    </label>
                </div>
            </div>
            <div class="form-group col-md-2">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="selectfactura" id="nodeclaracion" value="vigencia">
                    <label class="form-check-label" for="nodeclaracion">
                        Vigencias
                    </label>
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="factura">Numero de Factura</label>
                <select class="form-control" name="factura" id="factura" data-placeholder="No hay predio seleccionado" multiple="multiple"></select>
            </div>
            <div class="form-group col-md-6">
                <label for="vigencias">Vigencias</label>
                <select class="form-control" name="vigencias[]" id="vigencias" data-placeholder="No hay predio seleccionado" multiple="multiple" disabled></select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="primeracuota">Porcentaje Primera Cuota</label>
                <input type="number" min="30" max="100" value="30" class="form-control" id="primeracuota" name="primeracuota">
            </div>
            <div class="form-group col-md-6">
                <label for="numerocuotas">Numero de Cuotas</label>
                <input type="number" min="0" max="1000" value="0" class="form-control" id="numerocuotas" name="numerocuotas">
            </div>
        </div>
        <div class="form-group">
            <button type="button" class="btn btn-primary" id="calcular">Calcular Cuotas</button>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="numeroresolucion">Numero de Resolución</label>
                <input type="text" name="numeroresolucion" class="form-control" id="numeroresolucion" disabled>
            </div>
            <div class="form-group col-md-6">
                <label for="fecharesolucion">Fecha de Resolución</label>
                <input type="text" name="fecharesolucion" class="form-control" id="fecharesolucion" disabled>
            </div>
        </div>
        <hr>
        <div class="form-row">
            <div class="form-group col-md-12">
                <p>Resumen vigencias</p>
                <table class="table">
                    <thead style="background-color: #149dff">
                    <tr >
                        <th scope="col">Vigencia</th>
                        <th scope="col">Impuesto</th>
                        <th scope="col">Interes</th>
                        <th scope="col">Sobretasa</th>
                        <th scope="col">Interes Sobretasa Ambiental</th>
                        <th scope="col">Otros Valores</th>
                        <th scope="col">Total</th>
                    </tr>
                    </thead>
                    <tbody id="datosvigencias">
                    <tr style="background-color: #aec6ff">
                        <th scope="row">Totalizado de Vigencias</th>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="form-row">
            <div class="form-group col-md-12">
                <p>Discriminar Cuotas</p>
                <table class="table">
                    <thead style="background-color: #149dff">
                    <tr >
                        <th scope="col"># Cuota</th>
                        <th scope="col">% Pago</th>
                        <th scope="col">Valor Capital</th>
                        <th scope="col">Valor Interes</th>
                        <th scope="col">Valor Sobretasa</th>
                        <th scope="col">Valor Sobretasa Interes</th>
                        <th scope="col">Otros</th>
                        <th scope="col">Valor Total</th>
                        <th scope="col">Interes Plazo</th>
                        <th scope="col">Total</th>
                        <th scope="col">Fecha de Pago</th>
                    </tr>
                    </thead>
                    <tbody id="datoscuotas">
                    <tr style="background-color: #aec6ff">
                        <th scope="row">Total</th>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" id="solicitud">Solicitud</button>
    </form>
    <hr>
    <form>
        <h2>Facturacion</h2>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="acuerdopago">Seleccione el acuerdo de pago</label>
                <select class="form-control" name="acuerdopago" id="acuerdopago" multiple="multiple"></select>
            </div>
            <div class="form-group col-md-6">
                <label for="cuotas">Seleccione la cuota</label>
                <select class="form-control" id="cuotas" name="cuotas" multiple="multiple"></select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="monto">Ingrese el monto</label>
                <input class="form-control" type="number" name="monto" id="monto" min="0" max="" value="" placeholder="0">
            </div>
            <div class="form-group col-md-6">
                <label for="fechapago">Seleccione la fecha de pago</label>
                <input class="form-control" type="date" name="fechapago" id="fechapago">
            </div>
        </div>
        <div class="form-group">
            <button type="button" class="btn btn-primary" id="facturacion">Facturar</button>
        </div>
    </form>
</div>

<script src="statics/js/jquery-3.5.0.js"></script>
<script src="statics/js/select2.min.js"></script>
<script src="statics/js/es.js"></script>
<script src="statics/js/scripts.js"></script>
<script src="statics/js/bootstrap.min.js"></script>
</body>
</html>