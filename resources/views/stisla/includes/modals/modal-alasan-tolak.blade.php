<form action="{{ $action }}" enctype="multipart/form-data" method="POST">
  @method('PUT')

  <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('Keterangan') }} {{ $title }}!</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          @if ($note ?? false)
            <div class="mb-4">
              <div class="alert alert-info alert-has-icon">
                <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
                <div class="alert-body">
                  <div class="alert-title">{{ __('Catatan!') }}</div>
                  {{ $note }}
                </div>
              </div>
            </div>
          @endif

          @include('stisla.includes.forms.editors.textarea', [
              'type' => 'textarea',
              // 'accept' => '*',
              'id' => 'keterangan',
              'name' => 'keterangan',
              'label' => __('Keterangan'),
              'required' => true,
              // 'accept' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
              // 'hint' => __('Hanya menerima berkas excel'),
          ])

          {{-- <div class="text-center">
              <a href="{{ $downloadLink }}" class="text-primary">
                <strong>Unduh contoh template impor</strong>
              </a>
            </div> --}}
        </div>
        <div class="modal-footer">
          @csrf

          <div>

            <button type="button" class="btn btn-secondary btn-save-form btn-icon icon-left btn-block" data-dismiss="modal">Tutup</button>
          </div>
          @include('stisla.includes.forms.buttons.btn-tolak')
        </div>
      </div>
    </div>
  </div>
</form>
