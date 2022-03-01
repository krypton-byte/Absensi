<?php
session_start();
header('content-type: application/json');
require '../modules/databases.php';
if(!(isset($_SESSION['username']) && isset($_SESSION['password']) && isset($_POST['nis']) && isset($_POST['absen']))){
    echo json_encode(['status' => false], JSON_PRETTY_PRINT);
    exit();
}
try{
    (new Administrator($_SESSION['username'], $_SESSION['password']))->Masuk();
    (new Siswa($_POST['nis']))->Absen($_POST['absen']);
    echo json_encode(['status' => true], JSON_PRETTY_PRINT);
}catch(Exception){
    echo json_encode(['status' => false], JSON_PRETTY_PRINT);
}
?>