<button type="submit" class="btn btn-{{ $color ?? 'primary' }} btn-save-form btn-icon icon-left">
  <i class="{{ $icon ?? 'fas fa-check' }}"></i>
  {{ $label ?? __('Simpan') }}
</button>
