
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Leave Manage</h2>
            </div>

            <div class="card-body">
                <div>
                    @livewire('partials.leave-types.create')

                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#leaveType-create-modal">
                         Create
                    </button>
                  <div class="table-responsive">
                    <table class="table table-bordered mt-5">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Title</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leave_types as $type)
                            <tr>
                                <td>{{$type->id}}</td>
                                <td>{{$type->title}}</td>
                                <td width="20px"><span class="btn text-danger" onclick="deleteLeave({{$type->id}})">&times;</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$leave_types->links()}}
                </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

@push('js-bottom')
<script type="text/javascript">


    function deleteLeave(id){
      if (confirm('Are you sure?')) {
          Livewire.emit('deleteLeaveType',id);
      }
    }

</script>
@endpush
