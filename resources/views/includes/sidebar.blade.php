<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('dashboard')}}}">
        <div class="sidebar-brand-icon rotate-n-15">
            {{-- <i class="fas fa-laugh-wink"></i> --}}
        </div>
        <div class="sidebar-brand-text mx-3">{{config('app.name')}}</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('dashboard')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    @can('user-list')
    <li class="nav-item">
    <a class="nav-link" href="{{route('employees')}}">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Employee</span></a>
    </li>
    @endcan
    
    @can('attendance-list')
    <li class="nav-item">
        <a class="nav-link" href="{{route('attendances')}}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Attendance</span></a>
    </li>
    @endcan

    @can('overtime-list')
    <li class="nav-item">
        <a class="nav-link" href="{{route('overtime')}}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Overtime</span></a>
    </li>
    @endcan

    @can('timelog-list')
    <li class="nav-item">
        <a class="nav-link" href="{{route('timesheets')}}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Attendance logs</span></a>
    </li>
    @endcan

    @can('report-list')
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAR" aria-expanded="false" aria-controls="collapseAR">
            <i class="fas fa-fw fa-cog"></i>
            <span>Reports</span>
        </a>
        <div id="collapseAR" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar" style="">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Reports:</h6>
                <a class="collapse-item" href="{{route('reports.attendance')}}">Attendance</a>
                <a class="collapse-item" href="{{route('reports.ot')}}">OT</a>
            </div>
        </div>
        
    </li>
    @endcan
    @can('schedule-list')
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSchedule" aria-expanded="false" aria-controls="collapseSchedule">
            <i class="fas fa-fw fa-cog"></i>
            <span>Schedule Management</span>
        </a>
        <div id="collapseSchedule" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar" style="">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Schedule Management:</h6>
                <a class="collapse-item" href="#">Employee Schedule</a>
                <a class="collapse-item" href="#">Holidays</a>
            </div>
        </div>
    </li>
    @endcan
    @can('leave-list')
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLeave" aria-expanded="false" aria-controls="collapseLeave">
            <i class="fas fa-fw fa-cog"></i>
            <span>Leave Management</span>
        </a>
        <div id="collapseLeave" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar" style="">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Leave Management:</h6>
                <a class="collapse-item" href="#">Employee Leaves</a>
                <a class="collapse-item" href="#">Leave Types</a>
            </div>
        </div>
    </li>
    @endcan
    @can('role-create')
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-cog"></i>
            <span>Role Management</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar" style="">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Role Management:</h6>
                <a class="collapse-item" href="{{route('roles')}}">Roles</a>
                <a class="collapse-item" href="{{route('assign-roles')}}">Assign Role</a>
            </div>
        </div>
    </li>
    @endcan

    @can('setting-list')
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSetting" aria-expanded="false" aria-controls="collapseSetting">
            <i class="fas fa-fw fa-cog"></i>
            <span>Settings</span>
        </a>
        <div id="collapseSetting" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar" style="">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Settings:</h6>
                <a class="collapse-item" href="{{route('recompute')}}">Recompute</a>
            </div>
        </div>
    </li>
    @endcan
    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>