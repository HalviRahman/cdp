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
                  @php
                    $userProdi = auth()->user()->prodi;
                    $prodiOptions = [];
                    foreach ($userProdi as $prodi) {
                        $prodiOptions[$prodi] = $prodi;
                    }
                  @endphp
                  @if (count($availableProdi) > 0 && count(auth()->user()->prodi) > 1)
                    <div class="col-md-12">
                      <div class="form-group">
                        <label>Program Studi</label>
                        <select name="prodi" class="form-control" required>
                          @foreach ($availableProdi as $prodi)
                            <option value="{{ $prodi['nama'] }}">{{ $prodi['nama'] }} (Sisa Kuota: {{ $prodi['kuota_tersisa'] }})</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  @endif
                  {{-- @if (isset($userProdi) && count($userProdi) > 1)
                    <div class="col-md-12">
                      @include('stisla.includes.forms.selects.select', [
                          'required' => false,
                          'disabled' => true,
                          'type' => 'text',
                          'id' => 'prodi',
                          'name' => 'prodi',
                          'label' => __('Program Studi'),
                          'options' => $prodiOptions,
                          'multiple' => false,
                      ])
                    </div>
                  @endif --}}
                  {{-- MAHASISWA --}}
                  <div class="col-md-12">
                    <h6 class="mb-3"><i class="fas fa-users me-2"></i> Tambah Mahasiswa</h6>
                    <div id="mahasiswaInputs">
                      <div class="row mahasiswa-row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>{{ __('NIM Mahasiswa') }}</label>
                            <input type="text" class="form-control" name="nim_mahasiswa[]" placeholder="{{ __('Masukkan NIM') }}">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>{{ __('Nama Mahasiswa') }}</label>
                            <input type="text" class="form-control" name="nama_mahasiswa[]" placeholder="{{ __('Masukkan Nama') }}">
                          </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                          <button type="button" class="btn btn-danger btn-remove-mahasiswa mb-3" style="display:none;">
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                    <button type="button" class="btn btn-info" id="addMahasiswaBtn">
                      <i class="fas fa-plus"></i> {{ __('Tambah Mahasiswa') }}
                    </button>
                  </div>
                  {{-- END MAHASISWA --}}
                @endif

                @php
                  // Cek periode pengajuan proposal (untuk dosen)
                  $jadwal_pengajuan = \App\Models\Jadwal::where('keterangan', 'Pengajuan Proposal')->first();
                  $can_edit = $jadwal_pengajuan && now()->between($jadwal_pengajuan->tgl_mulai, $jadwal_pengajuan->tgl_selesai);

                  // Cek periode verifikasi (untuk prodi/koordinator)
                  $jadwal_verifikasi = \App\Models\Jadwal::where('keterangan', 'Verifikasi Proposal')->first();
                  $can_verify = $jadwal_verifikasi && now()->between($jadwal_verifikasi->tgl_mulai, $jadwal_verifikasi->tgl_selesai);

                  // Cek periode pengumpulan laporan
                  $jadwal_laporan = \App\Models\Jadwal::where('keterangan', 'Pengumpulan Laporan')->first();
                  $can_upload_laporan = $jadwal_laporan && now()->between($jadwal_laporan->tgl_mulai, $jadwal_laporan->tgl_selesai);
                @endphp

                @if (isset($d))
                  <div class="col-md-12 mb-3">
                    @if (auth()->user()->hasRole('Dosen') && !$can_edit)
                      <div class="alert alert-warning">
                        <i class="fas fa-clock"></i> Periode pengajuan/edit proposal:
                        {{ \Carbon\Carbon::parse($jadwal_pengajuan->tgl_mulai)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($jadwal_pengajuan->tgl_selesai)->format('d M Y') }}
                      </div>
                    @endif
                    {{-- @if (auth()->user()->hasRole('Dosen') && !$can_upload_laporan)
                      <div class="alert alert-warning">
                        <i class="fas fa-clock"></i> Periode pengumpulan laporan:
                        {{ \Carbon\Carbon::parse($jadwal_laporan->tgl_mulai)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($jadwal_laporan->tgl_selesai)->format('d M Y') }}
                      </div> --}}
                    @if (auth()->user()->hasRole('Dosen') && $can_upload_laporan && $d->status == 2)
                      <div class="alert alert-warning">
                        <i class="fas fa-clock"></i> Anda masih bisa mengedit laporan kegiatan dan laporan perjalanan selama periode pengumpulan laporan
                        ( {{ \Carbon\Carbon::parse($jadwal_laporan->tgl_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($jadwal_laporan->tgl_selesai)->format('d M Y') }} ).
                      </div>
                    @endif
                    <h5>{{ $d->judul_proposal }}</h5>
                    <hr>
                  </div>

                  {{-- Form edit untuk dosen selama periode pengajuan --}}
                  @if (auth()->user()->hasRole('Dosen') && $d->status == 0 && $can_edit)
                    <div class="col-md-12">
                      @include('stisla.includes.forms.inputs.input', [
                          'required' => true,
                          'type' => 'text',
                          'id' => 'judul_proposal',
                          'name' => 'judul_proposal',
                          'label' => __('Judul Proposal'),
                          'value' => $d->judul_proposal,
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
                          'label' => __('File Proposal Baru'),
                          'accept' => '.pdf',
                          'hint' => 'Format File: PDF, Maksimal 5MB',
                      ])
                    </div>
                    <div class="col-md-12 mb-3">
                      <h6 class="mb-3"><i class="fas fa-users me-2"></i> Tambah Mahasiswa</h6>
                      <div id="mahasiswaInputs">
                        <div class="row mahasiswa-row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label>{{ __('NIM Mahasiswa') }}</label>
                              <input type="text" class="form-control" name="nim_mahasiswa[]" placeholder="{{ __('Masukkan NIM') }}">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label>{{ __('Nama Mahasiswa') }}</label>
                              <input type="text" class="form-control" name="nama_mahasiswa[]" placeholder="{{ __('Masukkan Nama') }}">
                            </div>
                          </div>
                          <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-remove-mahasiswa mb-3" style="display:none;">
                              <i class="fas fa-trash"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                      <button type="button" class="btn btn-info" id="addMahasiswaBtn">
                        <i class="fas fa-plus"></i> {{ __('Tambah Mahasiswa') }}
                      </button>
                    </div>
                  @endif

                  {{-- Form verifikasi untuk prodi selama periode verifikasi --}}
                  {{-- @if (auth()->user()->hasRole('Prodi') && isset($d) && $d->status == 0 && $can_verify)
                    <div class="col-md-12">
                      @include('stisla.includes.forms.editors.textarea', [
                          'required' => true,
                          'id' => 'keterangan',
                          'name' => 'keterangan',
                          'label' => __('Catatan Verifikasi'),
                          'value' => old('keterangan'),
                      ])
                    </div>
                  @endif --}}

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
                        @foreach ($mahasiswas as $mahasiswa)
                          <tr>
                            {{-- <td>{{ $anggota['nip'] }}</td> --}}
                            <td>{{ $mahasiswa['nip'] }} - {{ $mahasiswa['name'] }}</td>
                            <td><span class="badge badge-info">Mahasiswa</span></td>
                            <td><span class="badge badge-primary">Anggota</span></td>
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
                    <div class="d-flex flex-column flex-md-row align-items-md-center mb-3">
                      <div class="d-flex align-items-center flex-grow-1 mb-2 mb-md-0">
                        <div class="mr-3">
                          <i class="fas fa-file-pdf text-danger fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                          <p class="mb-0">{{ basename($d->file_proposal) }}</p>
                          <small class="text-muted">Diunggah pada: {{ $d->tgl_upload }}</small>
                        </div>
                      </div>
                      <div>
                        <a href="{{ $d->file_proposal }}" class="btn btn-sm btn-primary" target="_blank">
                          <i class="fas fa-eye"></i> Lihat
                        </a>
                      </div>
                    </div>
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

                {{-- Form upload laporan untuk dosen selama periode pengumpulan laporan --}}
                @if (auth()->user()->hasRole('Dosen') && isset($d) && $d->status == 2 && $can_upload_laporan)
                  <div class="col-md-12">
                    <h6 class="mb-3 mt-3"><i class="fas fa-upload me-2"></i> Upload Laporan</h6>

                    <div class="row">
                      <div class="col-md-6">
                        @include('stisla.includes.forms.inputs.input', [
                            'required' => true,
                            'type' => 'file',
                            'id' => 'laporan_kegiatan',
                            'name' => 'laporan_kegiatan',
                            'label' => __('File Laporan Kegiatan'),
                            'accept' => '.pdf',
                            'hint' => 'Format File: PDF, Maksimal 5MB',
                        ])
                      </div>

                      <div class="col-md-6">
                        @include('stisla.includes.forms.inputs.input', [
                            'required' => true,
                            'type' => 'file',
                            'id' => 'laporan_perjalanan',
                            'name' => 'laporan_perjalanan',
                            'label' => __('File Laporan Perjalanan'),
                            'accept' => '.pdf',
                            'hint' => 'Format File: PDF, Maksimal 5MB',
                        ])
                      </div>
                    </div>
                  </div>
                @endif

                {{-- Tampilkan file laporan jika sudah diupload --}}
                @if (isset($d) && $d->status == 0)
                @elseif (isset($d) && ($d->laporan_kegiatan || $d->laporan_perjalanan))
                  <div class="col-md-12">
                    <h6 class="mb-3 mt-3"><i class="fas fa-file-alt me-2"></i> File Laporan</h6>

                    @if ($d->laporan_kegiatan)
                      <div class="d-flex flex-column flex-md-row align-items-md-center mb-3">
                        <div class="d-flex align-items-center flex-grow-1 mb-2 mb-md-0">
                          <div class="mr-3">
                            <i class="fas fa-file-pdf text-danger fs-5"></i>
                          </div>
                          <div class="flex-grow-1">
                            <p class="mb-0">Kegiatan - {{ basename($d->laporan_kegiatan) }}</p>
                            <small class="text-muted">Diunggah pada: {{ $d->tgl_upload_laporan }}</small>
                          </div>
                        </div>
                        <div>
                          <a href="{{ $d->laporan_kegiatan }}" class="btn btn-sm btn-primary" target="_blank">
                            <i class="fas fa-eye"></i> Lihat
                          </a>
                        </div>
                      </div>
                    @endif

                    @if ($d->laporan_perjalanan)
                      <div class="d-flex flex-column flex-md-row align-items-md-center mb-3">
                        <div class="d-flex align-items-center flex-grow-1 mb-2 mb-md-0">
                          <div class="mr-3">
                            <i class="fas fa-file-pdf text-danger fs-5"></i>
                          </div>
                          <div class="flex-grow-1">
                            <p class="mb-0">Perjalanan - {{ basename($d->laporan_perjalanan) }}</p>
                            <small class="text-muted">Diunggah pada: {{ $d->tgl_upload_laporan }}</small>
                          </div>
                        </div>
                        <div>
                          <a href="{{ $d->laporan_perjalanan }}" class="btn btn-sm btn-primary" target="_blank">
                            <i class="fas fa-eye"></i> Lihat
                          </a>
                        </div>
                      </div>
                    @endif
                  </div>
                @endif

                {{-- Tombol aksi --}}
                <div class="col-md-12">
                  <br>
                  @csrf
                  @if (auth()->user()->hasRole('Dosen'))
                    @if (!isset($d))
                      @include('stisla.includes.forms.buttons.btn-save', [
                          'label' => 'Ajukan Proposal',
                          'icon' => 'fas fa-save',
                          'color' => 'primary',
                          'block' => 'btn-block',
                      ])
                    @elseif (isset($d) && $d->status == 0 && $can_edit)
                      @include('stisla.includes.forms.buttons.btn-save', [
                          'label' => 'Update Proposal',
                          'icon' => 'fas fa-save',
                          'color' => 'warning',
                          'float' => 'right',
                          'block' => 'btn-block',
                      ])
                    @elseif (isset($d) && $d->status == 2 && $can_upload_laporan)
                      @include('stisla.includes.forms.buttons.btn-save', [
                          'label' => 'Upload Laporan',
                          'icon' => 'fas fa-upload',
                          'color' => 'info',
                          'float' => 'right',
                          'block' => 'btn-block',
                      ])
                    @elseif (isset($d) && !$can_upload_laporan && $d->status == 2)
                      <div class="alert alert-warning">
                        <i class="fas fa-clock"></i> Periode pengumpulan laporan:
                        {{ \Carbon\Carbon::parse($jadwal_laporan->tgl_mulai)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($jadwal_laporan->tgl_selesai)->format('d M Y') }}
                      </div>
                    @endif
                  @endif

                  {{-- Verifikasi oleh Koordinator Prodi --}}
                  @if (auth()->user()->hasRole('Koordinator Prodi'))
                    @if (isset($d) && $d->status == 0 && $can_verify)
                      @include('stisla.includes.forms.buttons.btn-save', [
                          'label' => 'Setujui',
                          'icon' => 'fas fa-check-circle',
                          'color' => 'success',
                      ])
                      @include('stisla.includes.forms.buttons.btn-modal-tolak', [
                          'label' => 'Tolak',
                          'icon' => 'fas fa-times',
                          'color' => 'danger',
                      ])
                    @elseif(!$can_verify)
                      <div class="alert alert-warning">
                        <i class="fas fa-clock"></i> Periode verifikasi proposal:
                        {{ \Carbon\Carbon::parse($jadwal_verifikasi->tgl_mulai)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($jadwal_verifikasi->tgl_selesai)->format('d M Y') }}
                      </div>
                    @endif
                  @endif

                  {{-- Verifikasi oleh Prodi --}}
                  @if (auth()->user()->hasRole('Prodi') && auth()->user()->kaprodi == $d->prodi)
                    @if (isset($d) && $d->status == 1 && $can_verify)
                      @include('stisla.includes.forms.buttons.btn-save', [
                          'label' => 'Setujui',
                          'icon' => 'fas fa-check-circle',
                          'color' => 'success',
                      ])
                      @include('stisla.includes.forms.buttons.btn-modal-tolak', [
                          'label' => 'Tolak',
                          'icon' => 'fas fa-times',
                          'color' => 'danger',
                      ])
                    @elseif(!$can_verify)
                      <div class="alert alert-warning">
                        <i class="fas fa-clock"></i> Periode verifikasi proposal:
                        {{ \Carbon\Carbon::parse($jadwal_verifikasi->tgl_mulai)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($jadwal_verifikasi->tgl_selesai)->format('d M Y') }}
                      </div>
                    @endif
                  @endif

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
    $(document).ready(function() {
      $('#addMahasiswaBtn').click(function() {
        let newRow = $('.mahasiswa-row').first().clone();
        newRow.find('input').val('');
        newRow.find('.btn-remove-mahasiswa').show();
        $('#mahasiswaInputs').append(newRow);
      });

      $(document).on('click', '.btn-remove-mahasiswa', function() {
        if ($('.mahasiswa-row').length > 1) {
          $(this).closest('.mahasiswa-row').remove();
        }
      });
    });
  </script>
@endpush
