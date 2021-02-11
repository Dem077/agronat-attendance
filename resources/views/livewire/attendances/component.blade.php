
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h2>Attendance</h2>
            </div>

            <div class="card-body">
                <div>
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif
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
                    {{-- @include('livewire.users.update')
                    @include('livewire.users.create') --}}
                  <div class="table-responsive">
                    <table class="table table-bordered mt-5">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>User</th>
                                <th>Date</th>
                                <th>Duty Start</th>
                                <th>Duty End</th>
                                <th>Checkin</th>
                                <th>Checkout</th>
                                <th>Late min</th>
                                <th>Status</th>
                                @can('attendance-update')
                                <td>
                                    Action
                                </td>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                            <tr class="{{$attendance->late_min>0?'text-danger':''}}">
                                <td>{{ $attendance->id }}</td>
                                <td>{{ $attendance->user->name }}</td>
                                <td>{{ $attendance->ck_date }}</td>
                                <td>{{ $attendance->scin }}</td>
                                <td>{{ $attendance->scout }}</td>
                                <td>{{ $attendance->in }}</td>
                                <td>{{ $attendance->out }}</td>
                                <td>{{ $attendance->late_min }}</td>
                                <td>{{ $attendance->status }}</td>
                                @can('attendance-update')
                                <td>
                                    <button data-toggle="modal" data-target="#updateModal" wire:click="edit({{ $user->id }})" class="btn btn-primary btn-sm">Edit</button>
                                </td>
                                @endcan
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$attendances->links()}}
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
</script>
@endpush
