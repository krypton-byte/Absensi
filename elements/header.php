<?php
session_start();
if(isset($_SESSION['username']) && isset($_SESSION['password'])){
  try{
    $admin = new Administrator($_SESSION['username'], $_SESSION['password']);
    $admin->Masuk();
  }catch(Exception $e){
    unset($_SESSION['username']);
    unset($_SESSION['password']);
  }
}else {
  header('location: index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title> <?php echo $TITLE ?> | ABSENSI</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="AdminLTE/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="plugins/jquery-nice-select-1.1.0/css/nice-select.css">
  <link rel="stylesheet" href="AdminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="AdminLTE/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
</head>
<body class="hold-transition sidebar-mini">
  <!-- jQuery -->
<script src="AdminLTE/plugins/jquery/jquery.min.js"></script>
<script src="plugins/jquery-nice-select-1.1.0/js/jquery.nice-select.min.js"></script>
<!-- Bootstrap 4 -->
<script src="AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="AdminLTE/dist/js/adminlte.min.js"></script>

<script src="AdminLTE/plugins/sweetalert2/sweetalert2.all.js"></script>
<!-- Site wrapper -->
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="AdminLTE/index3.html" class="brand-link">
      <img src="AdminLTE/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Absensi</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="AdminLTE/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Administrator</a>
        </div>
      </div>

      <!-- SidebarSearch Form -->

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul id="navx" class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-header">DASHBOARD</li>
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link">
            <i class="nav-icon fas fa-chart-line"></i>
            <p>Home</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="siswa.php" class="nav-link">
              <i class="nav-icon fas fa-user"></i>
              <p>Siswa</p>
            </a>
          </li>
          <li class="nav-item"><a href="keluar.php" class="nav-link"><i class="nav-icon fas fa-door-closed"></i><p>
            Keluar
          </p></a></li>
          <li class="nav-header">KELAS</li>
          <li class="nav-item">
            <a class="nav-link" id="tkelas">
                <i class="nav-icon fas fa-plus"></i>
                <p>Tambah Kelas</p>
            </a>
          </li>
          <?php 
            foreach((new Administrator($_SESSION['username'], $_SESSION['password']))->Kelas() as $kelas){
                echo '
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-chalkboard-user"></i>
                        <p>
                            '.htmlspecialchars($kelas).'
                        </p>
                    </a>
                    <ul class="nav nav-treeview" id="navx'.htmlspecialchars($kelas).'">
                    <li class="nav-item">
                        <a href="javascript:tambahJurusan(\''.htmlspecialchars($kelas).'\')" class="nav-link">
                            <i class="nav-icon fas fa-plus"></i>
                            <p>Tambah Jurusan</p>
                        </a>
                      </li>';
                            foreach((new Kelas($kelas))->Jurusan() as $jurusan){
                                echo '
                                <li class="nav-item">
                                    <a href="kelas.php?id='.$jurusan['id'].'" class="nav-link">
                                        <i class="nav-icon fa fa-bookmark"></i>
                                        <p>'.htmlspecialchars($jurusan['nama']).'</p>
                                    </a>
                                </li>';
                            }
                        echo '
                    </ul>
                </li>';
            }
          ?>
          <!-- <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>
                10
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
              <a href="http://" class="nav-link">
                  <i class="nav-icon fas fa-plus"></i>
                  <p>Tambah Jurusan</p>
              </a>
            </li>
            </ul>
          </li> -->
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
<script>
function tambahJurusan(kelas){
  Swal.fire({
  title: 'Masukan Nama Jurusan',
  input: 'text',
  inputAttributes: {
    autocapitalize: 'off'
  },
  showCancelButton: true,
  confirmButtonText: 'Tambah',
  cancelButtonText:'Batal',
  showLoaderOnConfirm: true,
  preConfirm: (jurusan) => {
    const form = new FormData();
    form.append('kelas', kelas);
    form.append('jurusan', jurusan)
    return fetch('ajax/tambahJurusan.php', {
      method: 'POST',
      body:form
    })
      .then(response => {
        if (!response.ok) {
          throw new Error(response.statusText)
        }
        return response.json()
      })
      .catch(error => {
        Swal.showValidationMessage(
          `Request failed: ${error}`
        )
      })
  },
  allowOutsideClick: () => !Swal.isLoading()
}).then((result) => {
  if (result.isConfirmed) {
    Swal.fire({
      position: 'top-end',
      icon: result.value.status?'success':'error',
      title: (result.value.status?'Berhasil':'Gagal')+' ditambahkan',
      showConfirmButton: false,
      timer: 1500
    });
    if(result.value.status){
      const navx=`<li class="nav-item">
                                    <a href="kelas.php?id='${result.value.id}'" class="nav-link">
                                        <i class="nav-icon fas fa-certificate"></i>
                                        <p>${result.value.jurusan}</p>
                                    </a>
                                </li>`;
      $(`#navx${kelas}`).append(navx);
    }
  }
})
}
document.getElementById('tkelas').onclick=function (){
  Swal.fire({
  title: 'Masukan Nama Kelas',
  input: 'text',
  inputAttributes: {
    autocapitalize: 'off'
  },
  showCancelButton: true,
  confirmButtonText: 'Tambah',
  cancelButtonText:'Batal',
  showLoaderOnConfirm: true,
  preConfirm: (kelas) => {
    const form = new FormData();
    form.append('kelas', kelas);
    return fetch('ajax/tambahKelas.php', {
      method: 'POST',
      body:form
    })
      .then(response => {
        if (!response.ok) {
          throw new Error(response.statusText)
        }
        return response.json()
      })
      .catch(error => {
        Swal.showValidationMessage(
          `Request failed: ${error}`
        )
      })
  },
  allowOutsideClick: () => !Swal.isLoading()
}).then((result) => {
  if (result.isConfirmed) {
    Swal.fire({
      position: 'top-end',
      icon: result.value.status?'success':'error',
      title: (result.value.status?'Berhasil':'Gagal')+' ditambahkan',
      showConfirmButton: false,
      timer: 1500
    });
    if(result.value.status){
      const navx=`<li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            ${result.value.kelas}
                        </p>
                    </a>
                    <ul class="nav nav-treeview" id="navx${result.value.kelas}" style="display: none;">
                    <li class="nav-item">
                      <a href="javascript:tambahJurusan('${result.value.kelas}')" class="nav-link">
                              <i class="nav-icon fas fa-plus"></i>
                              <p>Tambah Jurusan</p>
                          </a>
                      </li>
                    </ul>
                </li>`
      $('#navx').append(navx);
    }
  }
})
}

</script>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content mt-3">

      <!-- Default box -->