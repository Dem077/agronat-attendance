
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Assign Roles</h2>
            </div>

            <div class="card-body">
                <div>
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

                    @include('livewire.assign-role.update')

                  <div class="table-responsive">
                    <table class="table table-bordered mt-5">
                        <thead>
                            <tr>
                                <th class="w-5">No</th>
                                <th class="w-50">Employee Name</th>
                                <th class="w-40">Roles</th>
                                <th class="w-5">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $i=>$user)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $user->name }}</td>
                                <td>
                                    @foreach ($user->roles as $role)
                                        <span class="badge badge-success">{{$role->name}}</span>
                                    @endforeach
                                </td>
                                <td>
                                    {{-- <a class="btn btn-sm btn-info mt-1" href="{{route('roles.show',$role->id)}}">Show</a> --}}
                                    @can('role-edit')
                                        <button data-toggle="modal" data-target="#updateModal" wire:click="edit({{ $user->id }})" class="btn btn-primary btn-sm mt-1">Edit</button>
                                    @endcan
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
