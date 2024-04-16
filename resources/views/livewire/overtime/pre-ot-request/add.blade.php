<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card">
            <div class="card-header">
                <h2>OT Request</h2>
            </div>

            <div class="card-body">
                <form>
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (count($this->users)>1)
                <div class="row">
                    <div class="form-group col-md-6">
                        @livewire('partials.user-select')
                        @error('user_id') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>
                @endif

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="apply-date">Date</label>
                            <input type="text" class="form-control mt-1" id="apply-date" placeholder="YYYY-MM-DD" wire:model.defer="ot.ot_date" autocomplete="off">
                            @error('ot.ot_date') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="apply-in">Start Time</label>
                            <input type="time" class="form-control mt-1" id="apply-in" wire:model.defer="ot.start_time" autocomplete="off" >
                            @error('ot.start_time') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="apply-out">End Time</label>
                            <input type="time" class="form-control mt-1" id="apply-out" wire:model.defer="ot.end_time" autocomplete="off" >
                            @error('ot.end_time') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="apply-reason">Purpose</label>
                            <textarea type="text"  rows="2" class="form-control" id="apply-reason" wire:model.defer="ot.purpose" autocomplete="off" required {{$readonly?'readonly':''}}></textarea>
                            @error('ot.purpose') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    {{--

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
                    <a class="btn btn-primary" href="{{route('overtime.pre-ot-request')}}">
                        Back
                    </a>
                    <button type="button" wire:click="store" id="apply-button" class="btn btn-success">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                        Save
                    </button>

                    </form>
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
      Livewire.on('userSelected', id => {
        @this.set('user_id', id);
    });
    $('#apply-date').datepicker(options);
    $('#apply-date').on('change', function (e) {
       @this.set('ot.ot_date', e.target.value);
    });
</script>
@endpush
