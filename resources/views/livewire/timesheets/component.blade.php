
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h2>Attendance Logs</h2>
            </div>

            <div class="card-body">
                <div>
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                        Add
                    </button>
                    <div class="row">
                        <div class="col">
                            <form>
                                <div class="form-row align-items-center">
                                  <div class="col-sm-3 my-1">
                                    <label class="sr-only" for="inlineFormInputName">Employee</label>
                                    <select type="text" class="form-control" id="inlineFormInputName" placeholder="Employee" wire:bind="user_id" >
                                        <option>Select Employee</option>
                                        @foreach($employees as $employee)
                                            <option value="{{$employee->id}}" {{$user_id==$employee->id?'SELECTED':''}}>{{$employee->name}}</option>
                                        @endforeach
                                     </select>
                                  </div>
                                  <div class="col-sm-3 my-1">
                                    <label class="sr-only" for="inlineFormInputGroupStartDate">Start Date</label>
                                    <div class="input-group">
                                      <div class="input-group-prepend">
                                        <div class="input-group-text">Start</div>
                                      </div>
                                        <input type="text" class="form-control" id="inlineFormInputGroupStartDate" placeholder="YYYY-MM-DD" wire:model="start_date">
                                    </div>
                                  </div>
                                  <div class="col-sm-3 my-1">
                                    <label class="sr-only" for="inlineFormInputGroupEndDate">End Date</label>
                                    <div class="input-group">
                                      <div class="input-group-prepend">
                                        <div class="input-group-text">End</div>
                                      </div>
                                      <input type="text" class="form-control" id="inlineFormInputGroupEndDate" placeholder="YYYY-MM-DD" wire:model="end_date">
                                    </div>
                                  </div>
                                  <div class="col-auto my-1">
                                    <button type="button" class="btn btn-success" wire:click.prevent="exportRecord()"><i class="fas fa-file-download"></i></button>
                                  </div>
                                </div>
                              </form>
                        </div>
                    </div>

                    @include('livewire.timesheets.create')
                  <div class="table-responsive">
                    <table class="table table-bordered mt-5">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>User</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th width="150px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->user->name }}</td>
                                <td>{{ $log->punch }}</td>
                                <td>{{ $log->IN_OUT }}</td>
                                <td>
                                    <button wire:click="delete({{ $log->id }})" class="btn btn-danger btn-sm">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$logs->links()}}
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

    var options={
        format: 'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true,
      };
    $('#inlineFormInputGroupStartDate').datepicker(options);
    $('#inlineFormInputGroupEndDate').datepicker(options);
    $('#inlineFormInputName').select2();

    $('#inlineFormInputGroupStartDate').on('change', function (e) {
       @this.set('start_date', e.target.value);
    });
    $('#inlineFormInputGroupEndDate').on('change', function (e) {
       @this.set('end_date', e.target.value);
    });
    $('#inlineFormInputName').on('change', function (e) {
       @this.set('user_id', e.target.value);
    });
</script>
@endpush
