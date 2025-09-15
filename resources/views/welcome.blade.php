<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pest Controller - Sistema de Control de Plagas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#000000">
</head>
<body class="bg-black min-h-screen">
    <div class="min-h-screen flex items-center justify-center bg-black">
        <div class="max-w-md w-full space-y-8 p-8">
            <!-- Logo -->
            <div class="text-center">
                <div class="flex justify-center mb-6">
                    <img src="https://pestcontroller.cl/wp-content/uploads/2022/07/pestcontroller-logo.png" 
                         alt="Pest Controller Logo" 
                         class="h-24 w-auto">
                </div>
                <p class="text-gray-300 mb-8">Sistema de Gestión para Control de Plagas</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-xl p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Iniciar Sesión</h2>
                
                <form method="POST" action="{{ route("login") }}" class="space-y-6">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" type="email" name="email" value="{{ old("email") }}" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                        <input id="password" type="password" name="password" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" 
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-900">Recordarme</label>
                        </div>
                    </div>
                    
                    <button type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Iniciar Sesión
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Usuario demo: <strong>admin@pestcontroller.cl</strong><br>
                        Contraseña: <strong>admin123</strong>
                    </p>
                </div>
            </div>
            
            <div class="text-center text-gray-400 text-sm">
                <p>&copy; 2024 Pest Controller. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>
</body>
</html>
