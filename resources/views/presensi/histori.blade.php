@extends('layouts.presensi')

@section('header')
<div class="appHeader bg-danger text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Histori Presensi</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<div class="row" style="margin-top: 70px;">
    <div class="col">
        <div class="form-row">
            <div class="col-md-6 mb-3">
                <select name="bulan" id="bulan" class="form-control">
                    <option value="">Pilih Bulan</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                            {{ $namabulan[$i] }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <select name="tahun" id="tahun" class="form-control">
                    <option value="">Pilih Tahun</option>
                    @php
                        $tahunmulai = 2022;
                        $tahunskr = date("Y");
                    @endphp
                    @for ($tahun = $tahunmulai; $tahun <= $tahunskr; $tahun++)
                        <option value="{{ $tahun }}" {{ date('Y') == $tahun ? 'selected' : '' }}>
                            {{ $tahun }}
                        </option>
                    @endfor
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="col-12">
                <button type="submit" class="btn btn-danger btn-block" id="getdata">
                    <ion-icon name="search-outline"></ion-icon> Cari
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3"> <!-- Tambahkan margin atas di sini -->
    <div class="col" id="showhistori"></div>
</div>
@endsection

@push('myscript')
<script>
    $(function(){
        $("#getdata").click(function(e){
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            $.ajax({
                type : 'POST',
                url : 'gethistori',
                data : {
                    _token : "{{ csrf_token() }}",
                    bulan : bulan,
                    tahun : tahun,
                },
                cache : false,
                success : function(respond){
                    $("#showhistori").html(respond);
                }
            })
        })
    })
</script>
@endpush
