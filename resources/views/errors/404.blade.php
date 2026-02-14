<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - RV Rising</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a1a2e;
            --accent: #e94560;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-container {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            max-width: 500px;
        }

        .error-code {
            font-size: 8rem;
            font-weight: 700;
            color: var(--primary);
            line-height: 1;
            margin-bottom: 0;
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .error-message {
            color: #6c757d;
            margin-bottom: 2rem;
        }

        .error-icon {
            font-size: 4rem;
            color: var(--accent);
            margin-bottom: 1rem;
        }

        .btn-home {
            background: var(--accent);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-home:hover {
            background: #d63854;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <div class="error-container">
        <i class="fas fa-search error-icon"></i>
        <h1 class="error-code">404</h1>
        <h2 class="error-title">Page Not Found</h2>
        <p class="error-message">The page you're looking for doesn't exist or has been moved.</p>
        <a href="/" class="btn-home">
            <i class="fas fa-home me-2"></i>Back to Homepage
        </a>
    </div>
</body>

</html>