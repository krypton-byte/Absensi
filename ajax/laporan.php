<?php
session_start();
header('content-type: application/json');
require '../modules/databases.php';
if(!(isset($_SESSION['username']) && isset($_SESSION['password']) && isset($_POST['jurusan']))){
    echo json_encode(['status' => false], JSON_PRETTY_PRINT);
    exit();
}   
try{
    (new Administrator($_SESSION['username'], $_SESSION['password']))->Masuk();
    $id = (new Jurusan($_POST['jurusan']))->Kehadiran();
    echo json_encode(['status' => true, 'data' => $id], JSON_PRETTY_PRINT);
}catch(Exception){
    echo json_encode(['status' => false], JSON_PRETTY_PRINT);
}