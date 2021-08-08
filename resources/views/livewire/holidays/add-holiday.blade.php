<div>
    <p><strong>Add Holiday</strong></p>
    @if (session()->has('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif
    <div class="form-row">
        <div class="form-group col-md-2">
            <label for="leave-create-from">From</label>
            <input type="text" class="form-control mt-1" id="from_date" placeholder="YYYY-MM-DD"
                wire:model="from_date" autocomplete="off" required>
            @error('from_date') <span class="text-danger">{{ $message }}</span>@enderror
        </div>
        <div class="form-group col-md-2">
            <label for="leave-create-to">To</label>
            <input type="text" class="form-control mt-1" id="to_date" placeholder="YYYY-MM-DD"
                wire:model="to_date" autocomplete="off" required>
            @error('to_date') <span class="text-danger">{{ $message }}</span>@enderror
        </div>
        <div class="form-group col-md-4">
            <label for="leave_type">Description</label>
            <input type="text" wire:model="description" class="form-control mt-1" required>
        </div>
        <div class="col-auto mt-4">
            <button type="button" wire:click="add" class="btn btn-primary text-right">
                Save</button>
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
    $('#to_date').datepicker(options);
    $('#from_date').datepicker(options);

    $('#to_date').on('change', function (e) {
       @this.set('to_date', e.target.value);
    });
    $('#from_date').on('change', function (e) {
       @this.set('from_date', e.target.value);
    });
</script>
@endpush

