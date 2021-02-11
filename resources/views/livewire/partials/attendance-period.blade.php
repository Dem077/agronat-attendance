<div>
    <label for="user-select-name">Month:</label>
    <select type="text" class="form-control" id="attendance-period-month" placeholder="Month">
        <option value=''>Select Month</option>
    </select>
</div>

@push('js-bottom')
    <script>
        var periods=@JSON($periods);
        $.each(periods, function(i,v) {
            $('#attendance-period-month').append(new Option(v['month'],i));
        });
        $('#attendance-period-month').on('change', function (e) {
            Livewire.emit('periodSelected',periods[e.target.value])
        });
    </script>
@endpush
