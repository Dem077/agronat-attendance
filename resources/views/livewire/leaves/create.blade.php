<div wire:ignore.self class="modal fade" id="leave-create-modal" tabindex="-1" role="dialog"
    aria-labelledby="leave-create-modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leave-create-modalLabel">Leave Manage</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true close-btn">×</span>
                </button>
            </div>
            <div class="modal-body">
                @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
                @endif
                <form>
                    <div class="form-group">
                        @livewire('partials.user-select')
                        @error('user_id') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="leave-create-from">From</label>
                            <input type="text" class="form-control mt-1" id="leave-create-from" placeholder="YYYY-MM-DD"
                                wire:model="form.from" autocomplete="off" required>
                            @error('form.from') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="leave-create-to">To</label>
                            <input type="text" class="form-control mt-1" id="leave-create-to" placeholder="YYYY-MM-DD"
                                wire:model="form.to" autocomplete="off" required>
                            @error('form.to') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="leave_type">Leave Type</label>
                        <select wire:model="form.leave_type_id" id="leave_type" class="form-control">
                            <option value="">Select Leave type</option>
                            @foreach ($leave_types as $type)
                                <option value="{{$type->id}}">{{$type->title}}</option>
                            @endforeach
                        </select>
                        @error('form.leave_type_id') <span class="text-danger">{{ $message }}</span>@enderror

                    </div>
                    <button type="button" wire:click="store" id="leave-create-button" class="btn btn-primary">
                        Save</button>
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
    var options={
        format: 'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true,
      };
      $('#leave-create-button').on('click',()=>{
        $('#leave-create-button').attr('disabled','disabled');
        $('#leave-create-button span').show();
      });
      window.livewire.on('leaveCreated', () => {
        $('#leave-create-button').removeAttr('disabled');
        $('#leave-create-button span').hide();
    });
    $('#leave-create-from').datepicker(options);
    $('#leave-create-to').datepicker(options);
    $('#leave-create-user_id').on('change', function (e) {
       @this.set('user_id', e.target.value);
    });

    $('#leave-create-from').on('change', function (e) {
       @this.set('form.from', e.target.value);
    });

    $('#leave-create-to').on('change', function (e) {
       @this.set('form.to', e.target.value);
    });

</script>
@endpush
