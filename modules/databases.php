<?php

    require 'Utils.php';
    function absenValidator(string $absen){
        if(!in_array($absen,['Hadir', 'Alpa','Belum Absen', 'Sakit', 'Izin'])) throw new Exception('parameter absen tidak valid');
    }
    class Connection {
        private string $host = '127.0.0.1';
        private string $username = 'root';
        private string $password = '';
        private string $database = 'Absensi';
        public $connection;
        public function __construct() {
            $this->connection = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->database
            );
        }

        public function __toString(): string
        {
            return "{$this->username}@{$this->host}:{$this->database}";
        }
    }
    class Kelas extends Connection {
        public string $kelas;
        public function __construct(string $kelas)
        {
            parent::__construct();
            $this->kelas = strtoupper($kelas);
        }

        public function tambahKelas() {
            $query = $this->connection->prepare('INSERT INTO Kelas(Kelas) VALUES (?)');
            $query->bind_param('s', $this->kelas);
            $query->execute();
        }

        public function tambahJurusan(string $nama): int {
            $up = strtoupper($nama);
            $query = $this->connection->prepare('INSERT INTO Jurusan(nama, kelas) VALUES (?, ?)');
            $query->bind_param('ss', $up, $this->kelas);
            $query->execute();
            return $query->insert_id;
        }

        public function Jurusan() {
            $query = $this->connection->prepare('SELECT id, nama FROM Jurusan WHERE kelas = ?');
            $query->bind_param('s', $this->kelas);
            $query->execute();
            return $query->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        public function delete() {

        }

    }
    class Siswa extends Connection {
        public string $nis;
        public function __construct(string $nis) {
            parent::__construct();
            $this->nis = NISValidator($nis);
        }
        public function kehadiranHariIni(){
            $cdate = (new DateTime('now'))->format('Y-m-d');
            $query = $this->connection->prepare('SELECT Kehadiran FROM Absensi WHERE Siswa = ? AND waktu = ?');
            $query->bind_param('ss', $this->nis, $cdate);
            $query->execute();
            $result = $query->get_result()->fetch_assoc();
            return $result?$result['Kehadiran']:'Belum Absen';
        }
        public function Kehadiran(){
            $date = new DateTime('now');
            $fdate = date('Y-m-1');
            $date->modify('last day of this month');
            $lastdayOfMonth = $date->format('Y-m-d');
            $tg = $this->connection->prepare('SELECT * FROM Absensi WHERE waktu >= ? AND waktu <= ? AND Siswa = ?');
            $tg->bind_param('sss', $fdate, $lastdayOfMonth, $this->nis);
            $tg->execute();
            return $tg->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        public function Tambah(string $nama, int $kelas, string $gender) {
            if(!in_array($gender, ['P', 'L'])) throw new Exception('parameter gender Tidak valid');
            $query = $this->connection->prepare('INSERT INTO Siswa(NIS, Nama, Kelas, Gender) VALUES (?, ?, ?, ?)');
            $query->bind_param('ssis', $this->nis, $nama, $kelas, $gender);
            $query->execute();
        }

        public function InfoSiswa(): array {
            $query = $this->connection->prepare('SELECT Siswa.NIS, Siswa.Nama, Siswa.Gender, Jurusan.nama, Kelas.Kelas FROM Siswa RIGHT JOIN Jurusan ON Siswa.Kelas = Jurusan.id RIGHT JOIN Kelas ON Kelas.Kelas = Jurusan.kelas WHERE NIS=?');
            $query->bind_param('s', $this->nis);
            $query->execute();
            $siswa = $query->get_result()->fetch_assoc();
            if(!$siswa){
                throw new Exception('NIS Tidak Terdaftar');
            }
            return $siswa;
        }

        public function Absen(string $absen) {
            absenValidator($absen);
            $currentDate = currentDate();
            $query = $this->connection->prepare('SELECT COUNT(Siswa) FROM Absensi WHERE Siswa = ? AND waktu = ?');
            $query->bind_param('ss', $this->nis, $currentDate);
            $query->execute();
            $query = $this->connection->prepare($query->get_result()->fetch_assoc()['COUNT(Siswa)']?'UPDATE Absensi SET Kehadiran = ? WHERE Siswa = ? AND waktu = ?':'INSERT INTO Absensi(Kehadiran, Siswa, waktu) VALUES (?, ?, ?)');
            $query->bind_param('sss', $absen, $this->nis, $currentDate);
            $query->execute();
        }
        public function update(string $nama, string $gender): int {
            if(!in_array($gender, ['P', 'L'])) throw new Exception('parameter gender Tidak valid');
            $query = $this->connection->prepare('UPDATE Siswa SET Nama = ?, Gender = ? WHERE NIS = ?');
            $query->bind_param('sss', $nama, $gender, $this->nis);
            $query->execute();
            return $query->affected_rows;
        }
        public function hapus(): int {
            $query = $this->connection->prepare('DELETE FROM Siswa WHERE NIS = ?');
            $query->bind_param('s', $this->nis);
            $query->execute();
            return $query->affected_rows;
        }
    }

    class Jurusan extends Connection {
        public int $id_jurusan;
        public function __construct(int $id)
        {
            parent::__construct();
            $this->id_jurusan = $id;
        }
        public function namaJurusan(): string | NULL {
            $query = $this->connection->prepare('SELECT nama FROM Jurusan WHERE id = ?');
            $query->bind_param('i', $this->id_jurusan);
            $query->execute();
            return $query->get_result()->fetch_assoc()['nama'];
        }

        public function Kehadiran(): array {
            $data = [];
            foreach($this->siswa() as $iter){
                $kehadiran = (new Siswa($iter['NIS']))->Kehadiran();
                array_push($data, [...$iter, 'kehadiran' => $kehadiran]);
            }
            return $data;
        }

        public function siswa(): array {
            $query = $this->connection->prepare('SELECT NIS, Nama FROM Siswa WHERE Kelas = ? ORDER BY Nama');
            $query->bind_param('i', $this->id_jurusan);
            $query->execute();
            return $query->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        public function Kelas(): string | NULL {
            $query = $this->connection->prepare('SELECT kelas FROM Jurusan WHERE id = ?');
            $query->bind_param('i', $this->id_jurusan);
            $query->execute();
            return $query->get_result()->fetch_assoc()['kelas'];
        }
        public function Harian(): array {
            $cdate = currentDate();
            $query = $this->connection->prepare('SELECT 
                                                    Siswa.NIS,
                                                    Siswa.Nama,
                                                    Siswa.Gender,
                                                    Jurusan.nama,
                                                    Kelas.Kelas,
                                                    Absensi.Kehadiran
                                                FROM Siswa RIGHT JOIN Jurusan ON Jurusan.id = Siswa.Kelas
                                                RIGHT JOIN Kelas ON Jurusan.kelas = Kelas.kelas
                                                LEFT JOIN Absensi ON Siswa.NIS = Absensi.Siswa 
                                                WHERE Absensi.waktu = ? AND Siswa.Kelas = ? ORDER BY Siswa.Nama');
            $query->bind_param('si', $cdate, $this->id_jurusan);
            $query->execute();
            return $query->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        public function jumlahSiswa(): int {
            $query = $this->connection->prepare('SELECT COUNT(NIS) FROM Siswa WHERE Kelas = ?');
            $query->bind_param('i', $this->id_jurusan);
            $query->execute();
            return $query->get_result()->fetch_assoc()['COUNT(NIS)'];
        }
        public function statistics(string $absen): int {
            absenValidator($absen);
            $query = $this->connection->prepare('SELECT COUNT(Siswa) FROM Absensi RIGHT JOIN Siswa ON Siswa.NIS = Absensi.Siswa WHERE Kehadiran = ? AND Kelas = ?');
            $query->bind_param('si', $absen, $this->id_jurusan);
            $query->execute();
            return $query->get_result()->fetch_assoc()['COUNT(Siswa)'];
        }
        public function delete(): bool {
            $query = $this->connection->prepare('DELETE FROM Jurusan WHERE id = ?');
            $query->bind_param('i', $this->id_jurusan);
            $query->execute();
            return boolval($query->affected_rows);
        }

    }

    class Administrator extends Connection {
        private string $username;
        private string $password;
        public function __construct(string $username, string $password) {
            parent::__construct();
            $this->username = $username;
            $this->password = $password;
        }
        public function statistics():array {
            return [];
        }

        public function Kelas() {
            $query = $this->connection->prepare('SELECT Kelas FROM Kelas');
            $query->execute();
            return array_map('unpackArray', $query->get_result()->fetch_all(MYSQLI_NUM));
        }
        public function jumlahKelas(): int {
            $query = $this->connection->prepare('SELECT COUNT(Kelas) FROM Kelas');
            $query->execute();
            return $query->get_result()->fetch_assoc()['COUNT(Kelas)'];
        }
        public function jumlahJurusan(): int {
            $query = $this->connection->prepare('SELECT COUNT(id) FROM Jurusan');
            $query->execute();
            return $query->get_result()->fetch_assoc()['COUNT(id)'];
        }
        public function Masuk() {
            $con = $this->connection->prepare('SELECT password FROM Administrator WHERE username = ?');
            $con->bind_param('s', $this->username);
            $con->execute();
            if(!password_verify($this->password, $con->get_result()->fetch_assoc()['password']))throw new Exception('username & password tidak terdaftar');
        }

        public function jumlahSiswa():int {
            $query = $this->connection->prepare('SELECT COUNT(NIS) FROM Siswa');
            $query->execute();
            return $query->get_result()->fetch_assoc()['COUNT(NIS)'];
        }
        public function belumAbsen(): int {
            $current = currentDate();
            $query = $this->connection->prepare('SELECT COUNT(Siswa) FROM Absensi WHERE Kehadiran <> "Belum Absen" AND waktu = ?');
            $query->bind_param('s', $current);
            $query->execute();
            $absen = $query->get_result()->fetch_assoc()['COUNT(Siswa)'];
            return $this->jumlahSiswa() - $absen;
        }
        public function jumlah(string $absen): int {
            if($absen === 'Belum Absen')return $this->belumAbsen();
            absenValidator($absen);
            $current = currentDate();
            $query = $this->connection->prepare('SELECT COUNT(Siswa) FROM Absensi WHERE waktu = ? AND Kehadiran = ?');
            $query->bind_param('ss', $current, $absen);
            $query->execute();
            return $query->get_result()->fetch_assoc()['COUNT(Siswa)'];
        }
        public function Daftar() {
            $query = $this->connection->prepare('INSERT INTO Administrator(username,password) VALUES (?, ?)');
            $hash = password_hash($this->password, PASSWORD_BCRYPT);
            $query->bind_param('ss', $this->username, $hash);
            $query->execute();
            return $query;
        }
    }
?>