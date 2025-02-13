<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>{{ __('Laporan') }}</title>

  <link rel="stylesheet" href="{{ asset('assets/css/export-pdf.min.css') }}">
</head>

<body>
  <h1>{{ __('Laporan') }}</h1>
  <h3>{{ __('Total Data:') }} {{ $data->count() }}</h3>
  <table>
    <thead>
      <tr>
        <th>{{ __('#') }}</th>
        <th class="text-center">{{ __('ID Kelompok') }}</th>
        <th class="text-center">{{ __('Judul Proposal') }}</th>
        <th class="text-center">{{ __('File Proposal') }}</th>
        <th class="text-center">{{ __('Tanggal Upload') }}</th>
        <th class="text-center">{{ __('Status') }}</th>
        <th class="text-center">{{ __('Verifikator') }}</th>
        <th class="text-center">{{ __('Keterangan') }}</th>
        <th class="text-center">{{ __('Tanggal Verifikasi') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($data as $item)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $item->id_kelompok }}</td>
          <td>{{ $item->judul_proposal }}</td>
          <td>{{ $item->file_proposal }}</td>
          <td>{{ $item->tgl_upload }}</td>
          <td>{{ $item->status }}</td>
          <td>{{ $item->verifikator }}</td>
          <td>{{ $item->keterangan }}</td>
          <td>{{ $item->tgl_verifikasi }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  @if (($isPrint ?? false) === true)
    <script>
      window.print();
    </script>
  @endif

</body>

</html>
