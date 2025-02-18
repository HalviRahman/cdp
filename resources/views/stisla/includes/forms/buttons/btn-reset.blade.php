<button type="reset" class="btn btn-{{ $color ?? 'danger' }} btn-reset-form btn-icon icon-left">
  <i class="{{ $icon ?? 'fas fa-undo-alt' }}"></i>
  {{ $label ?? __('Reset') }}
</button>
