
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Employees</h2>
            </div>

            <div class="card-body" x-data="{department:'{{$department}}'}">
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
                                <label for="user-select-name">Department:</label>
                                <select type="text" class="form-control" id="department-select-name" placeholder="Department" x-model="department" x-on:change="window.location.href='?department='+department">
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $id=>$department)
                                        <option value='{{$department->id}}'>{{$department->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3 my-1">
                            <div>
                                <label for="user-select-name">Employee:</label>
                                <select type="text" class="form-control" id="user-select-name" placeholder="Employee">
                                    <option value="">Select Employee</option>
                                    @foreach ($employees as $id=>$name)
                                        <option value='{{$id}}' {{strval($employee)==strval($id)?'SELECTED="SELECTED"':''}}>{{$name}}</option>
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
                                <th>National ID</th>
                                <th>Email</th>
                                <th>Designation</th>
                                <th>Mobile</th>
                                <th>Emp No.</th>
                                <th>Joined Date</th>
                                @can('user-create')
                                <th width="200px">Action</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user_list as $user)
                            <tr class="{{$user->active?'':'text-danger'}}">
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->nid }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->designation }}</td>
                                <td>{{ $user->mobile }}</td>
                                <td>{{ $user->emp_no }}</td>
                                <td>{{ $user->joined_date }}</td>
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
                    {{$user_list->links()}}
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
        window.location.href="?employee="+e.target.value;
    });
</script>
@endpush
