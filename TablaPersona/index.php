<?php
include_once "Persona.php";

$lista = [ 
$p = new persona("Daniel", "2000-11-02", "5523456789"),
$p = new persona("Jose", "2004-10-01", "5568912345"),
$p = new persona("Rogelio", "2000-12-02", "5523456789"),
$p = new persona("David", "2000-11-02", "5523456789"),
$p = new persona("Leonardo", "2000-11-02", "5523456789"),
$p = new persona("Miguel", "2000-11-02", "5523456789"),
$p = new persona("Katy", "2000-11-02", "5523456789"),
$p = new persona("Ana", "2000-11-02", "5523456789"),
$p = new persona("Paula", "2000-11-02", "5523456789"),
$p = new persona("Viridiana", "2000-11-02", "5523456789"),
];

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="uft-8">
        <h2>Lake18-hunter<h2>
        <style>
            td {
                background-color:cyan;
            }
        </style>
    </head>
    <body>
    <h1>Lista de Personas</h1>
    <table border>
        <thead>
        <tr>
                <th>Nombre</th>
                <th>Fecha Nacimiento</th>
                <th>Edad actual</th>
                <th>Telefono</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($lista as $p): ?>
        <tr>
                <td><?php echo $p->getNombre(); ?></td>
                <td><?php echo $p->getFecNac(); ?></td>
                <td><?php echo date_diff(date_create($p->getFecNac()), date_create('today'))->y; ?></td>
                <td><?php echo $p->getTel(); ?></td>
            </tr>
            <?php endforeach?>
        </tbody>
    </table>
    </body>
</html>