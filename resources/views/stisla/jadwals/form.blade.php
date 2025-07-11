@extends('stisla.layouts.app')

@section('title')
  {{ $fullTitle }}
@endsection

@section('content')
  @include('stisla.includes.breadcrumbs.breadcrumb-form')

  <div class="section-body">

    <h2 class="section-title">Jadwal CDP</h2>
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
            <h4><i class="fa fa-fas fa-calendar"></i> {{ $fullTitle }}</h4>
          </div>
          <div class="card-body">
            <form action="{{ $action }}" method="POST" enctype="multipart/form-data">

              @isset($d)
                @method('PUT')
              @endisset

              <div class="row">
                <div class="col-md-6">
                  @include('stisla.includes.forms.inputs.input', [
                      'required' => true,
                      'type' => 'date',
                      // 'type' => 'datetime-local',
                      'id' => 'tgl_mulai',
                      'name' => 'tgl_mulai',
                      'label' => __('Tanggal Mulai'),
                  ])
                </div>

                <div class="col-md-6">
                  @include('stisla.includes.forms.inputs.input', [
                      'required' => true,
                      'type' => 'date',
                      // 'type' => 'datetime-local',
                      'id' => 'tgl_selesai',
                      'name' => 'tgl_selesai',
                      'label' => __('Tanggal Selesai'),
                  ])
                </div>

                <div class="col-md-12">
                  @include('stisla.includes.forms.selects.select', [
                      'required' => true,
                      'type' => 'text',
                      'id' => 'keterangan',
                      'name' => 'keterangan',
                      'label' => __('Kegiatan'),
                      'options' => [
                          'Sosialisasi' => 'Sosialisasi',
                          'Pengajuan Proposal' => 'Pengajuan Proposal',
                          'Verifikasi Proposal' => 'Verifikasi Proposal',
                          'Penerbitan SK' => 'Penerbitan SK',
                          'Pelaksanaan Pengabdian' => 'Pelaksanaan Pengabdian',
                          'Pengumpulan Laporan' => 'Pengumpulan Laporan',
                          'Review Laporan' => 'Review Laporan',
                          'Proses Pencairan' => 'Proses Pencairan',
                      ],
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
