#!/bin/bash

echo "=== CONFIGURACIÓN COMPLETA PEST CONTROLLER ==="

cd /var/www/html/pest-controller

# Configurar permisos
echo "Configurando permisos..."
sudo chown -R www-data:www-data /var/www/html/pest-controller
sudo chmod -R 755 /var/www/html/pest-controller
sudo chmod -R 775 storage bootstrap/cache

# Configurar .env
echo "Configurando variables de entorno..."
cp .env.example .env
php artisan key:generate

# Configurar base de datos
echo "Configurando base de datos..."
sed -i "s/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/" .env
sed -i "s/DB_HOST=127.0.0.1/DB_HOST=127.0.0.1/" .env
sed -i "s/DB_PORT=3306/DB_PORT=3306/" .env
sed -i "s/DB_DATABASE=laravel/DB_DATABASE=pest_controller/" .env
sed -i "s/DB_USERNAME=root/DB_USERNAME=pest_controller/" .env
sed -i "s/DB_PASSWORD=/DB_PASSWORD=pest_controller_pass/" .env

# Crear base de datos MySQL
echo "Creando base de datos MySQL..."
sudo mysql -e "CREATE DATABASE IF NOT EXISTS pest_controller;"
sudo mysql -e "CREATE USER IF NOT EXISTS pest_controller@localhost IDENTIFIED BY \"pest_controller_pass\";"
sudo mysql -e "GRANT ALL PRIVILEGES ON pest_controller.* TO pest_controller@localhost;"
sudo mysql -e "FLUSH PRIVILEGES;"

# Ejecutar migraciones
echo "Ejecutando migraciones..."
php artisan migrate --force

# Crear usuario administrador
echo "Creando usuario administrador..."
php artisan tinker --execute="
\$user = new App\Models\User();
\$user->name = \"Super Admin\";
\$user->email = \"admin@pestcontroller.cl\";
\$user->password = bcrypt(\"admin123\");
\$user->email_verified_at = now();
\$user->save();
echo \"Usuario administrador creado: admin@pestcontroller.cl / admin123\";
"

# Configurar cache
echo "Configurando cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== CONFIGURACIÓN COMPLETADA ==="
echo "Accede a: http://43.205.110.48"
echo "Usuario: admin@pestcontroller.cl"
echo "Contraseña: admin123"
