
<div class = "row justify-content-center" >

<!-- Page Heading -->


    <div class="col-md-8">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            
        </div>

    <!-- Content Row -->

    <div class="row">

        <!-- Area Chart -->
        <div class="col">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                {{-- <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    
                    
                </div> --}}
                <!-- Card Body -->
                <div class="card-body">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h6 class="h6 mb-0 text-gray-800"><strong>Today</strong></h6>
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
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" style="min-width: 300px; padding: 20px; position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-283px, 23px, 0px);"
                                ar ia-labelledby="dropdownMenuLink">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="user_id">Employees</label>
                                            <input type="text" class="form-control" id="user_id" wire:model="user_id"  aria-describedby="empHelp" placeholder="Select Employee">
                                            <small id="empHelp" class="form-text text-danger">error message.</small>
                                        </div>
                                        <div class="form-group">
                                            <label for="from_date">From date</label>
                                            <input type="date" class="form-control" id="from_date" wire:model="from_date" aria-describedby="fdHelp" placeholder="from date">
                                            <small id="fdHelp" class="form-text text-danger">error message.</small>
                                        </div>
                                        <div class="form-group">
                                            <label for="to_date">To date</label>
                                            <input type="date" class="form-control" id="to_date" wire:model="to_date" aria-describedby="tdHelp" placeholder="to date">
                                            <small id="tdHelp" class="form-text text-danger">error message.</small>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-4 col-md-6 mb-4">
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
                        <div class="col-xl-4 col-md-6 mb-4">
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
                        <div class="col-xl-4 col-md-6 mb-4">
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

                    <livewire:line-chart key="{{ now() }}" :period="$period">

                </div>
            </div>
        </div>

    </div>

    </div>

<!-- Content Row -->

</div>
@push('js-bottom')
<script type = "text/javascript" > var options = {
    format: 'yyyy-mm-dd',
    todayHighlight: true,
    autoclose: true
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