<?php
session_start();
header('content-type: application/json');
require '../modules/databases.php';
if(!(isset($_SESSION['username']) && isset($_SESSION['password']) && isset($_POST['kelas']))){
    echo json_encode(['status' => false], JSON_PRETTY_PRINT);
    exit();
}
try{
    (new Administrator($_SESSION['username'], $_SESSION['password']))->Masuk();
    $id = (new Kelas($_POST['kelas']))->tambahKelas();
    echo json_encode(['status' => true, 'kelas' => $_POST['kelas']]);
}catch(Exception){
    echo json_encode(['status' => false], JSON_PRETTY_PRINT);
}
?>