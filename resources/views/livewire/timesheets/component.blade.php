<div class="row justify-content-center">
  <div class="col-md-12">
      <div class="card">
          <div class="card-header">
              <h2>Attendance Logs</h2>
          </div>

          <div class="card-body">
              <div>
                  @can('timelog-create')
                  @include('livewire.timesheets.create')
                  @include('livewire.timesheets.changes')
                  <input type="hidden" id="changeData" wire:model="changeData">

                  <livewire:partials.timesheets.sync-component :users="$users"/>

                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal">Add</button>
                  <button type="button" class="btn btn-success" data-toggle="modal" data-target="#syncModal">Sync</button>
                  <a href="{{route('timesheet.import-log')}}" class="btn btn-success">Import Log</a>
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
                                <button type="button" class="btn btn-success" wire:loading.class="disabled" wire:loading.attr="disabled" wire:click.prevent="exportRecord">
                                  <span class="spinner-border spinner-border-sm collapse" wire:loading.class.remove="collapse" wire:loading wire:target="exportRecord" role="status"></span>
                                  <i class="fas fa-file-download"></i></button>
                              </div>
                            </div>
                          </form>
                    </div>
                </div>

                <div class="table-responsive">
                  <table class="table table-bordered mt-5">
                      <thead>
                          <tr>
                              <th>No.</th>
                              <th>User</th>
                              <th>Date</th>
                              <th>Day</th>
                              <th>Attend</th>
                              @for($i=0;$i<6;$i++)
                                <th>Check{{$i%2?'Out':'In'}} {{floor($i/2+1)}}</th>
                              @endfor
                          </tr>
                      </thead>
                      <tbody>
                          @foreach($logs['data'] as $log)
                          @if($log->changes)
                          @endif
                          <tr>
                              <td>{{ $log->id }}</td>
                              <td>{{ $log->employee }}
                                @if($log->changes)
                                  @can('timelog-create')
                                    <button class="btn btn-warning btn-sm ml-2" title="Change Alert" type="button" data-toggle="modal" data-target="#changeModal" onclick="setChangesData({{ json_encode($log->id) }})">
                                      <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                                    </button>
                                  @endcan
                                @endif
                            
                              </td>
                              <td>{{ $log->ck_date }}</td>
                              <td>{{ $log->day }}</td>
                              <td>{{ $log->status }}</td>
                              @for($i=0;$i<6;$i++)
                                @if($i<count($log->punch))
                                  <td>{{$log->punch[$i]['time']}}
                                    @can('timelog-create')
                                    <span onclick="deleteLog({{$log->punch[$i]['id']}},{{ $log->id }})" class="text-danger">&times;</span>
                                    @endcan
                                  </td>
                                @else
                                <td></td>
                                @endif
                              @endfor
                          </tr>
                          @endforeach
                      </tbody>
                  </table>
                  {{$logs['links']}}
              </div>
              </div>
          </div>
      </div>
  </div>
</div>

@push('js-bottom')
<script type="text/javascript">

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

  function setChangesData(changes) {
      document.getElementById('changeData').value = JSON.stringify(changes);
      Livewire.emit('setChangesData', changes);
  }

  function deleteLog(id , rowid) {
        let reason = prompt('Please provide a reason for deletion:');
        if (reason && confirm('Are you sure you want to delete this log?')) {
            Livewire.emit('deleteLog', id, rowid, reason);
        }
    }
</script>
@endpush
