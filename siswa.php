<?php
require 'modules/databases.php';
$TITLE = "SISWA";
include 'elements/header.php';
?>
<!-- Modal -->
<div class="modal fade" id="editSiswa" tabindex="-1" aria-labelledby="editSiswaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editSiswaLabel">EDIT SISWA</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div class="input-group mb-3 row">
      <label for="" class="col-form-label col-2">Nama</label>
        <div class="col-10">
          <input type="hidden" id="Inis">
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
        <button type="button" id="btnUpdate"  class="btn btn-primary">Update</button>
      </div>
    </div>
  </div>
</div>

<div class="container">
    <div class="row">
        <div class="offset-md-3 col-md-6 col-12">
            <div class="card">
                <div class="card-header">
                    <h3>CARI SISWA</h3>
                    <div class="input-group">
                        <input type="text" class="form-control" id="nis" placeholder="NIS">
                        <div class="input-group-append" id="search">
                            <div class="input-group-text">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body card-siswa">
                </div>
                <div class="card-footer">
                
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function edit(NIS){
        const form = new FormData();
        form.append('nis', NIS);
        fetch('ajax/dataSiswa.php', {
            method: 'POST',
            body: form
        }).then(async (r)=>{
            const resp = await r.json();
            const isempty = JSON.stringify(resp) === '[]';
            document.getElementById('nama').value = isempty?'':resp.Nama;
            $('#gender').val(isempty?'L':resp.Gender).niceSelect('update');
            document.getElementById('Inis').value = isempty?'':NIS;
            document.getElementById('btnUpdate').onclick = function (){
                const form = new FormData();
                form.append('nis', document.getElementById('Inis').value);
                form.append('nama', document.getElementById('nama').value);
                form.append('gender', $('#gender').val());
                fetch('ajax/update.php', {
                    method: 'POST',
                    body: form
                }).then(async (r)=>{
                    const res = await r.json();
                    const form1 = new FormData();
                    form1.append('nis', document.getElementById('Inis').value);
                    if(res.status){
                        fetch('ajax/dataSiswa.php', {
                            method: 'POST',
                            body: form1
                        }).then(async (resp)=>{
                            const json = await resp.json();
                            if(JSON.stringify(json) === '[]'){
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'error',
                                    title: 'NIS tidak ditemukan',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            }else{
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Berhasil Di Update!',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                $('#editSiswa').modal('hide');
                                $('.card-siswa').empty();
                                $('.card-footer').empty();
                                    $('.card-siswa').append(`                    <div class="row">
                                                <div class="col-md-4 pt-md-3 col-sm-12">
                                                    <img src="img/${json.Gender === 'P'?'fe':''}male.png" class="img-thumbnail" alt="" srcset="">
                                                </div>
                                                <div class="col-md-8 col-sm-12">
                                                    <p class="pt-3">NIS: ${json.NIS}</p>
                                                    <p>Nama: <span id="dnama">${json.Nama}</span></p>
                                                    <p>Kelas: ${json.Kelas} ${json.nama}</p>
                                                    <p>Gender: <span id="dkelas">${{L: 'Laki-Laki', P: 'Perempuan'}[json.Gender]}</span></p>
                                                </div>
                                            </div>`)
                                $('.card-footer').append(`<button onclick="edit('${document.getElementById('nis').value}');" class="btn btn-primary" style="float: right;"><i class="fas fa-trash"></i> Edit</button>
                                <button onclick="hapusSiswa('${document.getElementById('nis').value}');" class="btn btn-danger mx-2" style="float: right;"><i class="fas fa-trash"></i> Hapus</button>`)
                                }
                        })
                    }
                })
            }
            $('#editSiswa').modal('show');
        })
    }
    function hapusSiswa(NIS){
        Swal.fire({
            title: 'Apakah Anda yakin?',
            showDenyButton: true,
            confirmButtonText: 'Hapus',
            denyButtonText: 'Batal',
            }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                const form = new FormData();
                form.append('nis', NIS);
                fetch('ajax/hapusSiswa.php',{
                    method: 'POST',
                    body: form
                }).then(async (resp)=>{
                    const resjs = await resp.json();
                    Swal.fire(resjs.status?'Terhapus!':'Gagal!', '', resjs.status?'success':'error');
                    resjs.status && $('.card-siswa').empty() && $('.card-footer').empty();
                })
            }
            })
    }
    $(document).ready(function() {
        $('select').niceSelect();
        document.getElementById('nis').onkeyup = (el) => {
            console.log(el.target.value.length);
            if(el.key === 'Enter' || el.target.value.length === 10) {
                document.getElementById('search').click();    
            }
        }
        document.getElementById('search').onclick = () => {
        const form = new FormData();
        form.append('nis', document.getElementById('nis').value);
        fetch('ajax/dataSiswa.php', {
            method: 'POST',
            body: form
        }).then(async (resp)=>{
            const json = await resp.json();
            if(JSON.stringify(json) === '[]'){
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: 'NIS tidak ditemukan',
                    showConfirmButton: false,
                    timer: 1500
                });
            }else{
                $('.card-siswa').empty();
                $('.card-footer').empty();
                    $('.card-siswa').append(`                    <div class="row">
                                <div class="col-md-4 pt-md-3 col-sm-12">
                                    <img src="img/${json.Gender === 'P'?'fe':''}male.png" class="img-thumbnail" alt="" srcset="">
                                </div>
                                <div class="col-md-8 col-sm-12">
                                    <p class="pt-3">NIS: ${json.NIS}</p>
                                    <p>Nama: <span id="dnama">${json.Nama}</span></p>
                                    <p>Kelas: ${json.Kelas} ${json.nama}</p>
                                    <p>Gender: <span id="dkelas">${{L: 'Laki-Laki', P: 'Perempuan'}[json.Gender]}</span></p>
                                </div>
                            </div>`)
                $('.card-footer').append(`<button onclick="edit('${document.getElementById('nis').value}');" class="btn btn-primary" style="float: right;"><i class="fas fa-trash"></i> Edit</button>
                <button onclick="hapusSiswa('${document.getElementById('nis').value}');" class="btn btn-danger mx-2" style="float: right;"><i class="fas fa-trash"></i> Hapus</button>`)
                }
        })
    }
    })
</script>
<?php
include 'elements/footer.php';
?>