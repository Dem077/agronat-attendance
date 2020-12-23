
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h2>Employees</h2>
            </div>

            <div class="card-body">
                <div>
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                        Add
                    </button>
                    @include('livewire.users.update')
                    @include('livewire.users.create')
                    <div class="row">
                        <div class="col-sm-3 my-1">
                            <label class="sr-only" for="inlineFormInputName">Employee</label>
                            <select type="text" class="form-control" id="inlineFormInputName" placeholder="Employee" wire:bind="user_id" >
                                <option value=''>Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{$employee->id}}" {{$user_id==$employee->id?'SELECTED':''}}>{{$employee->name}}</option>
                                @endforeach
                             </select>
                          </div>
                    </div>
                  <div class="table-responsive">
                    <table class="table table-bordered mt-5">
                        <thead>
                            <tr>
                                <th>Emp No.</th>
                                <th>Fullname</th>
                                <th>Email</th>
                                <th>Designation</th>
                                <th>Mobile</th>
                                <th width="150px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->external_id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->designation }}</td>
                                <td>{{ $user->mobile }}</td>
                                <td>
                                    <button data-toggle="modal" data-target="#updateModal" wire:click="edit({{ $user->id }})" class="btn btn-primary btn-sm">Edit</button>
                                    <button wire:click="delete({{ $user->id }})" class="btn btn-danger btn-sm">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$users->links()}}
                </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

@push('js-bottom')
<script type="text/javascript">
    window.livewire.on('.Store', () => {
        $('#exampleModal').modal('hide');
    });
    $('#inlineFormInputName').select2();
    $('#inlineFormInputName').on('change', function (e) {
    @this.set('user_id', e.target.value);
    });
</script>
@endpush
