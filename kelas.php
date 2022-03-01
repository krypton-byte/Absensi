<?php
require 'modules/databases.php';
if(isset($_GET['id']) && is_numeric($_GET['id'])){
    try {
        $jur = new Jurusan(intval($_GET['id']));
        $TITLE = $jur->Kelas().' '.$jur->namaJurusan();
        if(!str_replace(' ', '', $TITLE)){
            $TITLE = 'Kelas Tidak Ditemukan';
        }
    }catch(Exception){
        $TITLE = 'Kelas Tidak Ditemukan';
    }
}
else {
    $TITLE = 'Kelas Tidak Ditemukan';
}
include 'elements/header.php';
if($TITLE !== 'Kelas Tidak Ditemukan'){
?>
  <!-- Modal -->
  <script src="AdminLTE/plugins/datatables/jquery.dataTables.min.js"></script>
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tambah Siswa</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="input-group mb-3 row">
          <label for="" class="col-form-label col-2">NIS</label>
          <div class="col-10">
            <input type="text" id="nis" class="form-control" placeholder="NIS">
            <div class="input-group-append">
            </div>
          </div>
        </div>
        <div class="input-group mb-3 row">
          <label for="" class="col-form-label col-2">Nama</label>
          <div class="col-10">
            <input type="text" id="nama" class="form-control" placeholder="Nama">
          </div>
        </div>
        <div class="input-group mb-3 row">
          <label for="" class="col-form-label col-2">Gender</label>
          <div class="col-10">
          <select id="gender">
            <option value="L">Laki-Laki</option>
            <option value="P">Perempuan</option>
          </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="tambahkan" class="btn btn-primary">Tambahkan</button>
      </div>
    </div>
  </div>
</div>

            <div class="card card-info">
              <div class="card-header">
              <button onclick="javascript:$('#exampleModal').modal('show');" class="btn btn-primary" style="float: right;"><i class="fas fa-plus"></i> Siswa</button>
              <button id="cetak" class="btn btn-primary mx-3" style="float: right;"><i class="fas fa-print"></i> Cetak</button>
                <h3 class="card-title">DAFTAR KEHADIRAN <?php echo htmlspecialchars($TITLE)?></h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="table-data" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>NIS</th>
                    <th>NAMA</th>
                    <th>Absensi</th>
                  </tr>
                  </thead>
                  <tbody>
                      <?php
                        foreach($jur->siswa() as $i){
                            $siswa = new Siswa($i['NIS']);
                            $kehadiran = $siswa->kehadiranHariIni();
                            echo '<tr>
                              <td>'.$i['NIS'].'</td>
                              <td>'.htmlspecialchars($i['Nama']).'</td>
                              <td>
                              '?>
                              <select class="kehadiran" onchange="ubah(<?php echo $i['NIS']?>,this)">
                                <option value="Belum Absen" <?php echo $kehadiran=='Belum Absen'?'selected':''?>>Belum Absen</option>
                                <option value="Hadir" <?php echo $kehadiran=='Hadir'?'selected':''?>>Hadir</option>
                                <option value="Izin" <?php echo $kehadiran=='Izin'?'selected':''?>>Izin</option>
                                <option value="Sakit" <?php echo $kehadiran=='Sakit'?'selected':''?>>Sakit</option>
                                <option value="Alpa" <?php echo $kehadiran=='Alpa'?'selected':''?>>Alpa</option>
                              </select>
                              <?php
                            echo '</td></tr>';
                        }
                      ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <style>
              input {
                margin-bottom: 10px;
              }
              th {
                background-color: #17A2B8;
              }
            </style>
            <script>
              function printDaily(){
                const fdata = new FormData();
                fdata.append('jurusan', '<?php echo htmlspecialchars($_GET['kelas'])?>')
                fetch('ajax/harian.php', {
                  method:'POST',
                  body:fdata
                }).then(async (resp)=>{
                  
                })
              }
              function createMultipleCol(data){
                blank = [...Array(31)].map(()=>' ');
                console.log(JSON.stringify(data));
                data.kehadiran.forEach(d => {
                  blank[Number(d.waktu.split('-')[2]) - 1] = {text: d.Kehadiran==='Belum Absen'?' ':d.Kehadiran[0], alignment: 'center'};
                })
                return blank;
              }
              $(document).ready(function() {
                $("#table-data").DataTable({
                  "responsive": true, "lengthChange": false, "autoWidth": false, "paging": false
                })
                  const toMonthName = (n) => {
                    return [
                      'Januari',   'Februari',
                      'Maret',     'April',
                      'Mei',       'Juni',
                      'Juli',      'Agustus',
                      'September', 'Oktober',
                      'November',  'Desember'
                    ][n]
                  }
                  document.getElementById('cetak').onclick = async ()=>{
                    const { value: fruit } = await Swal.fire({
                      title: 'Pilih Rentang Waktu',
                      input: 'select',
                      inputOptions: {
                        'daily': 'Hari Ini',
                        'monthly':'Bulan Ini'
                      },
                      inputPlaceholder: 'Rentang Waktu',
                      showCancelButton: true,
                      inputValidator: (value) => {
                        const now = new Date();
                        switch(value){
                            case 'daily':
                              return (function (){
                                console.log('ccc')
                                const fdata = new FormData();
                                fdata.append('jurusan', <?php echo $_GET['id']?>)
                                return fetch('ajax/harian.php', {
                                  method:'POST',
                                  body:fdata
                                }).then(async (resp)=>{
                                  const json = await resp.json();
                                  const dd = {
                                    info:{
                                      author: 'Puja',
                                      title: 'Laporan Absensi <?php echo htmlspecialchars($TITLE)?>',
                                      subject: 'Laporan Absensi Kelas',
                                      keyword: 'laporan'
                                    },
                                    pageSize: 'a4',
                                    content: [
                                        {text: 'ABSENSI KELAS <?php echo htmlspecialchars($TITLE)?>', fontSize: 23, alignment:'center'},
                                        {text: `${now.getDate()} ${toMonthName(now.getMonth())} ${now.getFullYear()}`, fontSize: 13, alignment:'center',lineHeight: 2},
                                      {table:{
                                          headerRows:1,
                                          widths: [22, 'auto', 190, 170],
                                          body:[
                                            [{
                                              text: 'No',
                                              alignment: 'center'
                                              },{
                                                  text: 'NIS',
                                                  alignment: 'center'
                                              }, {
                                                  text: 'Nama',
                                                  alignment: 'center'
                                              },{
                                                  text:'Absensi',
                                                  alignment: 'center'
                                              }
                                            ],
                                          ...json.map((p, y)=>[{text: y+1, 'alignment': 'center'}, p.NIS, p.Nama, p.kehadiran])
                                          ]
                                      }}
                                    ]
                                  }
                                  pdfMake.createPdf(dd).print();
                              })
                            })();
                            break;
                            case 'monthly':(function (){
                              // playground requires you to assign document definition to a variable called dd
                              const fd = new FormData();
                              fd.append('jurusan', <?php echo $_GET['id']?>);
                              fetch('ajax/laporan.php', {
                                method: 'POST',
                                body: fd
                              }).then(async (r)=>{
                                const js = await r.json();
                                var dd = {
                                    pageOrientation:'landscape',
                                    pageSize:'A4',
                                    content: [
                                        {text: 'Absensi Kelas 10 PPLG 1', fontSize:23, alignment: 'center'},
                                        {text: `${now.getDate()} ${toMonthName(now.getMonth())} ${now.getFullYear()}`, fontSize: 13, alignment:'center',lineHeight: 2},
                                        {table:{
                                            headerRows:1,
                                            widths:[20, 110, 'auto','auto','auto','auto','auto','auto','auto','auto','auto','auto','auto','auto','auto','auto','auto','auto','auto','auto','auto','auto','auto','auto','auto','auto','auto','auto','auto','auto','auto','auto', 'auto'],
                                            body:[
                                                [
                                                    {text:'No', alignment: 'center'},{text:'Nama', alignment: 'center'}, '1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31'
                                                ],
                                                ...js.data.map((d, i)=>[{'text': i.toString(), 'alignment': 'center'}, d.Nama, ...createMultipleCol(d)])
                                            ]
                                        }}
                                    ]
                                    
                                  }
                                  pdfMake.createPdf(dd).print();
                                  
                              })
                              })()
                          break;
                          // if (value === 'oranges') {
                          //   resolve()
                          // } else {
                          //   resolve('You need to select oranges :)'+value)
                          // }
                        }
                      }
                    })
                  }  
                  document.getElementById('tambahkan').onclick= () => {
                  const form = new FormData();
                  form.append('nis', document.getElementById('nis').value);
                  form.append('nama', document.getElementById('nama').value);
                  form.append('gender', document.getElementById('gender').value);
                  form.append('kelas', <?php echo $_GET["id"]?>);
                  fetch('ajax/tambahSiswa.php', {
                    method: 'POST',
                    body: form
                  }).then(async (resp) => {
                    const json = await resp.json();
                    Swal.fire({
                      position: 'top-end',
                      icon: json.status?'success':'error',
                      title: (json.status?'Berhasil':'Gagal')+' ditambahkan',
                      showConfirmButton: false,
                      timer: 1500
                    });
                    json.status && window.location.reload();
                  })
                }
                $('select').niceSelect();
              })
              function ubah(nis, el){
                  const form = new FormData();
                  form.append('nis', nis);
                  form.append('absen', el.value);
                  fetch('ajax/kehadiranSiswa.php', {
                    method: 'POST',
                    body: form
                  }).then(async (resp)=>{
                    const json = await resp.json();
                    Swal.fire({
                      position: 'top-end',
                      icon: json.status?'success':'error',
                      title: json.status?'Berhasil':'Gagal',
                      showConfirmButton: false,
                      timer: 1500
                    })
                  })
                }
            </script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.3.0-beta.1/pdfmake.min.js" integrity="sha512-G332POpNexhCYGoyPfct/0/K1BZc4vHO5XSzRENRML0evYCaRpAUNxFinoIJCZFJlGGnOWJbtMLgEGRtiCJ0Yw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.3.0-beta.1/vfs_fonts.min.js" integrity="sha512-6RDwGHTexMgLUqN/M2wHQ5KIR9T3WVbXd7hg0bnT+vs5ssavSnCica4Uw0EJnrErHzQa6LRfItjECPqRt4iZmA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.3.0-beta.1/fonts/Roboto.min.js" integrity="sha512-pGCzTqMr/3jV+O3cu9KXYTO0/0UHJba6H09poX7vS66l4w73yalUZDb/u10WBVt9gtRI2dRcOoKPWriyqfV1hA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<?php
}
include 'elements/footer.php';
?>