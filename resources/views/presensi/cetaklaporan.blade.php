<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Laporan Kehadiran Karyawan AB Cargo</title>

    <!-- Normalize or reset CSS with your favorite library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

    <!-- Load paper.css for happy printing -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

    <!-- Set page size here: A4, A5, A3, etc. -->
    <!-- Set also "landscape" if you need -->
    <style>
        @page { size: A4 }
        #title {
            font-size: 18px;
            font-weight: bold;
        }
        .tabeldatakaryawan {
            margin-top: 40px;
        }
        .tabeldatakaryawan td {
            padding: 5px;
        }
        .tabelpresensi {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .tabelpresensi th {
            border: 1px solid #131212;
            padding: 8px;
            background-color: #dbdbdb;
            text-align: center; /* Menambahkan perataan teks tengah */
        }
        .tabelpresensi td {
            border: 1px solid #131212;
            padding: 5px;
            font-size: 12px;
            text-align: center; /* Menambahkan perataan teks tengah */
        }
        .foto {
            width: 40px;
            height: 30px;
        }
    </style>
</head>
<body class="A4">
<?php
function selisih($jam_masuk, $jam_keluar)
        {
            list($h, $m, $s) = explode(":", $jam_masuk);
            $dtAwal = mktime($h, $m, $s, "1", "1", "1");
            list($h, $m, $s) = explode(":", $jam_keluar);
            $dtAkhir = mktime($h, $m, $s, "1", "1", "1");
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
                        LAPORAN PRESENSI KARYAWAN <br>
                        PERIODE BULAN {{strtoupper($namabulan[$bulan])}} {{$tahun}}<br>
                        PT. HERLYK EKSPRESS
                    </span><br>
                    <span><i>Jl. Mampang Prpt. Raya No.77, RT.1/RW.2, Tegal Parang, Kec. Mampang Prpt., Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12790</i></span>
                </td>
            </tr>
        </table>
        <table class="tabeldatakaryawan">
            <tr>
                <td rowspan="6">
                    @php
                    $path = Storage::url('uploads/karyawan/' . $karyawan->foto);
                    @endphp
                    <img src="{{ public_path($path) }}" alt="" width="150" height="200">
                </td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>:</td>
                <td>{{$karyawan->nik}}</td>
            </tr>
            <tr>
                <td>Nama Karyawan</td>
                <td>:</td>
                <td>{{$karyawan->nama_lengkap}}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{$karyawan->jabatan}}</td>
            </tr>
            <tr>
                <td>Nama Departemen</td>
                <td>:</td>
                <td>{{$karyawan->nama_dept}}</td>
            </tr>
            <tr>
                <td>No. HP</td>
                <td>:</td>
                <td>{{$karyawan->no_hp}}</td>
            </tr>
        </table>
        <table class="tabelpresensi">
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <!-- <th>Foto</th> -->
                <th>Jam Pulang</th>
                <!-- <th>Foto</th> -->
                <!-- <th>Lokasi</th> -->
                <th>Keterangan</th>
            </tr>
            <!-- $path_in= Storage::url('uploads/karyawan/'.$d->foto_in);
            $path_out= Storage::url('uploads/karyawan/'.$d->foto_out); -->
            @foreach ($presensi as $d)
            @php
            $jamterlambat = selisih('08:00:00',$d->jam_in);
            @endphp
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{date("d-m-Y",strtotime($d->tgl_presensi))}}</td>
                <td>{{$d->jam_in}}</td>
                <td>{{$d->jam_out}}</td>
                <td>
                    @if ($d->jam_in > '08:00')
                    Terlambat {{ $jamterlambat }}
                    @else
                    Tepat Waktu
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
        <table width="100%" style="margin-top:100px">
            <tr>
                <td colspan="2" style="text-align: right">Bekasi, {{ date('d-m-Y') }}</td>
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
