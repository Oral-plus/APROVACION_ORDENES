<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
 
    <title>Aprobacion ordenes</title>
    <link rel="icon" type="image/x-icon" href="./imagenes/logosas.png">
    <link rel="stylesheet" href="./css/custom.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Old+Standard+TT:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="ruta/a/jquery.js"></script>
    <script src="ruta/a/jquery-ui.js"></script>
    <link rel="stylesheet" href="ruta/a/jquery-ui.css">
<style>
    body {
            margin: 0;
            padding: 0;
        }

        .imagen {
            text-align: center;
        }

        .scrollable-table {
            overflow: auto;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        input[type="date"],
        input[type="submit"] {
            margin: 10px 0;
        }

</style>
</head>
<body>
<form action="" method="post" class="formulario">
    <div class="fila">
        <div class="imagen">
            <img src="./imagenes/logosas.png" alt="valida tu conexión">
        </div>
        <div class="center">
        <div class="campo">
            <label for="inicio">Fecha de inicio  :  </label>
            <input type="text" name="inicio" id="inicio" placeholder="AAAA-MM-DD" >
             <img class="calendario" src="./imagenes/calendario.gif" alt="">
        </div>
        <div class="campo">
            <label for="final">Fecha final:</label>
            <input type="text" name="final" id="final" placeholder="AAAA-MM-DD">
            <img class="calendario" src="./imagenes/calendario.gif" alt="">
        </div>
    </div>
    
    <div class="fila">
        <div class="campo">
            <label for="estado">Estado:</label>
            <select name="estado" id="estado">
                <option value="">Seleccionar Estado</option>
                <option value="aprobado">Aprobado</option>
                <!-- Puedes agregar más opciones según tus necesidades -->
            </select>
        </div>
        <div class="campo">
            <label for="doc_num">Número de Orden:</label>
            <input type="text" name="doc_num" id="doc_num">
        </div> </div>
        <div class="campo">
            <input type="submit" name="Consultar" value="Consultar">
        </div>
    </div>
</form>

<script>
  $(function() {
    $("#inicio").datepicker({ dateFormat: 'yy-mm-dd' });
    $("#final").datepicker({ dateFormat: 'yymmdd' });
  });
</script>
<script>
    function abrirVentana(docNum) {
        // Define la URL de la ventana emergente, puedes ajustarla según tus necesidades
        var urlVentana = 'ventana_emergente.php?docNum=' + docNum;

        // Abre la ventana emergente
        window.open(urlVentana, 'VentanaEmergente', 'width=600,height=400');
    }
</script>

<?php
require_once "./php/conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si los índices están definidos
    $fecha_inicial = isset($_POST["inicio"]) ? $_POST["inicio"] : "";
    $fecha_final = isset($_POST["final"]) ? $_POST["final"] : "";
    $estado = isset($_POST["estado"]) ? $_POST["estado"] : "";
    $doc_num = isset($_POST["doc_num"]) ? $_POST["doc_num"] : "";

    // Construir la condición de la consulta SQL
    $condicion_fecha = !empty($fecha_inicial) && !empty($fecha_final) ? "AND OPOR.DocDate BETWEEN '$fecha_inicial' AND '$fecha_final'" : "";
    $condicion_estado = !empty($estado) ? "AND OPOR.U_Aprovacion = '$estado'" : "";
    $condicion_doc_num = !empty($doc_num) ? "AND OPOR.DocNum = '$doc_num'" : "";

    $consulta = sqlsrv_query($conn, "SELECT DISTINCT
                                    OPOR.DocNum,
                                    OPOR.CardCode,
                                    OPOR.CardName,
                                    OPOR.DocDate,
                                    (SELECT SUM(POR1.Quantity * POR1.PriceBefDi) FROM POR1 WHERE OPOR.DocEntry = POR1.DocEntry) AS precio_total,
                                    OPOR.U_Aprovacion
                                 FROM
                                    OPOR 
                                 WHERE
                                    1=1 $condicion_fecha $condicion_estado $condicion_doc_num");


    echo "<form action='' method='post'>";
    echo "<div class='scrollable-table'>";
    echo "<div style='display: inline-block; margin-left: 45%; text-align: center;'>";
    echo "<input type='submit' name='Actualizar' value='APROBAR'>";
    echo "</div>";
    echo "<table>";
    echo "<tr>
    <th>Articulos</th>
            <th>Numero de orden</th>
            <th>Codigo del cliente</th>
            <th>Nombre del cliente</th>
            <th>Fecha</th>
            <th>Total del documento</th>
            <th>Estado</th>
            <th>Aprobar</th>
        </tr>";

        while ($fila = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td><i class='far fa-caret-square-right' style='font-size:22px;color:#4caf50; cursor: pointer;' onclick='abrirVentana(" . $fila["DocNum"] . ")'></i></td>";
            echo "<td>" . $fila["DocNum"] . "</td>";
            echo "<td>" . $fila["CardCode"] . "</td>";
            echo "<td>" . $fila["CardName"] . "</td>";
            echo "<td>" . $fila["DocDate"]->format('Y-m-d') . "</td>";
             // Imprimir la columna precio_total
             echo "<td>" . number_format($fila["precio_total"], 2, '.', ',') . "</td>";
            echo "<td>" . $fila["U_Aprovacion"] . "</td>";
            echo "<td><input type='checkbox' name='actualizar[]' value='" . $fila["DocNum"] . "'></td>";
            echo "</tr>";
        }
        

    echo "</table>";

    echo "</div>";
    echo "<br>";

    echo "</form>";

    sqlsrv_free_stmt($consulta);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["Actualizar"])) {
    $actualizar = $_POST["actualizar"];

    foreach ($actualizar as $orden) {
        // Actualiza el campo U_Aprovacion a 'aprobado' para las órdenes seleccionadas
        $updateQuery = sqlsrv_query($conn, "UPDATE OPOR SET U_Aprovacion = 'aprobado' WHERE DocNum = '$orden'");
    }

    // Redirige o muestra un mensaje de éxito
    // header("Location: tu_pagina.php");
    echo '<script>alert("Aprobación exitosa.");</script>';
}

sqlsrv_close($conn);
?>

</body>
</html>