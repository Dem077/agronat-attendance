
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
                            <div class="alert alert-info">
                                <h6><strong>Attendance Month: </strong>{{$month}}</h6>
                                <h6><strong>Attendance Period: </strong>{{$from_date}} to {{$to_date}}</h6>
                            </div>
                            

                            <div class="row">
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
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-info shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Late Minutes</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{$attendance['Latemin']??'0'}}</div>
                                                </div>
                                                <div class="col-auto">
                        
                                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-danger shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Absent</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{$attendance['Absent']??'0'}}</div>
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
        
                            {{-- <livewire:charts.monthly-attendance key="{{ now() }}" :period="$period"> --}}
        
                        </div>


                    </div>
                </div>
            </div>
            <!-- Area Chart -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><strong>Leave Balance</strong></h6>
                    </div>
            
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Leave Type</th>
                                    <th class="text-center" scope="col ">Balance</th>
                                    <th class="text-center" scope="col">Allocated</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="">Sick Leave (With Certificate)</td>
                                    <td class="text-center text-gray-800">{{$balance['Sick Leave (With Certificate)']??'NA'}}</td>
                                    <td class="text-center text-gray-800 table-active">15</td>
                                </tr>
                                <tr>
                                    <td class="">Sick Leave (Without Certificate)</td>
                                    <td class="text-center text-gray-800">{{$balance['Sick Leave (Without Certificate)']??'NA'}}</td>
                                    <td class="text-center text-gray-800 table-active">15</td>
                                </tr>
                                <tr>
                                    <td class="">Family Leave</td>
                                    <td class="text-center text-gray-800">{{$balance['Family Leave']??'NA'}}</td>
                                    <td class="text-center text-gray-800 table-active">10</td>
                                </tr>
                                <tr>
                                    <td class="">Annual Leave</td>
                                    <td class="text-center text-gray-800">{{$balance['Annual Leave']??'NA'}}</td>
                                    <td class="text-center text-gray-800 table-active">30</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            
                </div>
            </div>
            
        </div>
    
    </div>