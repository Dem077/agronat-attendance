
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