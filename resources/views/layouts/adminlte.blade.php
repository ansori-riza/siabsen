<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield("title", $activeSekolah ?? $sekolah ?? \App\Models\Sekolah::first()->nama ?? "SiAbsen") - Sistem Absensi Sekolah</title>
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    @php
        $activeSekolah = $activeSekolah ?? $sekolah ?? \App\Models\Sekolah::where('is_active', true)->first() ?? \App\Models\Sekolah::first();
    @endphp
    <div class="wrapper">
        
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="fas fa-school mr-1"></i>
                        {{ $school->nama ?? 'Dashboard' }}
                    </a>
                </li>
            </ul>
            
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-user-circle"></i> {{ Auth::user()->name ?? 'Administrator' }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-user mr-2"></i> Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>
        
        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('dashboard') }}" class="brand-link">
                @php
                    $school = $activeSekolah ?? $sekolah ?? \App\Models\Sekolah::where('is_active', true)->first() ?? \App\Models\Sekolah::first();
                @endphp
                @if($school && $school->logo)
                    <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->nama }}" class="brand-image img-circle elevation-3" style="opacity: .8; width: 33px; height: 33px; object-fit: cover;">
                @else
                    <i class="fas fa-school brand-image img-circle elevation-3" style="width: 33px; height: 33px; line-height: 33px; text-align: center; background: #fff; color: #007bff;"></i>
                @endif
                <span class="brand-text font-weight-light">{{ $school->nama ?? 'SiAbsen' }}</span>
            </a>
            
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        
                        <!-- Role-based menu items -->
                        @php
                            $user = Auth::user();
                            $isSuperAdmin = $user && $user->role === 'super_admin';
                            $isOperator = $user && $user->role === 'operator';
                            $canManageData = $isSuperAdmin || $isOperator;
                        @endphp

                        <!-- MASTER DATA section - only for super_admin and operator -->
                        @if($canManageData)
                        <li class="nav-header">MASTER DATA</li>

                        <li class="nav-item">
                            <a href="{{ route('sekolah.index') }}" class="nav-link {{ request()->routeIs('sekolah.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-building"></i>
                                <p>Sekolah</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('guru.index') }}" class="nav-link {{ request()->routeIs('guru.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-chalkboard-teacher"></i>
                                <p>Guru</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('murid.index') }}" class="nav-link {{ request()->routeIs('murid.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user-graduate"></i>
                                <p>Data Murid</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('kelas.index') }}" class="nav-link {{ request()->routeIs('kelas.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-school"></i>
                                <p>Data Kelas</p>
                            </a>
                        </li>
                        @endif

                        <!-- ABSENSI - available for all authenticated users -->
                        <li class="nav-header">ABSENSI</li>

                        <li class="nav-item">
                            <a href="{{ route('absensi.index') }}" class="nav-link {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-clipboard-check"></i>
                                <p>Monitoring Absensi</p>
                            </a>
                        </li>

                        <!-- PENGATURAN - only for super_admin -->
                        @if($isSuperAdmin)
                        <li class="nav-header">PENGATURAN</li>

                        <li class="nav-item">
                            <a href="{{ route('jadwal-sekolah.index') }}" class="nav-link {{ request()->routeIs('jadwal-sekolah.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-calendar-alt"></i>
                                <p>Jadwal Sekolah</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('perangkat.index') }}" class="nav-link {{ request()->routeIs('perangkat.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-mobile-alt"></i>
                                <p>Perangkat</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('user.index') }}" class="nav-link {{ request()->routeIs('user.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users-cog"></i>
                                <p>Manajemen User</p>
                            </a>
                        </li>
                        @endif
                        
                    </ul>
                </nav>
            </div>
        </aside>
        
        <!-- Content -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            
            <section class="content">
                <div class="container-fluid">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="icon fas fa-check"></i> {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="icon fas fa-ban"></i> {{ session('error') }}
                        </div>
                    @endif
                    
                    @yield('content')
                </div>
            </section>
        </div>
        
        <!-- Footer -->
        <footer class="main-footer">
            <strong>SiAbsen &copy; 2026</strong> - Sistem Absensi Sekolah
        </footer>
        
    </div>
    
    <!-- AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    
    @stack('scripts')
</body>
</html>