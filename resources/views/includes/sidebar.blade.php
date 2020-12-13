<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            {{-- <i class="fas fa-laugh-wink"></i> --}}
        </div>
        <div class="sidebar-brand-text mx-3">{{config('app.name')}}</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('employee.dashboard')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <li class="nav-item">
    <a class="nav-link" href="{{route('employees')}}">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Employee</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('attendances')}}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Attendance</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('overtime')}}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Overtime</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('timesheets')}}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Attendance logs</span></a>
    </li>
    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>