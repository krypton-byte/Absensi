<?php
session_start();
header('content-type: application/json');
require '../modules/databases.php';
if(!(isset($_SESSION['username']) && isset($_SESSION['password']) && isset($_POST['jurusan']) && isset($_POST['kelas']))){
    echo json_encode(['status' => false], JSON_PRETTY_PRINT);
    exit();
}
try{
    (new Administrator($_SESSION['username'], $_SESSION['password']))->Masuk();
    $id = (new Kelas($_POST['kelas']))->tambahJurusan($_POST['jurusan']);
    echo json_encode(['status' => true, 'id' => $id, 'kelas' => $_POST['kelas'], 'jurusan' =>$_POST['jurusan']], JSON_PRETTY_PRINT);
}catch(Exception){
    echo json_encode(['status' => false], JSON_PRETTY_PRINT);
}
?>