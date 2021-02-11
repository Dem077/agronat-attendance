
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
                    @can('timelog-create')
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                        Add
                    </button>
                    @endcan
                    <div class="row">
                      <div class="col">
                          <form>
                              <div class="form-row align-items-center">
                                <div class="col-sm-3 my-1">
                                    @livewire('partials.user-select')
                                </div>
                                <div class="col-sm-3 my-1">
                                    @livewire('partials.attendance-period')
                                </div>
                                <div class="col-sm-3 my-1">
                                  <label for="inlineFormInputGroupStartDate">Start Date</label>
                                  <div class="input-group">
                                    <div class="input-group-prepend">
                                      <div class="input-group-text">Start</div>
                                    </div>
                                    <input type="text" class="form-control" autocomplete="off" id="inlineFormInputGroupStartDate" placeholder="YYYY-MM-DD" wire:model="start_date">
                                  </div>
                                </div>
                                <div class="col-sm-3 my-1">
                                  <label for="inlineFormInputGroupEndDate">End Date</label>
                                  <div class="input-group">
                                    <div class="input-group-prepend">
                                      <div class="input-group-text">End</div>
                                    </div>
                                    <input type="text" class="form-control" autocomplete="off" id="inlineFormInputGroupEndDate" placeholder="YYYY-MM-DD" wire:model="end_date">
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
                                @can('timelog-delete')
                                <th width="150px">Action</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->user->name }}</td>
                                <td>{{ $log->punch }}</td>
                                <td>{{ $log->IN_OUT }}</td>
                                @can('timelog-delete')
                                <td>
                                    <button wire:click="delete({{ $log->id }})" class="btn btn-danger btn-sm">Delete</button>
                                </td>
                                @endcan
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

    $('#inlineFormInputGroupStartDate').on('change', function (e) {
       @this.set('start_date', e.target.value);
    });
    $('#inlineFormInputGroupEndDate').on('change', function (e) {
       @this.set('end_date', e.target.value);
    });
    Livewire.on('userSelected', id => {
        @this.set('user_id', id);
    });
    Livewire.on('periodSelected', period => {
      @this.set('start_date', period['start']);
      @this.set('end_date', period['end']);
    });
</script>
@endpush
