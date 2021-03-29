<div wire:ignore.self class="modal fade" id="applyModal" tabindex="-1" role="dialog" aria-labelledby="applyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="applyModalLabel">Apply Modal</h5>
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
                    <div class="form-group col-md-6">
                        <label for="apply-user">Employee</label>
                        <select class="form-control" id="apply-user" wire:model.defer="ot.user_id">
                            <option value="">Select Employee</option>
                            @foreach ($users as $user)
                                <option value="{{$user['id']}}" {{$user['id']==$ot['user_id']?'SELECTED':''}}>{{$user['name']}}</option>
                            @endforeach
                        </select>
                        @error('ot.user_id') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="apply-date">Date</label>
                        <input type="text" class="form-control mt-1" id="apply-date" placeholder="YYYY-MM-DD" wire:model.defer="ot.ck_date" autocomplete="off">
                        @error('ot.ck_date') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="apply-in">Start Time</label>
                        <input type="time" class="form-control mt-1" id="apply-in" wire:model.defer="ot.in" autocomplete="off" >
                        @error('ot.in') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="apply-out">End Time</label>
                        <input type="time" class="form-control mt-1" id="apply-out" wire:model.defer="ot.out" autocomplete="off" >
                        @error('ot.out') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>
                {{-- 
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="apply-reason">Reason</label>
                        <textarea type="text"  rows="2" class="form-control" id="apply-reason" wire:model.defer="ot.reason" autocomplete="off" required {{$readonly?'readonly':''}}></textarea>
                        @error('ot.reason') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="apply-status">status</label>
                        <select class="form-control" id="apply-status" wire:model="status" required {{$readonly?'readonly':''}}>
                            <option value="Pending" {{$status=='Pending'?'SELECTED':''}}>Pending</option>
                            <option value="Approved" {{$status=='Approved'?'SELECTED':''}}>Approve</option>
                            <option value="Rejected" {{$status=='Rejected'?'SELECTED':''}}>Reject</option>
                        </select>
                        @error('status') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div> --}}
                @if(!$readonly)
                    <button type="button" wire:click="store" id="apply-button" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                        Save
                    </button>
                    <button type="button" wire:click="update" id="apply-button" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                        Update
                    </button>
                @endif
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
  </div>

@push('js-bottom')
<script>
    $('#apply-user').select2();
    var options={
        format: 'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true,
      };
    $('#apply-date').datepicker(options);
    $('#apply-date').on('change', function (e) {
       @this.set('ot.ck_date', e.target.value);
    });
</script>
@endpush
