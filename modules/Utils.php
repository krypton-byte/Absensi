<?php
    function currentDate(): string {
        return (new DateTime())->format('Y-m-d');
    }

    function NISValidator(string $nis): string {
        if(strlen($nis) === 10 && is_numeric($nis)) {
            return $nis;
        }
        throw new Exception('NIS Tidak Valid');
    }

    function arrayGenerator(int $end, string $value): array {
        $x = 0;
        $arr = array();
        while($x <= $end){
            array_push($arr, $value);
            $x++;
        }
        unset($arr[0]);
        return $arr;
    }

    function AbsenSerialize(array $data) {
        $baru = arrayGenerator(31, ' ');
        foreach($data as $iter){
            $baru[explode('-', $iter['waktu'])[2]] = $iter['kehadiran'];
        }
    }

    function unpackArray(array $data): string {
        return $data[0];
    }
?>