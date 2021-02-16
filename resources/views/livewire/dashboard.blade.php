
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
                            <h6 class="h6 mb-0 text-gray-800 text-center">{{$from_date}} to {{$to_date}}</h6>
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
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{$attendance['Absent']??'0'}}</div>
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