@extends($data->count() > 0 ? 'stisla.layouts.app-table' : 'stisla.layouts.app')

@section('title')
  {{ $title }}
@endsection

@section('content')
  @include('stisla.includes.breadcrumbs.breadcrumb-table')

  {{-- <div class="col-12"> --}}
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

  @if (auth()->user()->hasRole('Fakultas'))
    <div class="row">
      <div class="col-6">
        <div class="stats-card">
          <div class="card card-statistic-1">
            <div class="card-icon bg-info">
              <i class="fas fa-file-text"></i>
            </div>
            <div class="card-wrap">
              <div class="card-header">
                <h4 class="text-dark">Total Proposal Masuk</h4>
              </div>
              <div class="card-body">
                <h4>{{ $proposalMasuk }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6">
        <div class="stats-card">
          <div class="card card-statistic-1">
            <div class="card-icon bg-warning">
              <i class="fas fa-file-text"></i>
            </div>
            <div class="card-wrap">
              <div class="card-header">
                <h4 class="text-dark">Total Proposal Belum Masuk</h4>
              </div>
              <div class="card-body">
                <h4></h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <h4>Rekap Proposal Masuk per Program Studi</h4>
      </div>
    </div>
    <div class="row">
      @foreach ($programStudi as $prodi)
        <a href="{{ route('proposals.index', [
            'prodi' => $prodi->jenjang . ' ' . $prodi->nama_prodi,
            'tahun' => request('tahun', date('Y')),
            'view' => 'table',
        ]) }}"
          class="col-4">
          <div class="stats-card">
            <div class="card card-statistic-1">
              <div class="card-icon bg-info">
                <i class="fas fa-file-text"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4 class="text-dark">{{ $prodi->nama_prodi }}</h4>
                </div>
                <div class="card-body">
                  <h4>{{ $prodi->proposals_count }}</h4>
                </div>
              </div>
            </div>
          </div>
        </a>
      @endforeach
    </div>
  @endif
  {{-- </div> --}}
  <div class="section-body">

    {{-- <h2 class="section-title">{{ $title }}</h2>
    <p class="section-lead">{{ __('Merupakan halaman yang menampilkan kumpulan data ' . $title) }}.</p> --}}

    {{-- <div class="row"> --}}
    {{-- index old --}}

    {{-- end index old --}}

    {{-- </div> --}}

    @if (auth()->user()->hasRole('Prodi'))
      <h2 class="section-title">Daftar Proposal</h2>

      @foreach ($data as $proposal)
        <div class="card">
          <div class="card-body">
            <h5>{{ $proposal->judul_proposal }}</h5>
            <p><strong>Ketua:</strong> {{ $proposal->ketuaKelompok->user->name }} - {{ implode('; ', $proposal->ketuaKelompok->user->prodi) }}
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
    @endif
  </div>
@endsection

@push('css')
@endpush

@push('js')
@endpush

@push('scripts')
  <script></script>
@endpush

@push('modals')
  @if ($canImportExcel)
    @include('stisla.includes.modals.modal-import-excel', ['formAction' => $routeImportExcel, 'downloadLink' => $excelExampleLink])
  @endif
@endpush
