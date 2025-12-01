#!/bin/bash

echo "=========================================="
echo "VERIFICACIÓN COMPLETA DEL SISTEMA"
echo "=========================================="
echo ""

echo "1. VERIFICANDO ARCHIVOS MODIFICADOS..."
echo ""

echo "A. TechnicianController.php - processMonitoreoEstadisticasData"
grep -A 5 "historical_data" app/Http/Controllers/TechnicianController.php | head -10
echo ""

echo "B. monitoreo-croquis.blade.php - enctype"
grep "enctype" resources/views/technician/checklist-stages/monitoreo-croquis.blade.php
echo ""

echo "C. service-pdf.blade.php - gráfico de líneas"
grep -c "polyline" resources/views/technician/service-pdf.blade.php
echo ""

echo "2. VERIFICANDO ÚLTIMOS LOGS..."
echo ""
tail -30 storage/logs/laravel.log | grep -E "Estadísticas|Croquis|Bait station"
echo ""

echo "3. VERIFICANDO ÚLTIMO SERVICIO EN BD..."
echo "Ejecuta:"
echo "  php artisan tinker"
echo "  \$s = \\App\\Models\\Service::latest()->first();"
echo "  \$s->id;"
echo "  isset(\$s->checklist_data['monitoreo_estadisticas']['historical_data']);"
echo "  isset(\$s->checklist_data['monitoreo_croquis']['croquis_file']);"
echo "  \$s->checklist_data['monitoreo_completo']['bait_stations'][0]['photos'] ?? 'NO PHOTOS';"
echo ""

echo "4. COMANDOS PARA LIMPIAR CACHÉ..."
echo "  php artisan view:clear"
echo "  php artisan config:clear"
echo "  php artisan cache:clear"
echo ""

echo "=========================================="
echo "FIN DE LA VERIFICACIÓN"
echo "=========================================="
