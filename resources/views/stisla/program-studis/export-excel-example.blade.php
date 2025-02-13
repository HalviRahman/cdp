<table>
  <thead>
    <tr>
      <th>{{ __('#') }}</th>
      <th class="text-center">{{ __('Program Studi') }}</th>
      <th class="text-center">{{ __('Kuota') }}</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($data as $item)
      <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->nama_prodi }}</td>
        <td>{{ $item->kuota }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
