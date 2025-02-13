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
