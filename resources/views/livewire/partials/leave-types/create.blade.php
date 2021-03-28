<div wire:ignore.self class="modal fade" id="leaveType-create-modal" tabindex="-1" role="dialog"
    aria-labelledby="leaveType-create-modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leaveType-create-modalLabel">Leave Manage</h5>
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
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="leaveType-create-title">Title</label>
                            <input type="text" class="form-control mt-1" id="leaveType-create-title" 
                                wire:model.defer="leaveType.title" autocomplete="off" required>
                            @error('leaveType.title') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" wire:click="store" id="leaveType-create-button" class="btn btn-primary">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                        style="display: none;"></span>

                    Save</button>
                <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('js-bottom')
<script type="text/javascript">
      $('#leaveType-create-button').on('click',()=>{
        $('#leaveType-create-button').attr('disabled','disabled');
        $('#leaveType-create-button span').show();
      });
      window.livewire.on('leaveTypeCreated', () => {
        $('#leaveType-create-button').removeAttr('disabled');
        $('#leaveType-create-button span').hide();
    });

</script>
@endpush
