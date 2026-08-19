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

    <title>Iniciar Sesión - {{ $globalSettings['brand_name'] ?? 'BeautyShop' }}</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    
    <style>
        /* Dark mode transitions */
        * {
            transition: background-color 300ms, color 300ms, border-color 300ms;
        }
        
        /* Dark mode styles */
        .dark body {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%) !important;
        }
        .dark .login-box {
            background-color: transparent;
        }
        .dark .card {
            background-color: #374151 !important;
            border: 1px solid #4b5563 !important;
        }
        .dark .card-body {
            background-color: #374151 !important;
        }
        .dark .card-header {
            background-color: #4b5563 !important;
            border-bottom: 1px solid #6b7280 !important;
        }
        .dark .login-logo a,
        .dark .card-header .h1,
        .dark .login-box-msg,
        .dark label {
            color: #f3f4f6 !important;
        }
        .dark .form-control {
            background-color: #1f2937 !important;
            border-color: #4b5563 !important;
            color: #f3f4f6 !important;
        }
        .dark .form-control:focus {
            background-color: #111827 !important;
            border-color: #C5A059 !important;
            color: #f3f4f6 !important;
        }
        .dark .input-group-text {
            background-color: #4b5563 !important;
            border-color: #4b5563 !important;
            color: #9ca3af !important;
        }
        .dark .alert-danger {
            background-color: #7f1d1d !important;
            border-color: #991b1b !important;
            color: #fecaca !important;
        }
        .dark .btn-link {
            color: #C5A059 !important;
        }
        .dark .icheck-primary > input:first-child:checked + label::before {
            background-color: #C5A059 !important;
            border-color: #C5A059 !important;
        }
    </style>
</head>
<body class="hold-transition login-page">
    <!-- Dark Mode Toggle -->
    <button id="theme-toggle" type="button" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999;" class="btn btn-sm btn-outline-secondary">
        <i id="theme-toggle-dark-icon" class="fas fa-moon" style="display: none;"></i>
        <i id="theme-toggle-light-icon" class="fas fa-sun" style="display: none;"></i>
    </button>

<div class="login-box">
    <div class="login-logo">
        <a href="{{ route('home') }}"><b>{{ $globalSettings['brand_name'] ?? 'Beauty' }}</b>Shop</a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Inicia sesión para comenzar</p>

            <form action="{{ route('login') }}" method="post">
                @csrf
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Contraseña" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember">
                                Recuérdame
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Entrar</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <p class="mb-0">
                <a href="{{ route('home') }}" class="btn btn-link">Volver al sitio</a>
            </p>
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->

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

</body>
</html>
