<div wire:ignore.self class="modal fade" id="recomputeModal" tabindex="-1" role="dialog" aria-labelledby="syncModalLabel" aria-hidden="true">
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
                    <label for="recompute-user_id">Employee:</label>
                    <select type="text" class="form-control" placeholder="Employee" wire:bind="user_id" id="recompute-user_id" required>
                        <option value="">Select Employee</option>
                        @foreach($users as $user)
                            <option value="{{$user['id']}}" {{$user_id==$user['id']?'SELECTED':''}}>{{$user['name']}}</option>
                        @endforeach
                    </select>
                    @error('user_id') <span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="recompute-from">From</label>
                        <input type="text" class="form-control mt-1" id="recompute-from" placeholder="YYYY-MM-DD" wire:model="from" autocomplete="off" required>
                        @error('from') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="recompute-to">To</label>
                        <input type="text" class="form-control mt-1" id="recompute-to" placeholder="YYYY-MM-DD" wire:model="to" autocomplete="off" required>
                        @error('to') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    </div>
                    <hr/>
                    <strong>Schedule</strong>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="recompute-in">In</label>
                            <input type="time" class="form-control mt-1" id="recompute-in"wire:model="in" autocomplete="off" required>
                            @error('in') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="recompute-out">Out</label>
                            <input type="time" class="form-control mt-1" id="recompute-out" wire:model="out" autocomplete="off" required>
                            @error('out') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        </div>
                    <button type="button" wire:click="recompute"  id="recompute-button" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>

                        Recompute</button>
                </form>
                
                @if ($progressKey)
                    <div wire:poll.2000ms="getProgress" class="mt-3">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: {{ $progress['percent'] }}%;" aria-valuenow="{{ $progress['percent'] }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $progress['percent'] }}%
                            </div>
                        </div>
                        <small>{{ $progress['completed'] }} / {{ $progress['total'] }} completed</small>
                        <div class="mt-2">
                            <button wire:click="stopAndClearQueue" class="btn btn-danger btn-sm">Stop & Clear Queue</button>
                        </div>
                    </div>
                @endif
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
      $('#recompute-button').on('click',()=>{
        $('#recompute-button').attr('disabled','disabled');
        $('#recompute-button span').show();
      });
      window.livewire.on('.Recomputed', () => {
        $('#recompute-button').removeAttr('disabled');
        $('#recompute-button span').hide();
    });
    $('#recompute-from').datepicker(options);
    $('#recompute-to').datepicker(options);
    $('#recompute-user_id').on('change', function (e) {
       @this.set('user_id', e.target.value);
    });
    $('#recompute-user_id').select2();

    $('#recompute-from').on('change', function (e) {
       @this.set('from', e.target.value);
    });

    $('#recompute-to').on('change', function (e) {
       @this.set('to', e.target.value);
    });
    $('#recomputeModal').on('hidden.bs.modal', function () {
        window.livewire.emit('stopAndClearQueue');
    });
</script>
@endpush
