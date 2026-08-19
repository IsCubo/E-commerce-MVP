<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Apply the dark/light theme before anything renders, so the page
         never flashes the wrong theme for a moment on load/navigation. -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark'
            || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    <title>Admin Panel | {{ $globalSettings['brand_name'] ?? config('app.name', 'BeautyShop') }}</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    <style>
        /* Logo in sidebar brand-link: AdminLTE assumes a square image for
           .img-circle. A non-square uploaded logo gets stretched into an
           oval and overflows the collapsed (icon-only) sidebar width.
           Force a fixed square box with a centered crop instead. */
        .brand-image.img-circle {
            width: 33px;
            height: 33px;
            object-fit: cover;
        }

        /* Brand colors for dashboard stat boxes (replace AdminLTE defaults).
           bg-warning's default text color is dark (meant for its original yellow
           background), so text color is forced to white on all four here too. */
        .small-box.bg-info,
        .small-box.bg-success,
        .small-box.bg-warning,
        .small-box.bg-danger {
            color: #fff !important;
        }
        .small-box.bg-info { background-color: #1A1A1A !important; }
        .small-box.bg-success { background-color: #8A6D2F !important; }
        .small-box.bg-warning { background-color: #4A4A4A !important; }
        .small-box.bg-danger { background-color: #6B5223 !important; }
        .small-box.bg-info h3, .small-box.bg-info p,
        .small-box.bg-success h3, .small-box.bg-success p,
        .small-box.bg-warning h3, .small-box.bg-warning p,
        .small-box.bg-danger h3, .small-box.bg-danger p {
            color: #fff !important;
        }

        /* Dark mode transitions - scoped to avoid breaking AdminLTE animations */
        .dark .content-wrapper,
        .dark .main-header,
        .dark .main-footer,
        .dark .card,
        .dark .card-header,
        .dark .form-control,
        .dark label {
            transition: background-color 300ms, color 300ms, border-color 300ms;
        }
        
        /* Dark mode styles for admin */
        .dark .content-wrapper,
        .dark .main-footer {
            background-color: #1f2937 !important;
            color: #f3f4f6 !important;
        }
        .dark .main-header.navbar {
            background-color: #1f2937 !important;
            border-bottom-color: #374151 !important;
        }
        .dark .main-header .nav-link,
        .dark .main-header .btn-link {
            color: #d1d5db !important;
        }
        .dark .main-sidebar {
            background-color: #111827 !important;
        }
        .dark .sidebar {
            background-color: #111827 !important;
        }
        .dark .nav-sidebar .nav-link {
            color: #d1d5db !important;
        }
        .dark .nav-sidebar .nav-link:hover {
            background-color: #1f2937 !important;
            color: #fff !important;
        }
        .dark .nav-sidebar .nav-link.active {
            background-color: #374151 !important;
            color: #fff !important;
        }
        .dark .card {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
        }
        .dark .card-header {
            background-color: #4b5563 !important;
            border-bottom-color: #6b7280 !important;
        }
        .dark .card-title,
        .dark .content-header h1 {
            color: #f3f4f6 !important;
        }
        .dark .small-box {
            color: #fff !important;
        }
        .dark .small-box h3,
        .dark .small-box p {
            color: #fff !important;
        }
        .dark .form-control,
        .dark .form-select,
        .dark textarea,
        .dark .custom-file-input,
        .dark .custom-file-label,
        .dark input[type="file"] {
            background-color: #1f2937 !important;
            border-color: #4b5563 !important;
            color: #f3f4f6 !important;
        }
        .dark .custom-file-label::after {
            background-color: #4b5563 !important;
            border-color: #4b5563 !important;
            color: #f3f4f6 !important;
        }
        .dark .form-control:focus,
        .dark .form-select:focus,
        .dark textarea:focus {
            background-color: #111827 !important;
            border-color: #C5A059 !important;
        }
        .dark .table {
            color: #f3f4f6 !important;
        }
        .dark .table thead th {
            border-color: #4b5563 !important;
        }
        .dark .table td {
            border-color: #374151 !important;
        }
        .dark label {
            color: #e5e7eb !important;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <!-- Dark Mode Toggle -->
            <li class="nav-item">
                <button id="theme-toggle" type="button" class="nav-link btn btn-link">
                    <i id="theme-toggle-dark-icon" class="fas fa-moon" style="display: none;"></i>
                    <i id="theme-toggle-light-icon" class="fas fa-sun" style="display: none;"></i>
                </button>
            </li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link">Cerrar Sesión</button>
                </form>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="{{ route('dashboard') }}" class="brand-link">
             @if(isset($globalSettings['logo_path']) && $globalSettings['logo_path'])
                <img src="{{ asset('storage/' . $globalSettings['logo_path']) }}" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
             @else
                <i class="fas fa-spa brand-image elevation-3" style="opacity: .8; font-size: 1.5rem; line-height: 1.5; margin-left: 0.8rem;"></i>
             @endif
            <span class="brand-text font-weight-light">{{ $globalSettings['brand_name'] ?? 'BeautyShop Admin' }}</span>
        </a>

        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=D4AF37&color=fff" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block">{{ auth()->user()->name }}</a>
                </div>
            </div>

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tags"></i>
                            <p>Categorías</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-box"></i>
                            <p>Productos</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('combos.index') }}" class="nav-link {{ request()->routeIs('combos.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-layer-group"></i>
                            <p>Combos</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>Configuración</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>@yield('title', 'Dashboard')</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                {{-- Flash messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i><strong>Por favor corrige los siguientes errores:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @yield('content')
            </div>
        </section>
    </div>

    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>Version</b> 1.0.0
        </div>
        <strong>Copyright &copy; {{ date('Y') }} {{ $globalSettings['brand_name'] ?? config('app.name', 'BeautyShop') }}.</strong>
    </footer>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<!-- Dark Mode Toggle Script (the theme itself was already applied in
     <head>, before first paint -- this just wires up the icon/button) -->
<script>
    const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

    if (document.documentElement.classList.contains('dark')) {
        themeToggleLightIcon.style.display = 'inline';
    } else {
        themeToggleDarkIcon.style.display = 'inline';
    }

    const themeToggleBtn = document.getElementById('theme-toggle');

    themeToggleBtn.addEventListener('click', function() {
        themeToggleDarkIcon.style.display = themeToggleDarkIcon.style.display === 'none' ? 'inline' : 'none';
        themeToggleLightIcon.style.display = themeToggleLightIcon.style.display === 'none' ? 'inline' : 'none';

        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
        }
    });
</script>

<script>
    // Custom Image Preview Script
    $(document).ready(function() {
        // Init Checkbox Styles
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
            
            // Image Preview
            const file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(event) {
                    $('#imgPreview').attr('src', event.target.result).show();
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Multiple Images Preview
        $('#images').on('change', function() {
            $('#image-preview-container').html(''); // Clear existing
            if (this.files) {
                Array.from(this.files).forEach(file => {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        let html = `
                            <div class="col-md-3 mb-3">
                                <img src="${e.target.result}" class="img-thumbnail" style="height: 150px; width: 100%; object-fit: cover;">
                            </div>
                        `;
                        $('#image-preview-container').append(html);
                    }
                    reader.readAsDataURL(file);
                });
            }
        });
    });
</script>
</body>
</html>
