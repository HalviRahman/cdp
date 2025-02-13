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
