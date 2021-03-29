
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Overtime</h2>
            </div>

            <div class="card-body">
                <div>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#applyModal">
                        Add
                    </button>
                    <div class="row">
                      <div class="col">
                          <form>
                              <div class="row">
                                <div class="col-md-4">
                                    @livewire('partials.user-select')
                                </div>
                                <div class="col-md-4">
                                    @livewire('partials.attendance-period')
                                </div>
                              </div>
                            </form>
                      </div>
                  </div>
                    
                    {{-- @include('livewire.users.update')
                    @include('livewire.users.create') --}}
                  <div class="table-responsive">
                    <table class="table table-bordered mt-5">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Employee</th>
                                <th>Checkin</th>
                                <th>Checkout</th>
                                <th>OT Minutes</th>
                                <th>action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ots as $ot)
                            <tr>
                                <td>{{ $ot->id }}</td>
                                <td>{{ $ot->ck_date }}</td>
                                <td>{{ $ot->day }}</td>
                                <td>{{ $ot->user->name }}</td>
                                <td>{{ $ot->in }}</td>
                                <td>{{ $ot->out }}</td>
                                <td>{{ $ot->ot }}</td>                                
                                <td>
                                  @can('overtime-create')
                                  {{-- <button class="btn btn-primary btn-sm m-1" wire:click="edit({{$ot->id}})" data-toggle="modal" data-target="#applyModal">Edit</button> --}}
                                  <button class="btn btn-danger btn-sm m-1" onclick="deleteLog({{$ot->id}})">Delete</button>
                                  @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$ots->links()}}
                </div>
                </div>
                
            </div>
        </div>
    </div>
    @include('livewire.overtime.apply')

</div>

@push('js-bottom')
<script type="text/javascript">
    window.livewire.on('.Store', () => {
        $('#createModal').modal('hide');
    });

    var options={
        format: 'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true,
      };
    Livewire.on('userSelected', id => {
        @this.set('user_id', id);
    });
    Livewire.on('periodSelected', period => {
      @this.set('start_date', period['start']);
      @this.set('end_date', period['end']);
    });

    function deleteLog(id){
      if (confirm('Are you sure?')) {
          Livewire.emit('deleteLog',id);
      }
    }

</script>
@endpush
