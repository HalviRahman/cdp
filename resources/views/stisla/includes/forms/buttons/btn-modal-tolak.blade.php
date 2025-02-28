@php
  $icon = $icon ?? 'fas fa-times';
@endphp

<a onclick="showImportModal(event)" class="btn btn-danger btn-save-form btn-icon icon-left" href="{{ $link ?? '' }}">
  @if ($icon ?? false)
    <i class="{{ $icon }}"></i>
  @endif
  {{ $label ?? __('Tolak') }}
</a>

{{-- 
<button type="submit" class="btn btn-danger btn-save-form btn-icon icon-left btn-block" name="action" value="reject">
  <i class="fas fa-times"></i>
  {{ $label ?? __('Tolak') }}
</button> --}}
