<div class="row justify-content-center">
    <div class="col-md-12">
        <h3>OT Report</h3>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="form-row align-items-center">
                    <div class="col-sm-3 my-1">
                        @livewire('partials.user-select')
                    </div>
                    <div class="col-sm-3 my-1">
                        @livewire('partials.attendance-period')
                    </div>
                    <div class="col-auto my-1">
                      <button type="button" class="btn btn-success" wire:click.prevent="exportRecord()"><i class="fas fa-file-download"></i></button>
                    </div>
                  </div>
                  <div class="row">
                      <div class="col-md-12">
                      </div>
                  </div>
            </div>
        </div>
    </div>
</div>

@push('js-bottom')
<script type="text/javascript">

    Livewire.on('userSelected', id => {
        @this.set('user_id', id);
    });
    Livewire.on('periodSelected', period => {
      @this.set('start_date', period['start']);
      @this.set('end_date', period['end']);
    });

</script>
@endpush