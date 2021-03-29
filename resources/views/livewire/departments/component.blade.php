
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Department</h2>
            </div>

            <div class="card-body">
                <div>

                    <div class="row">
                      <div class="col">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#department-create-modal">
                            Add
                        </button>
                      </div>
                  </div>
                    
                  <div class="table-responsive">
                    <table class="table table-bordered mt-5">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Name</th>
                                <th>Supervisor</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->departments as $department)
                            <tr>
                                <td>{{ $department->id }}</td>
                                <td>{{ $department->name }}</td> 
                                <td>{{ $department->supervisor->name??'' }}</td>
                                <td>
                                  @can('overtime-create')
                                  <button class="btn btn-primary btn-sm m-1" wire:click="edit({{$department->id}})" data-toggle="modal" data-target="#department-create-modal">Edit</button>
                                  <button class="btn btn-danger btn-sm m-1" onclick="deleteLog({{$department->id}})">Delete</button>
                                  @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$this->departments->links()}}
                </div>
                </div>
                
            </div>
        </div>
    </div>
    @include('livewire.departments.create')

</div>

@push('js-bottom')
<script type="text/javascript">
    function deleteLog(id){
      if (confirm('Are you sure?')) {
          Livewire.emit('deleteLog',id);
      }
    }

</script>
@endpush
