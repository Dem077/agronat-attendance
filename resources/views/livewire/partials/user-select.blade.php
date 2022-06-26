
<div class="form-group">
    <label for="{{$ref}}">Employee:</label>
    <select type="text" class="form-control" id="{{$ref}}" placeholder="Employee">
        <option value=''>Select Employee</option>
        @foreach($users as $user)
            <option value='{{$user['id']}}' {{$user_id==$user['id']?'SELECTED':''}}>{{$user['name']}} ({{$user['emp_no']}})</option>
        @endforeach
    </select>
</div>

@push('js-bottom')

    <script>
        var ref= "#"+@json($ref);
        $(ref).select2();
        $(ref).on('change', function (e) {
            Livewire.emit('userSelected',e.target.value)
        });
    </script>
@endpush
