<?php
require_once("connection.php");

$con = connection();
if ($con)
    echo "Conexión exitosa";