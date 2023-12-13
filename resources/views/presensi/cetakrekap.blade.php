<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Laporan Kehadiran Karyawan AB Cargo</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

    <style>
    @page { size: A4 landscape }
    body {
        font-family: Arial, sans-serif;
    }
    #title {
        font-size: 18px;
        font-weight: bold;
        margin: 10px 0;
    }
    .tabeldatakaryawan {
        margin-top: 20px;
    }
    .tabeldatakaryawan td {
        padding: 5px;
    }
    .tabelpresensi {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
    }
    .tabelpresensi th, .tabelpresensi td {
        border: 1px solid #000;
        padding: 8px;
        font-size: 12px;
        text-align: center;
    }
    .tabelpresensi th {
        background-color: #dbdbdb;
    }
    .foto {
        width: 40px;
        height: 30px;
    }
</style>

</head>
<body class="A4 landscape">
<?php
function selisih($jam_in, $jam_out)
{
    list($h, $m, $s) = explode(":", $jam_in);
    $dtAwal = mktime($h, $m, $s, 1, 1, 1);
    list($h, $m, $s) = explode(":", $jam_out);
    $dtAkhir = mktime($h, $m, $s, 1, 1, 1);
    $dtSelisih = $dtAkhir - $dtAwal;
    $totalmenit = $dtSelisih / 60;
    $jam = explode(".", $totalmenit / 60);
    $sisamenit = ($totalmenit / 60) - $jam[0];
    $sisamenit2 = $sisamenit * 60;
    $jml_jam = $jam[0];
    return $jml_jam . ":" . round($sisamenit2);
}
?>
    <section class="sheet padding-10mm">
        <!-- Write HTML just like a web page -->
        <table style="width: 100%">
            <tr>
                <td>
                    <img src="{{ asset('assets/img/abc.png') }}" width="125" height="70" alt="">
                </td>
                <td>
                    <span id="title">
                        LAPORAN REKAP PRESENSI KARYAWAN <br>
                        PERIODE BULAN {{strtoupper($namabulan[$bulan])}} {{$tahun}}<br>
                        PT. HERLYK EKSPRESS
                    </span><br>
                    <span><i>Jl. Mampang Prpt. Raya No.77, RT.1/RW.2, Tegal Parang, Kec. Mampang Prpt., Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12790</i></span>
                </td>
            </tr>
        </table>
        
        <table class="tabelpresensi">
            <tr>
                <th rowspan="2">Nik</th>
                <th rowspan="2">Nama Karyawan</th>
                <th colspan="31">Tanggal</th>
                <th rowspan="2">Total Hadir</th>
                <th rowspan="2">Total Terlambat</th>
            </tr>
            <tr>
                <?php
                for ($i = 1; $i <= 31; $i++) {
                ?>
                <th>{{$i}}</th>
                <?php
                }
                ?>
            </tr>
            @foreach ($rekap as $d)
            <tr>
                <td>{{ $d->nik }}</td>
                <td>{{ $d->nama_lengkap }}</td>
                <?php
                $totalhadir = 0;
                $totalterlambat = 0;
                for ($i = 1; $i <= 31; $i++) {
                    $tgl = "tgl_" . $i;
                    if (empty($d->$tgl)) {
                        $hadir = ['', ''];
                        $totalhadir += 0;
                    } else {
                        $hadir = explode("-", $d->$tgl);
                        $totalhadir += 1;
                        if($hadir[0] > "08:00:00"){
                            $totalterlambat += 1;
                        }
                    }
                ?>
                <td>
                    <span style="color: {{ $hadir[0] > "08:00:00" ? "red" : "" }}">{{ $hadir[0] }}</span> <br>
                    <span style="color: {{ $hadir[1] < "17:00:00" ? "red" : "" }}">{{ $hadir[1] }}</span>
                </td>
                <?php
                }
                ?>
                <td>{{ $totalhadir }}</td>
                <td>{{ $totalterlambat }}</td>
            </tr>
            @endforeach
        </table>

        <table width="100%" style="margin-top:100px">
            <tr>
                <td></td>
                <td style="text-align: center">Bekasi, {{ date('d-m-Y') }}</td>
            </tr>
            <tr>
                <td style="text-align: center; vertical-align:bottom" height="100px">
                    <u>Vani Ayu Anastasya</u><br>
                    <i><b>HRD Manager</b></i>
                </td>
                <td style="text-align: center; vertical-align:bottom">
                    <u>Albyan R.</u><br>
                    <i><b>Direktur</b></i>
                </td>
            </tr>
        </table>
    </section>
</body>
</html>
