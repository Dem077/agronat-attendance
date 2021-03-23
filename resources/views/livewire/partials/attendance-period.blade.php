<div class="form-group">
    <label for="user-select-name">Month:</label>
    <select type="text" class="form-control" id="attendance-period-month" placeholder="Month">
        <option value=''>Select Month</option>
    </select>
</div>

@push('js-bottom')
    <script>
        var periods=@JSON($periods);
        var period_id={{$period_id??''}};
        
        $.each(periods, function(i,v) {
            var op=new Option(v['month'],i);
            $('#attendance-period-month').append(op);
        });
        $('#attendance-period-month').on('change', function (e) {
            Livewire.emit('periodSelected',periods[e.target.value])
        });

        $(function(){
            $('#attendance-period-month').val(period_id).change();
        });
    </script>
@endpush
