<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BEVARARI - Login</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font & Custom Style -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            /* background: linear-gradient(135deg, #4f46e5, #3b82f6); */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('/app-bg.jpeg');
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-image {
            background: url('https://1.bp.blogspot.com/-85w3nKQ5gOg/XQs8JTEA3FI/AAAAAAAAQ8o/ja1bjNAnsCYYGoN1VKFKqqw12EEXKuuGACLcBGAs/s1600/Ma%2527soem%2BUniversity.jpg') center/cover no-repeat;
        }

        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
        }

        .btn-primary {
            background-color: #4f46e5;
            border: none;
        }

        .btn-primary:hover {
            background-color: #4338ca;
        }

        .brand-title {
            font-weight: 600;
            color: #4f46e5;
            letter-spacing: 0.5px;
        }

        .small-link {
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="row g-0">
                        <!-- Gambar kiri -->
                        <div class="col-md-6 d-none d-md-block login-image"></div>

                        <!-- Form kanan -->
                        <div class="col-md-6 bg-white p-5">
                            <div class="text-center mb-4">
                                <h3 class="brand-title">BEVARARI Apps</h3>
                                <p class="text-muted">Silakan masuk untuk melanjutkan</p>
                            </div>

                            <!-- Pesan error -->
                            @if ($errors->any())
                                <div class="alert alert-danger small py-2">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <form method="POST" >
                                @csrf
                                @method('POST')
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" required
                                        autofocus placeholder="nama@email.com" value="{{ old('email') }}">
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" required
                                        placeholder="">
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                        <label class="form-check-label small" for="remember">
                                            Ingat saya
                                        </label>
                                    </div>
                                    {{-- <a href="#" class="small-link text-decoration-none text-primary">Lupa
                                        password?</a> --}}
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary py-2">
                                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                                    </button>
                                </div>
                            </form>
                            {{-- 
                            <div class="text-center mt-4 small-link">
                                Belum punya akun?
                                <a href="#" class="text-primary text-decoration-none fw-semibold">Daftar
                                    sekarang</a>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</body>

</html>