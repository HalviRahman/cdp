@extends('stisla.layouts.app')

@section('title')
  {{ $fullTitle }}
@endsection

@section('content')
  @include('stisla.includes.breadcrumbs.breadcrumb-form')

  <div class="section-body">

    {{-- <h2 class="section-title">{{ $fullTitle }}</h2>
    <p class="section-lead">{{ __('Merupakan halaman yang menampilkan form ' . $title) }}.</p> --}}

    {{-- gunakan jika ingin menampilkan sesuatu informasi --}}
    <div class="alert alert-info alert-has-icon">
      <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
      <div class="alert-body">
        <div class="alert-title">{{ __('Informasi') }}</div>
        Tambahkan dan atur kuota program studi setiap tahun.
      </div>
    </div>

    <div class="row">
      <div class="col-12">

        <div class="card">
          <div class="card-header">
            {{-- <h4><i class="fa fa-fas fa-building"></i> {{ $fullTitle }}</h4> --}}
            <h4><i class="fa fa-fas fa-building"></i> Tambah dan Atur Kuota Program Studi</h4>
          </div>
          <div class="card-body">
            <form action="{{ $action }}" method="POST" enctype="multipart/form-data">

              @isset($d)
                @method('PUT')
              @endisset

              <div class="row">

                @if (auth()->user()->hasRole('Fakultas'))
                  <div class="col-md-2">
                    @include('stisla.includes.forms.inputs.input', [
                        'disabled' => true,
                        'required' => true,
                        'type' => 'text',
                        'id' => 'jenjang',
                        'name' => 'jenjang',
                        'label' => __('Jenjang'),
                        'options' => ['S1' => 'S1', 'S2' => 'S2', 'Kolaborasi' => 'Kolaborasi'],
                    ])
                  </div>
                  {{-- @if (auth()->user()->hasRole('Sysadmin')) --}}
                  <div class="col-md-10">
                    @include('stisla.includes.forms.inputs.input', [
                        'disabled' => true,
                        'required' => true,
                        'type' => 'text',
                        'id' => 'nama_prodi',
                        'name' => 'nama_prodi',
                        'label' => __('Program Studi'),
                    ])
                  </div>
                  {{-- @elseif (auth()->user()->hasRole('Fakultas')) --}}
                  {{-- <div class="col-md-10">
                    @include('stisla.includes.forms.selects.select', [
                        'required' => true,
                        'type' => 'text',
                        'id' => 'nama_prodi',
                        'name' => 'nama_prodi',
                        'label' => __('Program Studi'),
                        'options' => [
                            'Biologi' => 'Biologi',
                            'Matematika' => 'Matematika',
                            'Kimia' => 'Kimia',
                            'Fisika' => 'Fisika',
                            'Teknik Informatika' => 'Teknik Informatika',
                            'Teknik Arsitektur' => 'Teknik Arsitektur',
                            'Perpustakaan dan Sains Informasi' => 'Perpustakaan dan Sains Informasi',
                            'Magister Informatika' => 'Magister Informatika',
                            'Magister Biologi' => 'Magister Biologi',
                        ],
                    ])
                  </div> --}}
                  {{-- @endif --}}

                  <div class="col-md-2">
                    @include('stisla.includes.forms.inputs.input', [
                        'required' => true,
                        'type' => 'text',
                        'id' => 'tahun',
                        'name' => 'tahun',
                        'label' => __('Tahun'),
                        'disabled' => true,
                        'options' => array_combine(range(date('Y'), date('Y') + 5), range(date('Y'), date('Y') + 5)),
                    ])
                  </div>
                @else
                  <div class="col-md-2">
                    @include('stisla.includes.forms.selects.select', [
                        'required' => true,
                        'type' => 'text',
                        'id' => 'jenjang',
                        'name' => 'jenjang',
                        'label' => __('Jenjang'),
                        'options' => ['S1' => 'S1', 'S2' => 'S2', 'Kolaborasi' => 'Kolaborasi'],
                    ])
                  </div>
                  {{-- @if (auth()->user()->hasRole('Sysadmin')) --}}
                  <div class="col-md-10">
                    @include('stisla.includes.forms.inputs.input', ['required' => true, 'type' => 'text', 'id' => 'nama_prodi', 'name' => 'nama_prodi', 'label' => __('Program Studi')])
                  </div>
                  {{-- @elseif (auth()->user()->hasRole('Fakultas')) --}}
                  {{-- <div class="col-md-10">
                    @include('stisla.includes.forms.selects.select', [
                        'required' => true,
                        'type' => 'text',
                        'id' => 'nama_prodi',
                        'name' => 'nama_prodi',
                        'label' => __('Program Studi'),
                        'options' => [
                            'Biologi' => 'Biologi',
                            'Matematika' => 'Matematika',
                            'Kimia' => 'Kimia',
                            'Fisika' => 'Fisika',
                            'Teknik Informatika' => 'Teknik Informatika',
                            'Teknik Arsitektur' => 'Teknik Arsitektur',
                            'Perpustakaan dan Sains Informasi' => 'Perpustakaan dan Sains Informasi',
                            'Magister Informatika' => 'Magister Informatika',
                            'Magister Biologi' => 'Magister Biologi',
                        ],
                    ])
                  </div> --}}
                  {{-- @endif --}}

                  <div class="col-md-2">
                    @include('stisla.includes.forms.selects.select', [
                        'required' => true,
                        'type' => 'text',
                        'id' => 'tahun',
                        'name' => 'tahun',
                        'label' => __('Tahun'),
                        'options' => array_combine(range(date('Y'), date('Y') + 5), range(date('Y'), date('Y') + 5)),
                    ])
                  </div>
                @endif
                <div class="col-md-10">
                  @include('stisla.includes.forms.inputs.input', ['required' => true, 'type' => 'number', 'id' => 'kuota', 'name' => 'kuota', 'label' => __('Kuota')])
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
