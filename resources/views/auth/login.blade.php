<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pest Controller - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #000000;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        /* Patrón de fondo sutil */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(45, 90, 39, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(45, 90, 39, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 450px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 1;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2d5a27 0%, #1a3d1a 100%);
            border-radius: 20px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(45, 90, 39, 0.3);
        }
        
        .logo-icon svg {
            width: 40px;
            height: 40px;
            fill: white;
        }
        
        .logo h1 {
            color: #2d5a27;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .logo p {
            color: #666;
            font-size: 16px;
            font-weight: 300;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #2d5a27;
            background: white;
            box-shadow: 0 0 0 3px rgba(45, 90, 39, 0.1);
        }
        
        .login-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #2d5a27 0%, #1a3d1a 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 16px rgba(45, 90, 39, 0.3);
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(45, 90, 39, 0.4);
        }
        
        .login-btn:active {
            transform: translateY(0);
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: 1px solid #f5c6cb;
            font-size: 14px;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: 1px solid #c3e6cb;
            font-size: 14px;
        }
        
        .credentials-info {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            border-left: 4px solid #2d5a27;
        }
        
        .credentials-info h3 {
            color: #2d5a27;
            font-size: 16px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .credential-item {
            margin-bottom: 12px;
            padding: 10px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .credential-item .role {
            font-weight: 600;
            color: #2d5a27;
            font-size: 14px;
        }
        
        .credential-item .email {
            color: #666;
            font-size: 13px;
            margin-top: 2px;
        }
        
        .credential-item .password {
            color: #999;
            font-size: 12px;
            font-family: monospace;
            margin-top: 2px;
        }
        
        .copy-btn {
            background: #2d5a27;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            cursor: pointer;
            margin-left: 8px;
        }
        
        .copy-btn:hover {
            background: #1a3d1a;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </div>
            <h1>Pest Controller</h1>
            <p>Sistema de Control de Plagas</p>
        </div>
        
        @if(session('error'))
            <div class="error-message">
                {{ session('error') }}
            </div>
        @endif
        
        @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="login-btn">
                Iniciar Sesión
            </button>
        </form>
        
        <div class="credentials-info">
            <h3>🔑 Credenciales de Ejemplo</h3>
            
            <div class="credential-item">
                <div class="role">👨‍💼 Super Administrador</div>
                <div class="email">admin@pestcontroller.com</div>
                <div class="password">Contraseña: 123456</div>
                <button class="copy-btn" onclick="copyToClipboard('admin@pestcontroller.com')">Copiar</button>
            </div>
            
            <div class="credential-item">
                <div class="role">👨‍🔧 Técnico de Campo</div>
                <div class="email">tecnico@pestcontroller.com</div>
                <div class="password">Contraseña: 123456</div>
                <button class="copy-btn" onclick="copyToClipboard('tecnico@pestcontroller.com')">Copiar</button>
            </div>
        </div>
    </div>
    
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Cambiar temporalmente el texto del botón
                const btn = event.target;
                const originalText = btn.textContent;
                btn.textContent = '¡Copiado!';
                btn.style.background = '#28a745';
                
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.style.background = '#2d5a27';
                }, 1500);
            });
        }
        
        // Auto-rellenar campos al hacer clic en las credenciales
        document.addEventListener('DOMContentLoaded', function() {
            const credentialItems = document.querySelectorAll('.credential-item');
            
            credentialItems.forEach(item => {
                item.addEventListener('click', function() {
                    const email = this.querySelector('.email').textContent;
                    const password = '123456';
                    
                    document.getElementById('email').value = email;
                    document.getElementById('password').value = password;
                });
            });
        });
    </script>
</body>
</html>
