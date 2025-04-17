@extends('stisla.layouts.app-auth-simple')

@section('title')
  {{ $title = __('Form Pendaftaran SIM CDP') }}
@endsection

@section('content')
  <div class="card-body">
    <form method="POST" action="" class="needs-validation" novalidate="">
      @csrf
      @include('stisla.includes.forms.inputs.input-name', ['required' => true, 'hint' => 'Masukkan nama lengkap beserta gelar, Contoh: Dr. Soekarno, M.Si'])
      @include('stisla.includes.forms.inputs.input', [
          'required' => true,
          'id' => 'nip',
          'name' => 'nip',
          'label' => 'NIP',
          'icon' => 'fas fa-id-card',
      ])
      @include('stisla.includes.forms.selects.select2', [
          'required' => true,
          'id' => 'prodi',
          'name' => 'prodi',
          'required' => true,
          'multiple' => true,
          'label' => 'Program Studi',
          'options' => $prodiOptions,
      ])

      @include('stisla.includes.forms.inputs.input-email', ['hint' => 'Gunakan email UIN Malang (uin-malang.ac.id)'])
      {{-- @include('stisla.auth.login.input-password') --}}

      {{-- <div class="form-group">
        <div class="custom-control custom-checkbox">
          <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
          <label class="custom-control-label" for="remember-me">{{ __('Ingat Saya') }}</label>
        </div>
      </div> --}}

      <div class="form-group">
        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
          Daftar
        </button>
      </div>
    </form>

  </div>
@endsection
