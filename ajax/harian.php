<?php
session_start();
header('content-type: application/json');
require '../modules/databases.php';
if(!(
    isset($_SESSION['username']) &&
    isset($_SESSION['password']) &&
    isset($_POST['jurusan']))){
        echo json_encode(['status' => false], JSON_PRETTY_PRINT);
        exit();
}
try{
    (new Administrator($_SESSION['username'], $_SESSION['password']))->Masuk();
    $info = (new Jurusan(intval($_POST['jurusan'])))->siswa();
    $x=0;
    foreach($info as $e){
        $info[$x++]['kehadiran'] = (new Siswa($e['NIS']))->kehadiranHariIni();
    }
    echo json_encode($info, JSON_PRETTY_PRINT);
}catch(Exception){
    echo json_encode([], JSON_PRETTY_PRINT);
}