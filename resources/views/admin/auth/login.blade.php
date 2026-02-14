<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - {{ App\Models\SiteSetting::get('site_name', 'SmartTable CMS') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo img {
            max-height: 50px;
        }

        .login-logo h2 {
            color: #1e1e2d;
            font-weight: 700;
            margin-top: 1rem;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="login-logo">
            @if($logo = App\Models\SiteSetting::getLogo())
            <img src="{{ $logo }}" alt="Logo">
            @else
            <i class="fas fa-table fa-3x text-primary"></i>
            @endif
            <h2>{{ App\Models\SiteSetting::get('site_name', 'SmartTable CMS') }}</h2>
            <p class="text-muted">Sign in to your admin account</p>
        </div>

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                {{ $error }}<br>
                @endforeach
            </div>
            @endif

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Sign In
            </button>
        </form>
    </div>
</body>

</html>