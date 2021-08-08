
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Leave Manage</h2>
            </div>

            <div class="card-body">
                <div>

                    @include('livewire.leaves.create')
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#leave-create-modal">
                        Add
                    </button>
                    <div class="row">
                        <div class="col">
                            <form>
                                <div class="form-row align-items-center">
                                  <div class="col-sm-3 my-1">
                                      @livewire('partials.user-select',['ref'=>'leave-user-select'])
                                  </div>
                                  {{-- <div class="col-sm-3 my-1">
                                      @livewire('partials.attendance-period')
                                  </div> --}}
                                </div>
                              </form>
                        </div>
                    </div>

                  <div class="table-responsive">
                    <table class="table table-bordered mt-5">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Leave Type</th>
                                <th>Start</th>
                                <th>End</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaves as $leave)
                            <tr>
                                <td>{{$leave->user->name}}</td>
                                <td>{{$leave->type->title}}</td>
                                <td>{{$leave->from}}</td>
                                <td>{{$leave->to}}</td>
                                <td width="20px"><span class="btn text-danger" onclick="deleteLeave({{$leave->id}})">&times;</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$leaves->links()}}
                </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

@push('js-bottom')
<script type="text/javascript">

    Livewire.on('userSelected', id => {
        @this.set('user_id', id);
    });
    Livewire.on('periodSelected', period => {
      @this.set('start_date', period['start']);
      @this.set('end_date', period['end']);
    });
    function deleteLeave(id){
      if (confirm('Are you sure?')) {
          Livewire.emit('deleteLeave',id);
      }
    }



</script>
@endpush
