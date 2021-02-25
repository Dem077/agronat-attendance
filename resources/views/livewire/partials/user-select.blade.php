<div>
    <label for="user-select-name">Employee:</label>
    <select type="text" class="form-control" id="user-select-name" placeholder="Employee">
        <option value=''>Select Employee</option>
        <?= asort($users); ?>
        @foreach($users as $id=>$name)
            <option value='{{$id}}' {{$user_id==$id?'SELECTED':''}}>{{$name}}</option>
        @endforeach
    </select>
</div>

@push('js-bottom')
    <script>
        $('#user-select-name').select2();
        $('#user-select-name').on('change', function (e) {
            Livewire.emit('userSelected',e.target.value)
        });
    </script>
@endpush
