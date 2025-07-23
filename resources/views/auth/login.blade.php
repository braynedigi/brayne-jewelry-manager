<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ \App\Models\Setting::getValue('app_title', 'Jewelry Manager') }}</title>
    
    @php
    use App\Models\Setting;
    @endphp
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #D4AF37;
            --primary-dark: #B8860B;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --light-bg: #f8fafc;
            --dark-bg: #1e293b;
            --border-color: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: {{ \App\Models\Setting::getValue('login_background_color', '#f8fafc') }};
            @if(\App\Models\Setting::getValue('login_background_image'))
                background-image: url('{{ asset('storage/' . \App\Models\Setting::getValue('login_background_image')) }}');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
            @endif
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: white;
            border-radius: 1rem;
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            margin: 2rem;
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .login-logo {
            margin-bottom: 1rem;
            text-align: center;
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-logo img {
            max-height: 80px;
            max-width: 200px;
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
        }

        .login-logo .default-logo {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .login-subtitle {
            opacity: 0.9;
            margin: 0.5rem 0 0 0;
        }

        .login-body {
            padding: 2rem;
        }

        .form-control {
            border-radius: 0.75rem;
            border: 2px solid var(--border-color);
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            font-weight: 400;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 0.75rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .alert {
            border-radius: 0.75rem;
            border: none;
            padding: 1rem 1.5rem;
        }

        .input-group-text {
            background: var(--light-bg);
            border: 2px solid var(--border-color);
            border-right: none;
            border-radius: 0.75rem 0 0 0.75rem;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 0.75rem 0.75rem 0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">
                @if(\App\Models\Setting::getValue('login_logo'))
                    <img src="{{ asset('storage/' . \App\Models\Setting::getValue('login_logo')) }}" 
                         alt="Company Logo"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <div class="default-logo" style="display: none;">
                        <i class="fas fa-gem"></i>
                    </div>
                @else
                    <div class="default-logo">
                        <i class="fas fa-gem"></i>
                    </div>
                @endif
            </div>
            <h1 class="login-title">{{ \App\Models\Setting::getValue('company_name', 'Jewelry Manager') }}</h1>
            <p class="login-subtitle">Sign in to your account</p>
        </div>
        
        <div class="login-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Debug logo loading
        document.addEventListener('DOMContentLoaded', function() {
            const logoImg = document.querySelector('.login-logo img');
            if (logoImg) {
                console.log('Logo image found:', logoImg.src);
                console.log('Logo alt text:', logoImg.alt);
                console.log('Logo display style:', logoImg.style.display);
                console.log('Logo computed style:', window.getComputedStyle(logoImg).display);
                
                // Check if image is loaded
                if (logoImg.complete) {
                    console.log('Logo already loaded, natural dimensions:', logoImg.naturalWidth + 'x' + logoImg.naturalHeight);
                    if (logoImg.naturalWidth === 0) {
                        console.log('Logo has zero dimensions, likely failed to load');
                    }
                }
                
                logoImg.addEventListener('load', function() {
                    console.log('Logo loaded successfully');
                    console.log('Logo dimensions:', this.naturalWidth + 'x' + this.naturalHeight);
                });
                
                logoImg.addEventListener('error', function() {
                    console.log('Logo failed to load, showing fallback');
                    console.log('Error details:', this.src);
                    this.style.display = 'none';
                    const fallback = this.nextElementSibling;
                    if (fallback && fallback.classList.contains('default-logo')) {
                        fallback.style.display = 'block';
                    }
                });
                
                // Test image loading manually
                const testImg = new Image();
                testImg.onload = function() {
                    console.log('Test image loaded successfully:', this.src);
                };
                testImg.onerror = function() {
                    console.log('Test image failed to load:', this.src);
                };
                testImg.src = logoImg.src;
            } else {
                console.log('No logo image found, using default');
            }
        });
    </script>
</body>
</html> 