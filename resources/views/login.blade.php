<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN</title>
    <link href="{{ asset('assets/img/motor-logo.svg') }}" rel="icon">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}">
    
    <!-- Ultra 3D Theme CSS -->
    <link href="{{ asset('assets/css/ultra-3d-theme.css') }}" rel="stylesheet">
</head>
<body class="auth-page" style="background:#000 url('{{ asset('assets/img/motor-bg.svg') }}') center / 420px no-repeat fixed;">

    {{-- Halaman login. Form dikirim ke LoginController method aksilogin() --}}
    <div class="container d-flex justify-content-center align-items-center min-vh-100">

        <div class="row justify-content-center w-100">
            <div class="col-md-5 col-lg-4">
                <div class="auth-card card shadow-sm border-0 border-top border-warning border-3">
                    <div class="card-body">

                        <div class="text-center mb-4">
                            <img src="{{ asset('assets/img/motor-logo.svg') }}" alt="Logo" height="64">
                            <h2 class="fw-bold mt-3 mb-1">LOGIN</h2>
                            <p class="text-muted small mb-0">Bengkel Pintar</p>
                        </div>

                        <form method="POST" action="/login">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" name="u" id="name" class="form-control" required placeholder="Masukkan username">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="p" id="password" class="form-control" required placeholder="Masukkan password">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-warning text-dark fw-semibold rounded-pill w-100 mb-2 py-2">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>

                            <a href="/girasya" class="btn btn-outline-secondary rounded-pill w-100 py-2 fw-semibold">
                                <i class="bi bi-person-plus"></i> Register
                            </a>

                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
