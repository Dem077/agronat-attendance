<div wire:ignore.self class="modal fade" id="syncModal" tabindex="-1" role="dialog" aria-labelledby="syncModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="syncModalLabel">Add Employee</h5>
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
                    <label for="sync-user_id">Employee:</label>
                    <select type="text" class="form-control" placeholder="Employee" wire:bind="user_id" id="sync-user_id" required>
                        <option value="">Select Employee</option>
                        @foreach($users as $user)
                            <option value="{{$user['id']}}" {{$user_id==$user['id']?'SELECTED':''}}>{{$user['name']}}</option>
                        @endforeach
                    </select>
                    @error('user_id') <span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="sync-from">From</label>
                        <input type="text" class="form-control mt-1" id="sync-from" placeholder="YYYY-MM-DD" wire:model="from" autocomplete="off" required>
                        @error('from') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="sync-to">To</label>
                        <input type="text" class="form-control mt-1" id="sync-to" placeholder="YYYY-MM-DD" wire:model="to" autocomplete="off" required>
                        @error('to') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    </div>
                    <button type="button" wire:click="sync"  id="sync-button" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>

                        Start Sync</button>
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
      $('#sync-button').on('click',()=>{
        $('#sync-button').attr('disabled','disabled');
        $('#sync-button span').show();
      });
      window.livewire.on('.Synced', () => {
        $('#sync-button').removeAttr('disabled');
        $('#sync-button span').hide();
    });
    $('#sync-from').datepicker(options);
    $('#sync-to').datepicker(options);
    $('#sync-user_id').on('change', function (e) {
       @this.set('user_id', e.target.value);
    });

    $('#sync-from').on('change', function (e) {
       @this.set('from', e.target.value);
    });

    $('#sync-to').on('change', function (e) {
       @this.set('to', e.target.value);
    });

</script>
@endpush
