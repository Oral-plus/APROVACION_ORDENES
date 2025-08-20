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
            margin: 50px;
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



<?php
// ventana_emergente.php

// Incluye el archivo de conexión
require_once "./php/conexion.php";

// Obtén el DocNum de la URL
$docNum = isset($_GET['docNum']) ? $_GET['docNum'] : '';

// Validación adicional si es necesario

// Realiza la consulta para obtener la información específica del DocNum
$consultaDetalle = sqlsrv_query($conn, "SELECT POR1.LineTotal, OPOR.DocNum, OPOR.CardCode, OPOR.CardName, POR1.Quantity, OPOR.DocDate,  POR1.ItemCode, OITM.ItemName, OPOR.BaseAmnt, OPOR.U_Aprovacion
FROM OPOR INNER JOIN POR1 ON OPOR.[DocEntry] = POR1.[DocEntry] INNER JOIN OITM  ON POR1.[ItemCode] = OITM.[ItemCode] WHERE DocNum = '$docNum'");

if ($consultaDetalle === false) {
    die(print_r(sqlsrv_errors(), true));
}
echo "<table>";
echo "<tr>

    <th>Codigo del producto</th>
    <th>Descripcion de producto</th>
    <th>Cantidad por articulo</th>
    <th>Valor total </th>
</tr>";

while ($filaDetalle = sqlsrv_fetch_array($consultaDetalle, SQLSRV_FETCH_ASSOC)) {
    echo "<tr>";

    echo "<td>" . $filaDetalle["ItemCode"] . "</td>";
    echo "<td>" . $filaDetalle["ItemName"] . "</td>";
    $base = number_format($filaDetalle["Quantity"], 0, '.', ',');
            echo "<td>" . $base . "</td>";
    $baseAmntFormatted = number_format($filaDetalle["LineTotal"], 0, '.', ',');
            echo "<td>" . $baseAmntFormatted . "</td>";
    
    echo "</tr>";
}

echo "</table>";

// Resto del código...

sqlsrv_free_stmt($consultaDetalle);
sqlsrv_close($conn);
?>

</body>
</html>