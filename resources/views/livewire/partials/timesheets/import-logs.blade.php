

<div wire:ignore.self class="modal fade" id="logImportModal" tabindex="-1" role="dialog" aria-labelledby="logImportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logImportModalLabel">Add Employee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true close-btn">×</span>
                </button>
            </div>
           <div class="modal-body">
            <form>
                @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            <div class="form-group">
                <label for="">Location</label>
                <select class="form-control" wire:model='location'>
                    <option value="">Select Location</option>
                    <option value="fonadhoo">Fonadhoo RC</option>
                </select>
                @error('location') <span class="error">{{ $message }}</span> @enderror
    
            </div>
            <div class="form-group">
                <label for="">Sheet</label>
                <input type="file" wire:model="sheet">
                    
                @error('sheet') <span class="error">{{ $message }}</span> @enderror
            </div>

                    <button type="button" wire:click="logImport"  id="logImport-button" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>

                        Import</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
  </div>

@push('js-bottom')
<script type="text/javascript">
      $('#logImport-button').on('click',()=>{
        $('#logImport-button').attr('disabled','disabled');
        $('#logImport-button span').show();
      });
      window.livewire.on('logImported', () => {
        $('#logImport-button').removeAttr('disabled');
        $('#logImport-button span').hide();
    });


</script>
@endpush
