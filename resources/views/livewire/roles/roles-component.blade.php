
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h2>Manage Roles</h2>
            </div>

            <div class="card-body">
                <div>
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                        Create
                    </button>
                    {{-- @include('livewire.users.update')
                    @include('livewire.users.create') --}}
                    @include('livewire.roles.create')

                  <div class="table-responsive">
                    <table class="table table-bordered mt-5">
                        <thead>
                            <tr>
                                <th class="w-20">No</th>
                                <th class="w-50">Name</th>
                                <th class="w-30">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $i=>$role)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $role->name }}</td>
                                <td>
                                    <a class="btn btn-sm btn-info mt-1" href="{{-- route('roles.show',$role->id) --}}">Show</a>
                                    @can('role-edit')
                                        <a class="btn btn-sm btn-primary mt-1" href="{{-- route('roles.edit',$role->id) --}}">Edit</a>
                                    @endcan
                                    @can('role-delete')
                                        <button wire:click="delete({{ $role->id }})" class="btn btn-danger btn-sm mt-1">Delete</button>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$roles->links()}}
                </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

@push('js-bottom')
<script type="text/javascript">
    window.livewire.on('.Store', () => {
        $('#createModal').modal('hide');
    });
</script>
@endpush