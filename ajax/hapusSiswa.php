<?php
session_start();
header('content-type: application/json');
require '../modules/databases.php';
if(!(isset($_SESSION['username']) && isset($_SESSION['password']) && isset($_POST['nis']))){
    echo json_encode(['status' => false], JSON_PRETTY_PRINT);
    exit();
}
try{
    (new Administrator($_SESSION['username'], $_SESSION['password']))->Masuk();
    $status = (new Siswa($_POST['nis']))->hapus();
    echo json_encode(['status' => boolval($status)], JSON_PRETTY_PRINT);
}catch(Exception $e){
    echo json_encode(['status' => false], JSON_PRETTY_PRINT);
}
?>