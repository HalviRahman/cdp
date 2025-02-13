<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>{{ __('Jadwal') }}</title>

  <link rel="stylesheet" href="{{ asset('assets/css/export-pdf.min.css') }}">
</head>

<body>
  <h1>{{ __('Jadwal') }}</h1>
  <h3>{{ __('Total Data:') }} {{ $data->count() }}</h3>
  <table>
    <thead>
      <tr>
        <th>{{ __('#') }}</th>
        <th class="text-center">{{ __('Tanggal Mulai') }}</th>
        <th class="text-center">{{ __('Tanggal Selesai') }}</th>
        <th class="text-center">{{ __('Kegiatan') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($data as $item)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $item->tgl_mulai }}</td>
          <td>{{ $item->tgl_selesai }}</td>
          <td>{{ $item->keterangan }}</td>
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
