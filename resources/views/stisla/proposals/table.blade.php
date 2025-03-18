@extends('stisla.layouts.app')

@section('title')
  {{ $title }} - {{ $prodi }}
@endsection

@section('content')
  @include('stisla.includes.breadcrumbs.breadcrumb-table', [
      'title' => __('Daftar Proposal CDP - ' . $prodi),
  ])

  <div class="section-body">
    <div class="card">
      <div class="card-body">
        <div class="mb-3">
          <a href="{{ route('proposals.excel', [
              'prodi' => $prodi,
              'tahun' => request('tahun', date('Y')),
          ]) }}" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Export Excel
          </a>
        </div>
        <div class="table-responsive">
          <table class="table table-striped" id="table-1">
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal Upload</th>
                <th>Judul Proposal</th>
                <th>Ketua</th>
                <th>Program Studi</th>
                <th>File Proposal</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($data as $index => $item)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $item->created_at->format('d M Y') }}</td>
                  <td>{{ $item->judul_proposal }}</td>
                  <td>{{ $item->ketuaKelompok->user->name }}</td>
                  <td>{{ $item->prodi }}</td>
                  <td>
                    <a href="{{ $item->file_proposal }}" class="btn btn-primary">
                      <i class="fas fa-download"></i> Unduh
                    </a>
                  </td>
                  <td>
                    @if ($item->status == '0')
                      <span class="badge badge-warning">Menunggu Verifikasi Koordinator Prodi</span>
                    @elseif($item->status == '1')
                      <span class="badge badge-success">Menunggu Verifikasi Prodi</span>
                    @elseif($item->status == '2')
                      <span class="badge badge-success">Disetujui</span>
                    @elseif($item->status == '3')
                      <span class="badge badge-success">Disetujui</span>
                    @elseif($item->status == '10')
                      <span class="badge badge-danger">Ditolak</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
