<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class DepartemenController extends Controller
{
    public function index(Request $request){
        $nama_dept = $request->nama_dept;
        $query = Departemen::query();
        $query->select('*');
        if(!empty($nama_dept)){
            $query->where('nama_dept','like','%'.$nama_dept.'%');
        }
        $departmen = $query->get();
        // $departmen = DB::table('departmen')->orderBy('kode_dept')->get();
        return view('departmen.index', compact('departmen'));
    }

    public function store(Request $request){
        $kode_dept = $request->kode_dept;
        $nama_dept = $request->nama_dept;
        $data =[
            'kode_dept' => $kode_dept,
            'nama_dept' => $nama_dept
        ];
        $simpan = DB::table('departmen')->insert($data);
        if($simpan){
            return Redirect::back()->with(['success'=> 'Data Berhasil Disimpan']);
        }else{
            return Redirect::back()->with(['warning'=> 'Data Gagal Disimpan']);
        }
    }

    public function edit(Request $request){
        $kode_dept = $request->kode_dept;
        $departmen = DB::table('departmen')->where('kode_dept', $kode_dept)->first();
        return view('departmen.edit', compact('departmen'));
    }

    public function update($kode_dept, Request $request){
        $nama_dept = $request->nama_dept;
        $data = [
            'nama_dept' => $nama_dept
        ];
        $update = DB::table('departmen')->where('kode_dept', $kode_dept)->update($data);
        if($update){
            return Redirect::back()->with(['success'=>'Data Berhasil Di Update']);
        }else{
            return Redirect::back()->with(['warning'=>'Data Gagal Di Update']);
        }
    }

    public function delete($kode_dept, Request $request){
        $hapus = DB::table('departmen')->where('kode_dept', $kode_dept)->delete();
        if($hapus){
            return Redirect::back()->with(['success'=>'Data Berhasil Di Hapus']);
        }else{
            return Redirect::back()->with(['warning'=>'Data Gagal Di Hapus']);
        }
    }
}
