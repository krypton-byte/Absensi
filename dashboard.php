<?php
require 'modules/databases.php';
$TITLE = "HOME";
include 'elements/header.php';
$user = new Administrator($_SESSION['username'], $_SESSION['password']);
?>
<script src="AdminLTE/plugins/chart.js/Chart.min.js"></script>
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">DASHBOARD</h3>
              </div>
              <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <canvas id="donutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                    <div class="col-6">
                        <div class="col-12">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                  <p>Jumlah Kelas</p>
                                    <h3><?php echo $user->jumlahKelas() ?></h3>
                                </div>
                                <div class="icon">
                                <i class="fa fa-chalkboard-user"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                  <p>Total Jurusan</p>
                                    <h3><?php echo $user->jumlahJurusan() ?></h3>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-bookmark"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
<script>
        //-------------
    //- DONUT CHART -
    //-------------
    // Get context with jQuery - using jQuery's .get() method.
    var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
    var donutData        = {
      labels: [
          'Hadir',
          'Izin',
          'Sakit',
          'Alpa',
          'Belum Absen',
      ],
      datasets: [
        {
          data: [
            <?php echo $user->jumlah('Hadir')?>,
            <?php echo $user->jumlah('Izin')?>,
            <?php echo $user->jumlah('Sakit')?>,
            <?php echo $user->jumlah('Alpa')?>,
            <?php echo $user->belumAbsen() ?>
          ],
          backgroundColor : ['#00c0ef', '#00a65a', '#f39c12', '#f56954', '#3c8dbc'],
        }
      ]
    }
    var donutOptions     = {
      maintainAspectRatio : false,
      responsive : true,
    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    new Chart(donutChartCanvas, {
      type: 'doughnut',
      data: donutData,
      options: donutOptions
    })
</script>
<?php
include 'elements/footer.php';
?>