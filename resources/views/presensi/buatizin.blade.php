@extends('layouts.presensi')

@section('header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
<style>
    .datepicker-modal{
        max-height:430px !important;
    }
    .datepicker-date-display{
        background-color : #800000 !important;
    }
</style>
<div class="appHeader bg-danger text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Form Izin</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<div class="row" style="margin-top: 70px;">
    <div class="col">
        <form method="POST" action="/presensi/storeizin" id="frmIzin">
            @csrf
            <div class="form-group">
                <label for="tgl_izin">Tanggal</label>
                <input type="text" id="tgl_izin" name="tgl_izin" class="form-control datepicker" placeholder="Pilih Tanggal">
            </div>
            <div class="form-group">
                <label for="status">Izin / Sakit</label>
                <select name="status" id="status" class="form-control">
                    <option value="">Pilih Status</option>
                    <option value="i">Izin</option>
                    <option value="s">Sakit</option>
                </select>
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea name="keterangan" id="keterangan" cols="30" rows="5" class="form-control" placeholder="Masukkan Keterangan"></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-danger btn-block">Kirim</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('myscript')
<script>
var currYear = (new Date()).getFullYear();

$(document).ready(function() {
  $(".datepicker").datepicker({
    format: "yyyy-mm-dd"    
  });
  $("#tgl_izin").change(function(e){
    var tgl_izin = $(this).val();
    $.ajax({
        type: 'POST',
        url: '/presensi/cekpengajuanizin', // Perbaikan dalam URL
        data: {
            _token: "{{ csrf_token() }}",
            tgl_izin: tgl_izin
        },
        cache: false,
        success: function(respond){
            if (respond == 1){
                Swal.fire({
                    title: 'Oops!',
                    text: 'Anda sudah mengajukan izin untuk tanggal ini!',
                    icon: 'warning'
                }).then((result)=>{
                    $("#tgl_izin").val("");
                });
            }
        }
    });
});

  $("#frmIzin").submit(function(event){
    event.preventDefault();
    var tgl_izin = $("#tgl_izin").val();
    var status = $("#status").val();
    var keterangan = $("#keterangan").val();
    
    if(tgl_izin == ""){
        Swal.fire({
            title: 'Gagal!',
            text: 'Tanggal harus diisi!',
            icon: 'warning'
        });
        return false;
    } else if(status == ""){
        Swal.fire({
            title: 'Gagal!',
            text: 'Status harus diisi!',
            icon: 'warning'
        });
        return false;
    } else if(keterangan == ""){
        Swal.fire({
            title: 'Gagal!',
            text: 'Keterangan harus diisi!',
            icon: 'warning'
        });
        return false;
    } else {
        // Jika semua validasi terpenuhi, Anda dapat mengirimkan formulir
        $("#frmIzin")[0].submit();
    }
  });
});
</script>
@endpush
