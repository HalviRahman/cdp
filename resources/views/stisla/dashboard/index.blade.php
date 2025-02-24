@extends('stisla.layouts.app-table')

@section('title')
  Dashboard
@endsection

@section('content')
  <div class="section-header">
    {{-- <h1>{{ __('Dashboard') }}</h1> --}}
    <h1>Dashboard</h1>
  </div>
  <div class="row">
    {{-- <div class="col-12 mb-4">
      <div class="hero text-white hero-bg-image" data-background="{{ $_stisla_bg_home }}">
        <div class="hero-inner">
          <h2 class="float-left">{{ __('Halo') }}, {{ Auth::user()->name ?? 'Your Name' }}</h2>
          <div class="mt-4 float-right">
            <a href="{{ route('profile.index') }}" class="btn btn-outline-white btn-lg btn-icon icon-left">
              <i class="far fa-user"></i> {{ __('Lihat Profil') }}
            </a>
          </div>
          <div style="clear: both;"></div>
          <p class="lead">{{ $_app_description }}</p>
        </div>
      </div>
    </div> --}}

    {{-- @foreach ($widgets ?? range(1, 8) as $item)
      <div class="col-lg-3 col-md-3 col-sm-6 col-12">
        <div class="card card-statistic-1" @if ($item->route ?? false) onclick="openTo('{{ $item->route }}')" style="cursor: pointer;" @endif>
          <div class="card-icon bg-{{ $item->bg ?? 'primary' }}">
            <i class="fas fa-{{ $item->icon ?? 'fire' }}"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ $item->title ?? 'Nama Modul' }}</h4>
            </div>
            <div class="card-body">
              {{ $item->count ?? $loop->iteration . '00' }}
            </div>
          </div>
        </div>
      </div>
    @endforeach --}}

    {{-- @if ($user->can('Log Aktivitas'))
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4><i class="fa fa-clock-rotate-left"></i> {{ __('Log Aktivitas Terbaru') }}</h4>

          </div>
          <div class="card-body">
            <div class="table-responsive">

              <table class="table table-striped table-hovered" id="datatable">
                <thead>
                  <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">{{ __('Judul') }}</th>
                    <th class="text-center">{{ __('Jenis') }}</th>
                    <th class="text-center">{{ __('Request Data') }}</th>
                    <th class="text-center">{{ __('Before') }}</th>
                    <th class="text-center">{{ __('After') }}</th>
                    <th class="text-center">{{ __('IP') }}</th>
                    <th class="text-center">{{ __('User Agent') }}</th>
                    <th class="text-center">{{ __('Pengguna') }}</th>
                    <th class="text-center">{{ __('Role') }}</th>
                    <th class="text-center">{{ __('Created At') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($logs as $item)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $item->title }}</td>
                      <td>{{ $item->activity_type }}</td>
                      <td>
                        <textarea>{{ $item->request_data }}</textarea>
                      </td>
                      <td>
                        <textarea>{{ $item->before }}</textarea>
                      </td>
                      <td>
                        <textarea>{{ $item->after }}</textarea>
                      </td>
                      <td>{{ $item->ip }}</td>
                      <td>{{ $item->user_agent }}</td>
                      <td>{{ $item->user->name }}</td>
                      <td>{{ implode(', ', $item->roles) }}</td>
                      <td>{{ $item->created_at }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    @endif --}}
    @if (auth()->user()->hasRole('Dosen'))
      <div class="col-12">
        <div class="card status-card status-pending mb-4 author-box card-warning">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h5 class="card-title mb-1">Pengembangan Media Pembelajaran Berbasis AR</h5>
                <p class="card-text text-muted mb-0">Status: Menunggu Verifikasi</p>
              </div>
              <div>
                <button class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-eye me-2"></i>Detail
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif

    @if (auth()->user()->hasRole('Fakultas'))
      {{-- Box 1 --}}
      <div class="col-12 col-sm-12 col-lg-6">
        <div class="card author-box card-warning">
          <div class="card-body">
            <div class="card-body">
              <div class="author-box-name text-center">
                <a href="#" class="text-dark">Jadwal Kegiatan</a>
                <div class="author-box-job text-center">
                  <p>Kelola jadwal kegiatan CDP</p>
                </div>
                <div class="author-box-job text-center">
                  <a href="{{ route('jadwals.index') }}" class="btn btn-warning mt-3">
                    <i class="fa fa-eye"></i> Akses
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      {{-- Box 2 - Rekap Pendaftaran --}}
      <div class="col-12 col-sm-12 col-lg-6">
        <div class="card author-box card-success">
          <div class="card-body">
            <div class="card-body">
              <div class="author-box-name text-center">
                <a href="#" class="text-dark">Rekap Pendaftaran</a>
                <div class="author-box-job text-center">
                  <p>Lihat rekap pendaftaran peserta</p>
                </div>
                <div class="author-box-job text-center">
                  <a href="#" class="btn btn-success mt-3">
                    <i class="fa fa-eye"></i> Akses
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      {{-- Box 3 - Rekap Laporan --}}
      <div class="col-12 col-sm-12 col-lg-6">
        <div class="card author-box card-info">
          <div class="card-body">
            <div class="card-body">
              <div class="author-box-name text-center">
                <a href="#" class="text-dark">Rekap Laporan</a>
                <div class="author-box-job text-center">
                  <p>Lihat rekap laporan kegiatan CDP</p>
                </div>
                <div class="author-box-job text-center">
                  <a href="{{ route('laporans.index') }}" class="btn btn-info mt-3">
                    <i class="fa fa-eye"></i> Akses
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      {{-- Box 4 - Kuota Proposal --}}
      <div class="col-12 col-sm-12 col-lg-6">
        <div class="card author-box card-danger">
          <div class="card-body">
            <div class="card-body">
              <div class="author-box-name text-center">
                <a href="{{ route('program-studis.index') }}" class="text-dark">Kuota Proposal</a>
                <div class="author-box-job text-center">
                  <p>Setting kuota proposal Prodi</p>
                </div>
                <div class="author-box-job text-center">
                  <a href="{{ route('program-studis.index') }}" class="btn btn-danger mt-3">
                    <i class="fa fa-eye"></i> Akses
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
    @if (auth()->user()->hasRole('Dosen'))
      {{-- Box 5 - Ajukan Proposal --}}
      <div class="col-12 col-sm-12 col-lg-6">
        <div class="card author-box card-primary">
          <div class="card-body">
            <div class="card-body">
              <div class="author-box-name text-center">
                <a href="#" class="text-dark">Ajukan Proposal</a>
                <div class="author-box-job text-center">
                  <p>Ajukan proposal CDP baru sebagai ketua atau anggota kelompok</p>
                </div>
                <div class="author-box-job text-center">
                  <a href="{{ route('proposals.create') }}" class="btn btn-primary mt-3">
                    <i class="fa fa-plus-circle"></i> Ajukan
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      {{-- Box 6 - Upload Laporan --}}
      @php
        $kelompok = \App\Models\Kelompok::where('anggota_email', auth()->user()->email)
            ->where('peran', 'Ketua')
            ->first();
        $proposal = $kelompok ? \App\Models\Proposal::where('id_kelompok', $kelompok->id_kelompok)->where('status', '3')->first() : null;
      @endphp
      @if ($proposal)
        <div class="col-12 col-sm-12 col-lg-6">
          <div class="card author-box card-success">
            <div class="card-body">
              <div class="card-body">
                <div class="author-box-name text-center">
                  <a href="#" class="text-dark">Upload Laporan</a>
                  <div class="author-box-job text-center">
                    <p>Upload laporan kegiatan dan laporan keuangan CDP</p>
                  </div>
                  <div class="author-box-job text-center">
                    <a href="{{ route('laporans.create') }}" class="btn btn-success mt-3">
                      <i class="fa fa-upload"></i> Upload
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      @endif
    @endif

    @if (auth()->user()->hasRole('Sysadmin'))
      {{-- Box 1 - Kelola Pengguna --}}
      <div class="col-12 col-sm-12 col-lg-6">
        <div class="card author-box card-warning">
          <div class="card-body">
            <div class="card-body">
              <div class="author-box-name text-center">
                <a href="#" class="text-dark">Pengguna</a>
                <div class="author-box-job text-center">
                  <p>Kelola data pengguna sistem</p>
                </div>
                <div class="author-box-job text-center">
                  <a href="{{ route('user-management.users.index') }}" class="btn btn-warning mt-3">
                    <i class="fa fa-gear"></i> Kelola Pengguna
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      {{-- Box 2 - Kelola Prodi --}}
      <div class="col-12 col-sm-12 col-lg-6">
        <div class="card author-box card-success">
          <div class="card-body">
            <div class="card-body">
              <div class="author-box-name text-center">
                <a href="#" class="text-dark">Program Studi</a>
                <div class="author-box-job text-center">
                  <p>Kelola data program studi</p>
                </div>
                <div class="author-box-job text-center">
                  <a href="{{ route('program-studis.index') }}" class="btn btn-success mt-3">
                    <i class="fa fa-building"></i> Kelola Prodi
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
    @if (auth()->user()->hasRole('Prodi'))
      <div class="col-12">
        <div class="card">
          <div class="card-body">

            <form action="">
              @csrf
              <div class="row">

                <div class="col-md-3">
                  @include('stisla.includes.forms.selects.select', [
                      'id' => 'tahun',
                      'name' => 'tahun',
                      'required' => true,
                      'options' => array_combine(range(date('Y'), date('Y')), range(date('Y'), date('Y'))),
                      'label' => __('Tahun'),
                      'value' => request('tahun'),
                      'selected' => request('tahun'),
                  ])
                </div>
              </div>
              <button class="btn btn-primary icon"><i class="fa fa-search"></i> Cari Data</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-6 col-12 stats-card">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-file-text"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4 class="text-dark">Total Proposal</h4>
            </div>
            <div class="card-body">
              <h4>{{ $dataProposal->count() }}</h4>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-6 col-12 stats-card">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="fas fa-file-text"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4 class="text-dark">Kuota</h4>
            </div>
            <div class="card-body">
              {{-- <h4>{{ $programStudi->kuota }} </h4> --}}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-6 col-12 stats-card">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-file-text"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4 class="text-dark">Menunggu Verifikasi</h4>
            </div>
            <div class="card-body">
              <h4>{{ $dataProposal->where('status', '0')->count() }}</h4>
            </div>
          </div>
        </div>
      </div>


      <div class="col-12">
        <h2 class="section-title">Daftar Proposal</h2>
        @foreach ($dataProposal as $proposal)
          <div class="card">
            <div class="card-body">
              <h5>{{ $proposal->judul_proposal }}</h5>
              <p><strong>Ketua:</strong> {{ $proposal->ketuaKelompok->user->name }} - {{ implode('; ', json_decode($proposal->ketuaKelompok->user->prodi, true)) }}
              </p>
              @if ($proposal->verifikator)
                <p>Verifikator: {{ $proposal->verifikator }} </p>
                <p>Tanggal Verifikasi: {{ $proposal->tgl_verifikasi }} WIB</p>
              @endif
              @if ($proposal->status == '0')
                <span class="badge badge-warning">Menunggu Verifikasi</span>
              @elseif($proposal->status == '1')
                <span class="badge badge-success">Disetujui</span>
              @elseif($proposal->status == '10')
                <span class="badge badge-danger">Ditolak</span>
                <p>Alasan: {{ $proposal->keterangan }}</p>
              @else
                <span class="badge badge-warning">Menunggu Verifikasi</span>
              @endif
              <div class="float-right">
                <a href="{{ route('proposals.edit', $proposal->id) }}" class="btn btn-outline-info">Detail</a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  @if (auth()->user()->hasRole('Keuangan'))
    <div class="card">
      <div class="card-body">

        <form action="">
          @csrf
          <div class="row">

            <div class="col-md-3">
              @include('stisla.includes.forms.selects.select', [
                  'id' => 'tahun',
                  'name' => 'tahun',
                  'required' => true,
                  'options' => array_combine(range(date('Y'), date('Y')), range(date('Y'), date('Y'))),
                  'label' => __('Tahun'),
                  'value' => request('tahun'),
                  'selected' => request('tahun'),
              ])
            </div>
          </div>
          <button class="btn btn-primary icon"><i class="fa fa-search"></i> Cari Data</button>
        </form>
      </div>
    </div>
    <div class="card">
      <div class="card-body">
        <h5>Daftar Pengajuan CDP (Community Development Program)</h5>
        <table class="table table-striped table-hovered">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Ketua</th>
              <th>Program Studi</th>
              <th>Judul Proposal</th>
              <th>Proposal CDP</th>
              <th>Laporan Kegiatan</th>
              <th>Laporan Keuangan</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($dataProposalKeuangan as $proposal)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $proposal->ketuaKelompok->user->name }}</td>
                <td>{{ implode('; ', json_decode($proposal->ketuaKelompok->user->prodi, true)) }}</td>
                <td>{{ $proposal->judul_proposal }}</td>
                <td><a href="{{ $proposal->file_proposal }}" class="btn btn-outline-info" target="_blank"><i class="fa fa-file-pdf"></i> Unduh</a></td>
                @if ($proposal->laporan_kegiatan)
                  <td><a href="{{ $proposal->laporan_kegiatan }}" class="btn btn-outline-info" target="_blank"><i class="fa fa-file-pdf"></i> Unduh</a></td>
                @else
                  <td><span class="badge badge-warning">Menunggu</span></td>
                @endif
                @if ($proposal->laporan_keuangan)
                  <td><a href="{{ $proposal->laporan_keuangan }}" class="btn btn-outline-info" target="_blank"><i class="fa fa-file-pdf"></i> Unduh</a></td>
                @else
                  <td><span class="badge badge-warning">Menunggu</span></td>
                @endif
                @if ($proposal->status == '0')
                  <td><span class="badge badge-warning">Menunggu Verifikasi</span></td>
                @elseif($proposal->status == '1')
                  <td><span class="badge badge-success">Disetujui</span></td>
                @elseif($proposal->status == '10')
                  <td><span class="badge badge-danger">Ditolak</span></td>
                @endif
              </tr>
            @endforeach
          </tbody>
        </table>
  @endif
@endsection

@push('css')
  <style>
    .stats-card {
      transition: transform 0.2s;
    }

    .stats-card:hover {
      transform: translateY(-5px);
    }
  </style>
@endpush

@push('js')
  <script>
    function openTo(link) {
      window.location.href = link;
    }
  </script>
@endpush
