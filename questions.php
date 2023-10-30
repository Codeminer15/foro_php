<?php
require_once "connection.php";
require_once 'jwt.php';

//validar token jwt
$jwt = apache_request_headers()['Authorization'];
if(strstr($jwt, "Bearer")){
    $jwt = substr($jwt, 7);
}
if(JWT::verify($jwt, Config::SECRET)){
    header("HTTP/1.1 401 Unauthorized");
    exit();
}

//validar token jwt
$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo){
    case 'GET':
        //consulta
        $con = connection();
        if(isset($_GET['id'])){
            $sql = $con->prepare("SELECT * FROM questions WHERE id=:id");
            $sql->bindValue(":id", $_GET['id']);
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_ASSOC);
            $result = $sql->fetchAll();
        }else{
            $sql = $con->prepare("SELECT * FROM questions");
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_ASSOC);
            $result = $sql->fetchAll();
        }
        
        echo json_encode($result);
        break;
    case 'POST':
        //Crear/insertar
        if(isset($_POST['id']) && 
        isset($_POST['question']) && 
        isset($_POST['email']) && 
        isset($_POST['date']) && 
        isset($_POST['category_id'])){
            $con = connection();
            $sql = $con->prepare("INSERT INTO questions VALUES(:i, :q, :e, :d, :t)");
            $sql->bindValue(":i", $_POST['id']);
            $sql->bindValue(":q", $_POST['question']);
            $sql->bindValue(":e", $_POST['email']);
            $sql->bindValue(":d", $_POST['date']);
            $sql->bindValue(":t", $_POST['category_id']);
            $sql->execute();
            echo json_encode(["status"=>"agregado"]);
        }else{
            
            header("HTTP/1.1 400 Bad Request");
        }
        break;
    case 'PUT':
        //Actualizar
        if(isset($_GET['id']) && 
        isset($_GET['question']) && 
        isset($_GET['email']) && 
        isset($_GET['date']) && 
        isset($_GET['category_id'])){
            $con = connection();
            $sql = $con->prepare("UPDATE questions SET question=:q, email=:e, date=:d, category_id=:t WHERE id=:i");
            $sql->bindValue(":i", $_GET['id']);
            $sql->bindValue(":q", $_GET['question']);
            $sql->bindValue(":e", $_GET['email']);
            $sql->bindValue(":d", $_GET['date']);
            $sql->bindValue(":t", $_GET['category_id']);
            $sql->execute();
            echo json_encode(["status"=>"Actualizado"]);
        }else{
            header("HTTP/1.1 400 Bad Request");
        }
        break;
    case 'DELETE':
        //Borrar
        if(isset($_GET['id'])){
            $con = connection();
            $sql = $con->prepare("DELETE FROM questions WHERE id=:i");
            $sql->bindValue(":i", $_GET['id']);
            $sql->execute();
            echo json_encode(["status"=>"Eliminado"]);
        }else{
            header("HTTP/1.1 400 Bad Request");
        }
        break;
    default:
        //error
        header("HTTP/1.1 405 Method Not Allowed");
}