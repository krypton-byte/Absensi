<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Absensi | Administrator</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="AdminLTE/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="AdminLTE/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="AdminLTE/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href=""><b>Absensi</b> WEB</a>
  </div>
  <!-- /.login-logo -->
  <div class="card border border-info">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Login Administrator</p>
      <form action="" method="post">
      <?php
      session_start();
      require 'modules/databases.php';
        if(isset($_POST['username']) && isset($_POST['password'])){
            try {
                $admin = new Administrator($_POST['username'], $_POST['password']);
                $admin->Masuk();
                $_SESSION['username'] = $_POST['username'];
                $_SESSION['password'] = $_POST['password'];
                header('location: dashboard.php');
            }catch(Exception $e){
                echo '<div class="alert alert-danger" role="alert">
            '.$e->getMessage().'
          </div>';
            }
        }else if(isset($_POST['username']) && isset($_POST['password'])){
          $admin = (new Administrator($_SESSION['username'], $_SESSION['password']))->Masuk();
          header('location: dashboard.php');
        }
      ?>
        <div class="input-group mb-3">
          <input type="text" name="username" class="form-control" placeholder="Username">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-address-card"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <!-- /.col -->
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Masuk</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <!-- /.social-auth-links -->

    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="AdminLTE/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="AdminLTE/dist/js/adminlte.min.js"></script>
<style>
  body {
    background-image: url('img/bg.jpg');
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
  }
  .card, .card-body {
    background-color: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
  }
</style>
</body>
</html>
