<table>
  <thead>
    <tr>
      <th>{{ __('#') }}</th>
      <th class="text-center">{{ __('Ketua Email') }}</th>
      <th class="text-center">{{ __('Anggota Email') }}</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($data as $item)
      <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->ketua_email }}</td>
        <td>{{ $item->anggota_email }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
