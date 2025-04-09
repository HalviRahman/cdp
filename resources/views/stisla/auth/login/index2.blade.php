@extends('stisla.layouts.app-auth-simple')

@section('title')
  {{ $title = __('SIM CDP') }}
@endsection

@section('content')
  <div class="card-body">
    {{-- <form method="POST" action="{{ route('login-post') }}" class="needs-validation" novalidate="" id="formAuth">
      @csrf

      @include('stisla.includes.forms.inputs.input-email')
      @include('stisla.auth.login.input-password')

      @include('stisla.auth.gcaptcha')

      <div class="form-group">
        <div class="custom-control custom-checkbox">
          <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
          <label class="custom-control-label" for="remember-me">{{ __('Ingat Saya') }}</label>
        </div>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
          {{ $title }}
        </button>
      </div>
    </form> --}}

    {{-- <div class="row">
      @if ($_is_login_must_verified)
        <div class="col-md-6">
          <a href=" {{ route('send-email-verification') }}" class="text-small">
            {{ __('Belum verifikasi email?') }}
          </a>
        </div>
      @endif
      @if ($_is_active_register_page)
        <div class="col-md-6 @if ($_is_login_must_verified) text-right @endif">
          <a href="{{ route('register') }}" class="text-small text-primary">Belum punya akun?</a>
        </div>
      @endif
    </div> --}}
    <h3 class="text-center text-primary">Sistem Informasi Community Development Program</h3>
    <h3 class="text-center text-primary">Fakultas Sains dan Teknologi</h3>
    <h3 class="text-center text-primary">UIN Maulana Malik Ibrahim Malang</h3>
    @include('stisla.auth.login.includes.btn-social')

    <div class="mt-5">
      <!-- Tabel Jadwal -->
      <h5 class="text-center text-primary">Jadwal Kegiatan</h5>
      <h5 class="text-center text-primary">Community Development Program Tahun {{ date('Y') }}</h5>
      <div class="schedule">
        {{-- <h3>Jadwal</h3> --}}
        <table class="table table-sm table-bordered table-striped table-hover">
          <thead class="custom-thead">
            <tr>
              <th class="text-center">No</th>
              <th>Tanggal Mulai</th>
              <th>Tanggal Selesai</th>
              <th>Kegiatan</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($jadwal as $index => $item)
              <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tgl_mulai)->translatedFormat('d F Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tgl_selesai)->translatedFormat('d F Y') }}</td>
                <td>{{ $item->keterangan }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <!-- Akhir Tabel Jadwal -->

    </div>
  @endsection

  @include('stisla.auth.script-gcaptcha')

  <style>
    .custom-thead {
      background-color: #3e5c76;
      /* Ganti dengan warna yang diinginkan */
      color: #fff !important;
    }

    .schedule table {
      width: 100%;
      border-collapse: collapse;
    }

    .schedule td {
      padding: 8px;
      text-align: left;
      border: 1px solid #424242 !important;
      color: #000 !important;
    }

    .schedule th {
      background-color: #3e5c76;
      color: white;
    }

    .schedule tr:nth-child(even) {
      background-color: #f9f9f9;
    }
  </style>
