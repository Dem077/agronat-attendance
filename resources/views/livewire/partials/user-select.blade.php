<div>
    <label for="user-select-name">Employee:</label>
    <select type="text" class="form-control" id="user-select-name" placeholder="Employee">
        <option value=''>Select Employee</option>
    </select>
</div>

@push('js-bottom')
    <script>
        var users=@JSON($users);
        $.each(users, function(i,v) {
            $('#user-select-name').append(new Option(v,i));
        });
        $('#user-select-name').on('change', function (e) {
            Livewire.emit('userSelected',e.target.value)
        });
    </script>
@endpush
