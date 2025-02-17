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
    {{-- @if (auth()->user()->hasRole('Dosen')) --}}
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
    {{-- @endif --}}

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
    {{-- Box 3 --}}
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


  </div>
@endsection

@push('css')
  <style>
    .status-card {
      border-left: 4px solid;
    }

    .status-pending {
      border-left-color: #ffc107;
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
