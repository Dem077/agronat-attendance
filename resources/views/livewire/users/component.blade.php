
<div class="row justify-content-center">
    <div class="col-md-12">
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
                    @can('user-create')
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                        Add
                    </button>
                    @include('livewire.users.view')
                    @include('livewire.users.update')
                    @include('livewire.users.create')
                    @endcan
                    <div class="row">
                        <div class="col-sm-3 my-1">
                            <div>
                                <label for="user-select-name">Employee:</label>
                                <select type="text" class="form-control" id="user-select-name" wire:model="user_id" placeholder="Employee">
                                    <option value=''>Select Employee</option>
                                    @foreach ($employees as $id=>$name)
                                        <option value='{{$id}}'>{{$name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                  <div class="table-responsive">
                    <table class="table table-bordered mt-5">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fullname</th>
                                <th>Email</th>
                                <th>Designation</th>
                                <th>Mobile</th>
                                <th>Emp No.</th>
                                @can('user-create')
                                <th width="200px">Action</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->users as $user)
                            <tr class="{{$user->active?'':'text-danger'}}">
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->designation }}</td>
                                <td>{{ $user->mobile }}</td>
                                <td>{{ $user->emp_no }}</td>
                                @can('user-create')
                                <td>
                                    <button data-toggle="modal" data-target="#viewModal" wire:click="show({{ $user->id }})" class="btn btn-primary btn-sm">View</button>
                                    <button data-toggle="modal" data-target="#updateModal" wire:click="edit({{ $user->id }})" class="btn btn-primary btn-sm">Edit</button>
                                    <button wire:click="delete({{ $user->id }})" class="btn btn-danger btn-sm">Delete</button>
                                </td>
                                @endcan
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$this->users->links()}}
                </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

@push('js-bottom')
<script type="text/javascript">
$('#user-select-name').select2();
    window.livewire.on('.Store', () => {
        $('#createModal').modal('hide');
    });

    window.livewire.on('.userUpdated', () => {
        $('#updateModal').modal('hide');
    });

    $('#user-select-name').on('change',function(e){
        @this.set('user_id', e.target.value);
    });
</script>
@endpush
