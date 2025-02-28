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
          {{-- <div class="card-header">
            <h4><i class="fa fa-fas fa-file-text"></i> {{ $fullTitle }}</h4>
          </div> --}}
          <div class="card-body">
            <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
              @isset($d)
                @method('PUT')
              @endisset

              <div class="row">
                @if (!isset($d))
                  <div class="col-md-12">
                    @include('stisla.includes.forms.inputs.input', [
                        'required' => true,
                        'type' => 'text',
                        'id' => 'judul_proposal',
                        'name' => 'judul_proposal',
                        'label' => __('Judul Proposal'),
                        // 'disabled' => isset($d) ? true : false,
                    ])
                  </div>

                  <div class="col-md-12">
                    @include('stisla.includes.forms.inputs.input', [
                        'required' => false,
                        'disabled' => true,
                        'type' => 'text',
                        'id' => 'ketua_email',
                        'name' => 'ketua_email',
                        'label' => __('Ketua Kelompok'),
                        'value' => auth()->user()->name,
                    ])
                  </div>

                  <div class="col-md-6">
                    @include('stisla.includes.forms.selects.select2', [
                        'required' => false,
                        'disabled' => true,
                        'type' => 'text',
                        'id' => 'anggota_email',
                        'name' => 'anggota_email',
                        'label' => __('Anggota Dosen'),
                        'options' => $anggota,
                        'multiple' => true,
                    ])
                  </div>

                  <div class="col-md-6">
                    @include('stisla.includes.forms.inputs.input', [
                        'required' => true,
                        'type' => 'file',
                        'id' => 'file_proposal',
                        'name' => 'file_proposal',
                        'label' => __('File Proposal'),
                        'accept' => '.pdf',
                        'hint' => 'Format File: PDF, Maksimal 5MB',
                    ])
                  </div>
                @endif

                @if (isset($d))
                  <div class="col-md-12 mb-3">
                    {{-- <h6><i class="fas fa-file-alt me-2"></i> Judul Proposal:</h6> --}}
                    <h4>{{ $d->judul_proposal }}</h4>
                    <hr>
                  </div>

                  <div class="col-md-12">
                    <h6><i class="fas fa-users me-2"></i> Anggota Kelompok:</h6>
                    <table class="table table-sm table-striped">
                      <thead class="table-dark">
                        <tr>
                          {{-- <th>NIP</th> --}}
                          <th>Nama</th>
                          <th>Program Studi</th>
                          <th>Peran</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($anggotas as $anggota)
                          <tr>
                            {{-- <td>{{ $anggota['nip'] }}</td> --}}
                            <td>{{ $anggota['nama'] }}</td>
                            <td>{{ implode(', ', $anggota['prodi']) }}</td>
                            <td><span class="badge badge-primary">{{ $anggota['peran'] }}</span></td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                @endif

                {{-- <div class="col-md-12">
                  @include('stisla.includes.forms.inputs.input', ['required' => false, 'type' => 'text', 'id' => 'id_kelompok', 'name' => 'id_kelompok', 'label' => __('ID Kelompok')])
                </div> --}}

                {{-- MAHASISWA --}}
                {{-- <div class="col-md-4">
                  <div id="mahasiswaInputs" class="row">
                    <div class="col-md-12 form-group">
                      <label for="nim_mahasiswa_0">{{ __('NIM Mahasiswa') }}</label>
                      <input type="text" class="form-control" id="nim_mahasiswa_0" name="nim_mahasiswa[]" placeholder="{{ __('Masukkan NIM') }}">
                    </div>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <label for="nama_mahasiswa_0">{{ __('Nama Mahasiswa') }}</label>
                    <input type="text" class="form-control" id="nama_mahasiswa_0" name="nama_mahasiswa[]" placeholder="{{ __('Masukkan Nama') }}">
                  </div>
                </div>
                <div class="col-md-12">
                  <button type="button" class="btn btn-secondary" id="addMahasiswaBtn">{{ __('Tambah Mahasiswa') }}</button>
                </div> --}}
                {{-- END MAHASISWA --}}

                @if (isset($d))
                  <div class="col-md-12">
                    <h6 class="mb-3"><i class="fas fa-file-alt me-2"></i> File Proposal</h6>
                    <div class="d-flex align-items-center mb-2">
                      <div class="mr-3">
                        <i class="fas fa-file-pdf text-danger fs-5"></i>
                      </div>
                      <div class="flex-grow-1">
                        <p class="mb-0">{{ basename($d->file_proposal) }}</p>
                        <small class="text-muted">Diunggah pada: {{ $d->tgl_upload }}</small>
                      </div>
                    </div>
                    <div class="d-flex align-items-center">
                      <a href="{{ $d->file_proposal }}" class="btn btn-primary" target="_blank">
                        <i class="fas fa-eye me-2"></i> Lihat Proposal
                      </a>
                    </div>
                    {{-- <div class="form-group">
                      <h6 for="file_proposal">File Proposal</h6>
                      <hr>
                      <a class="btn " href="{{ $d->file_proposal }}" target="_blank">aa<img src="{{ Storage::url('docs.png') }}" width="150px" alt=""></a>
                    </div> --}}
                  </div>
                @else
                @endif
                {{-- 
                <div class="col-md-6">
                  @include('stisla.includes.forms.inputs.input', ['required' => true, 'type' => 'date', 'id' => 'tgl_upload', 'name' => 'tgl_upload', 'label' => __('Tanggal Upload')])
                </div>

                <div class="col-md-6">
                  @include('stisla.includes.forms.inputs.input', ['required' => true, 'type' => 'number', 'id' => 'status', 'name' => 'status', 'label' => __('Status')])
                </div>

                <div class="col-md-6">
                  @include('stisla.includes.forms.inputs.input', ['required' => true, 'type' => 'text', 'id' => 'verifikator', 'name' => 'verifikator', 'label' => __('Verifikator')])
                </div>

                <div class="col-md-6">
                  @include('stisla.includes.forms.editors.textarea', ['required' => true, 'type' => 'textarea', 'id' => 'keterangan', 'name' => 'keterangan', 'label' => __('Keterangan')])
                </div>

                <div class="col-md-6">
                  @include('stisla.includes.forms.inputs.input', [
                      'required' => true,
                      'type' => 'date',
                      'id' => 'tgl_verifikasi',
                      'name' => 'tgl_verifikasi',
                      'label' => __('Tanggal Verifikasi'),
                  ])
                </div> --}}



                <div class="col-md-12">
                  <br>

                  @csrf

                  {{-- @include('stisla.includes.forms.buttons.btn-save') --}}
                  @if (isset($d))
                    @if (auth()->user()->hasRole('Prodi'))
                      @if ($d->status == 0)
                        @include('stisla.includes.forms.buttons.btn-save', ['label' => 'Setujui', 'icon' => 'fas fa-check-circle', 'color' => 'success'])
                        {{-- @include('stisla.includes.forms.buttons.btn-tolak', ['label' => 'Tolak', 'icon' => 'fas fa-times', 'color' => 'danger']) --}}
                        @include('stisla.includes.forms.buttons.btn-modal-tolak', ['label' => 'Tolak', 'icon' => 'fas fa-times', 'color' => 'danger'])
                        {{-- @elseif ($d->status == 1) --}}
                        {{-- @include('stisla.includes.forms.buttons.btn-save', ['label' => 'Setujui', 'icon' => 'fas fa-check-circle', 'color' => 'success'])
                        @include('stisla.includes.forms.buttons.btn-tolak', ['label' => 'Tolak', 'icon' => 'fas fa-times', 'color' => 'danger']) --}}
                      @endif
                    @endif
                    @if (auth()->user()->hasRole('Dosen'))
                    @endif
                  @else
                    @if (auth()->user()->hasRole('Dosen'))
                      @include('stisla.includes.forms.buttons.btn-save', ['label' => 'Ajukan Proposal', 'icon' => 'fas fa-paper-plane'])
                    @endif
                  @endif

                  {{-- @include('stisla.includes.forms.buttons.btn-reset') --}}
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

@push('modals')
  {{-- @include('stisla.includes.modals.modal-import-excel', ['formAction' => $routeImportExcel, 'downloadLink' => $excelExampleLink]) --}}
  @include('stisla.includes.modals.modal-alasan-tolak', ['formAction' => $routeIndex])
@endpush

@push('js')
  <script>
    document.getElementById('addMahasiswaBtn').addEventListener('click', function() {
      var mahasiswaInputs = document.getElementById('mahasiswaInputs');
      var index = mahasiswaInputs.querySelectorAll('.form-group').length; // Menghitung jumlah input yang ada

      var nimInput = `
        <div class="col-md-4">
          <div class="form-group">
            <label for="nim_mahasiswa_${index}">{{ __('NIM Mahasiswa') }}</label>
            <input type="text" class="form-control" id="nim_mahasiswa_${index}" name="nim_mahasiswa[]" placeholder="{{ __('Masukkan NIM') }}">
          </div>
        </div>
      `;

      var namaInput = `
        <div class="col-md-8">
          <div class="form-group">
            <label for="nama_mahasiswa_${index}">{{ __('Nama Mahasiswa') }}</label>
            <input type="text" class="form-control" id="nama_mahasiswa_${index}" name="nama_mahasiswa[]" placeholder="{{ __('Masukkan Nama') }}">
          </div>
        </div>
      `;

      mahasiswaInputs.insertAdjacentHTML('beforeend', nimInput + namaInput);
    });
  </script>
@endpush
