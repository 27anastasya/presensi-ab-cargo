<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\PengajuanIzin;

class PresensiController extends Controller
{
    public function create()
    {
        $hariini = date("Y-m-d");
        $nik = Auth::guard('karyawan')->user()->nik;
        $cek = DB::table('presensi')->where('tgl_presensi', $hariini)->where('nik', $nik)->count();
        $lokasi_kantor = DB::table('konfigurasi_lokasi')->where('id', 1)->first();
        return view('presensi.create', compact('cek', 'lokasi_kantor'));
    }

    public function store(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $tgl_presensi = date("Y-m-d");
        $jam = date("H:i:s");
        $lokasi = $request->lokasi;
        $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->count();
        if ($cek > 0) {
            $ket = "out";
        } else {
            $ket = "in";
        }
        $image = $request->image;
        $folderPath = "public/uploads/absensi/";
        $formatName = $nik . "-" . $tgl_presensi . "-" . $ket;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;

        if ($cek > 0) {
            $data_pulang = [
                'jam_out' => $jam,
                'foto_out' => $fileName,
                'lokasi_out' => $lokasi,
            ];
            $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->update($data_pulang);
            if ($update) {
                echo "success|Terima Kasih, Selamat Beristirahat!|out";
                Storage::put($file, $image_base64);
            } else {
                echo "error|Maaf anda gagal absen, silahkan hubungi admin!|out";
            }
        } else {
            $data = [
                'nik' => $nik,
                'tgl_presensi' => $tgl_presensi,
                'jam_in' => $jam,
                'foto_in' => $fileName,
                'lokasi_in' => $lokasi,
            ];
            $simpan = DB::table('presensi')->insert($data);
            if ($simpan) {
                echo "success|Terima kasih, selamat bekerja!|in";
                Storage::put($file, $image_base64);
            } else {
                echo "error|Maaf anda gagal absen, silahkan hubungi admin!|in";
            }
        }
    }

    public function editprofile()
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
        return view('presensi.editprofile', compact('karyawan'));
    }

    public function updateprofile(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $nama_lengkap = $request->nama_lengkap;
        $no_hp = $request->no_hp;
        $password = Hash::make($request->password);
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
        if ($request->hasFile('foto')) {
            $foto = $nik . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $karyawan->foto;
        }
        if (empty($request->password)) {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'foto' => $foto,
            ];
        } else {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'password' => $password,
                'foto' => $foto
            ];
        }

        $update = DB::table('karyawan')
            ->where('nik', $nik)
            ->update($data);

        if ($update) {
            if ($request->hasFile('foto')) {
                $folderPath = "public/uploads/karyawan/";
                $request->file('foto')->storeAs($folderPath, $foto);
            }
            return Redirect()->back()->with('success', 'Profil berhasil diperbarui');
        } else {
            return Redirect()->back()->with('error', 'Gagal memperbarui profil, silahkan coba lagi.');
        }
    }

    public function histori()
    {
        $namabulan = [
            "",
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];
        return view('presensi.histori', compact('namabulan'));
    }

    public function gethistori(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $nik = Auth::guard('karyawan')->user()->nik;

        $histori = DB::table('presensi')
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->where('nik', $nik)
            ->orderBy('tgl_presensi')
            ->get();

        return view('presensi.gethistori', compact('histori'));
    }

    public function izin()
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $dataizin = DB::table('pengajuan_izin')->where('nik', $nik)->get();
        return view('presensi.izin', compact('dataizin'));
    }

    public function buatizin()
    {
        return view('presensi.buatizin');
    }

    public function storeizin(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $tgl_izin = $request->tgl_izin;
        $status = $request->status;
        $keterangan = $request->keterangan;

        $data = [
            'nik' => $nik,
            'tgl_izin' => $tgl_izin,
            'status' => $status,
            'keterangan' => $keterangan
        ];

        $simpan = DB::table('pengajuan_izin')->insert($data);

        if ($simpan) {
            return redirect('/presensi/izin')->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return redirect('/presensi/izin')->with(['error' => 'Data Gagal Disimpan']);
        }
    }

    public function monitoring()
    {
        return view('presensi.monitoring');
    }

    public function getpresensi(Request $request)
    {
        $tanggal = $request->tanggal;
        $presensi =  DB::table('presensi')
            ->select('presensi.*', 'nama_lengkap', 'nama_dept')
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->join('departmen', 'karyawan.kode_dept', '=', 'departmen.kode_dept')
            ->where('tgl_presensi', $tanggal)
            ->get();

        return view('presensi.getpresensi', ['presensi' => $presensi]);
    }

    public function tampilkanpeta(Request $request)
    {
        $id = $request->id;
        $presensi = DB::table('presensi')->where('id', $id)
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->first();
        return view('presensi.showmap', compact('presensi'));
    }

    public function laporan()
    {
        $namabulan = [
            "",
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];
        $karyawan = DB::table('karyawan')->orderBy('nama_lengkap')->get();
        return view('presensi.laporan', compact('namabulan', 'karyawan'));
    }

    public function cetaklaporan(Request $request)
    {
        $nik = $request->nik;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = [
            "",
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];
        $karyawan = DB::table('karyawan')->where('nik', $nik)
            ->join('departmen', 'karyawan.kode_dept', '=', 'departmen.kode_dept')
            ->first();
        $presensi = DB::table('presensi')
            ->where('nik', $nik)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->orderBy('tgl_presensi')
            ->get();
        if (isset($_POST['exportexcel'])) {
            $time = date("d-M-Y H:i:s");
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=Laporan Presensi $time.xls");
        }
        return view('presensi.cetaklaporan', compact('bulan', 'tahun', 'namabulan', 'karyawan', 'presensi'));
    }

    public function rekap()
    {
        $namabulan = [
            "",
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];
        return view('presensi.rekap', compact('namabulan'));
    }

    public function cetakrekap(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = [
            "",
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];

        $rekap = DB::table('presensi')
            ->select('presensi.nik', 'nama_lengkap')
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 1, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_1'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 2, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_2'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 3, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_3'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 4, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_4'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 5, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_5'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 6, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_6'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 7, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_7'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 8, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_8'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 9, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_9'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 10, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_10'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 11, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_11'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 12, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_12'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 13, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_13'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 14, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_14'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 15, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_15'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 16, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_16'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 17, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_17'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 18, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_18'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 19, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_19'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 20, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_20'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 21, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_21'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 22, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_22'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 23, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_23'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 24, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_24'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 25, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_25'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 26, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_26'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 27, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_27'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 28, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_28'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 29, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_29'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 30, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_30'))
            ->addSelect(DB::raw('MAX(IF(DAY(tgl_presensi) = 31, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")), "")) as tgl_31'))
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->whereRaw('MONTH(tgl_presensi) = ?', [$bulan])
            ->whereRaw('YEAR(tgl_presensi) = ?', [$tahun])
            ->groupByRaw('presensi.nik,nama_lengkap')
            ->get();


        if (isset($_POST['exportexcel'])) {
            $time = date("d-M-Y H:i:s");
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=Laporan Rekap Presensi $time.xls");
        }

        return view('presensi.cetakrekap', compact('bulan', 'tahun', 'rekap', 'namabulan'));
    }



    public function izinsakit(Request $request)
    {
        $query = PengajuanIzin::query();
        $query->select('id', 'tgl_izin', 'pengajuan_izin.nik', 'nama_lengkap', 'jabatan', 'status', 'status_approved', 'keterangan');
        $query->join('karyawan', 'pengajuan_izin.nik', '=', 'karyawan.nik');

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_izin', [$request->dari, $request->sampai]);
        }

        if (!empty($request->nik)) {
            $query->where('pengajuan_izin.nik', $request->nik);
        }

        if (!empty($request->nama_lengkap)) {
            $query->where('karyawan.nama_lengkap', 'LIKE', '%' . $request->nama_lengkap . '%');
        }

        if ($request->status_approved == "0" || $request->status_approved == "1" || $request->status_approved == "2") {
            $query->where('status_approved', $request->status_approved);
        }

        $query->orderBy('tgl_izin', 'desc');
        $izinsakit = $query->paginate(10);
        $izinsakit->appends($request->all());
        return view('presensi.izinsakit', compact('izinsakit'));
    }

    public function approvedizinsakit(Request $request)
    {
        $status_approved = $request->status_approved;
        $id_izinsakit_form = $request->id_izinsakit_form;

        $update = DB::table('pengajuan_izin')->where('id', $id_izinsakit_form)->update([
            'status_approved' => $status_approved
        ]);

        if ($update) {
            return redirect()->back()->with(['success' => 'Data berhasil diupdate']);
        } else {
            return redirect()->back()->with(['warning' => 'Data gagal diupdate']);
        }
    }

    public function batalkanizinsakit($id)
    {
        $update = DB::table('pengajuan_izin')->where('id', $id)->update([
            'status_approved' => 0
        ]);

        if ($update) {
            return redirect()->back()->with(['success' => 'Data berhasil diupdate']);
        } else {
            return redirect()->back()->with(['warning' => 'Data gagal diupdate']);
        }
    }

    public function cekpengajuanizin(Request $request)
    {
        $tgl_izin = $request->tgl_izin;
        $nik = Auth::guard('karyawan')->user()->nik;

        $cek = DB::table('pengajuan_izin')->where('nik', $nik)->where('tgl_izin', $tgl_izin)->count();

        return $cek;
    }
}
