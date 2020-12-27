
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>

    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Present</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$attendance['Present']??'0'}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->

        <!-- Earnings (Monthly) Card Example -->

        <!-- Pending Requests Card Example -->

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Absent</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$attendance['']??'0'}}</div>
                        </div>
                        <div class="col-auto">

                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">On Leave</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$attendance['Leave']??'0'}}</div>
                        </div>
                        <div class="col-auto">

                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->

    <div class="row">

        <!-- Area Chart -->
        <div class="col-xl-8 col-lg-8">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div
                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Attendance by period</h6>
                    <div class="dropdown no-arrow">
                        <a
                            class="dropdown-toggle"
                            href="#"
                            role="button"
                            id="dropdownMenuLink"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div
                            class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Dropdown Header:</div>
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <form>
                                <div class="form-row align-items-center">
                                  <div class="col-sm-3 my-1">
                                    <label class="sr-only" for="inlineFormInputName">Employee</label>
                                    <select type="text" class="form-control" id="inlineFormInputName" placeholder="Employee" wire:bind="user_id" >
                                        <option value="">Select Employee</option>
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
                    <livewire:line-chart key="{{ now() }}" :period="$period">

                </div>
            </div>
        </div>

    </div>

    <!-- Content Row -->

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