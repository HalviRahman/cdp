@extends('stisla.layouts.app')

@section('title')
  {{ $fullTitle }}
@endsection

@section('content')
  @include('stisla.includes.breadcrumbs.breadcrumb-form')

  <div class="section-body">

    <h2 class="section-title">Kelompok</h2>
    {{-- <p class="section-lead">{{ __('Merupakan halaman yang menampilkan form ' . $title) }}.</p> --}}

    {{-- gunakan jika ingin menampilkan sesuatu informasi --}}
    {{-- <div class="alert alert-info alert-has-icon">
      <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
      <div class="alert-body">
        <div class="alert-title">{{ __('Informasi') }}</div>
        This is a info alert.
      </div>
    </div> --}}

    <div class="row">
      <div class="col-12">

        <div class="card">
          <div class="card-header">
            <h4><i class="fa fa-fas fa-users"></i> {{ $fullTitle }}</h4>
          </div>
          <div class="card-body">
            <form action="{{ $action }}" method="POST" enctype="multipart/form-data">

              @isset($d)
                @method('PUT')
              @endisset

              <div class="row">
                <div class="col-md-12">
                  @include('stisla.includes.forms.inputs.input', [
                      'required' => true,
                      'type' => 'text',
                      'id' => 'id_kelompok',
                      'name' => 'id_kelompok',
                      'label' => __('ID Kelompok'),
                      'disabled' => true,
                  ])
                </div>
                <div class="col-md-6">
                  @include('stisla.includes.forms.inputs.input', [
                      'required' => true,
                      'type' => 'text',
                      'id' => 'anggota_email',
                      'name' => 'anggota_email',
                      'label' => __('Anggota Email'),
                  ])
                </div>

                <div class="col-md-6">
                  @include('stisla.includes.forms.selects.select', [
                      'required' => true,
                      'type' => 'text',
                      'id' => 'peran',
                      'name' => 'peran',
                      'label' => __('Peran'),
                      'options' => ['Ketua' => 'Ketua', 'Anggota' => 'Anggota'],
                  ])
                </div>



                <div class="col-md-12">
                  <br>

                  @csrf

                  @include('stisla.includes.forms.buttons.btn-save')
                  @include('stisla.includes.forms.buttons.btn-reset')
                </div>
              </div>
            </form>
          </div>
        </div>

      </div>

    </div>
  </div>
@endsection

@push('css')
@endpush

@push('js')
@endpush
