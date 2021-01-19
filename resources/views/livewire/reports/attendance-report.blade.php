<div class="row justify-content-center">
    <div class="col-md-6">
        <h3>Attendance Report</h3>
        <div class="card shadow mb-4">
            <div class="card-body">
                <form>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="start_date">Start Date</label>
                            <input type="text" class="form-control" autocomplete="off" id="start_date" placeholder="YYYY-MM-DD" wire:model="start_date">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="end_date">End Date</label>
                            <input type="text" class="form-control" autocomplete="off" id="end_date" placeholder="YYYY-MM-DD" wire:model="end_date">
                        </div>
                    </div>
                    <div class="col-auto my-1">
                        <button type="button" class="btn btn-success btn-sm" wire:click.prevent="exportRecord()">Download</button>
                    </div>
                </form>
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
    $('#start_date').datepicker(options);
    $('#end_date').datepicker(options);

    $('#start_date').on('change', function (e) {
       @this.set('start_date', e.target.value);
    });
    $('#end_date').on('change', function (e) {
       @this.set('end_date', e.target.value);
    });

</script>
@endpush