# 🐛 Problemas Identificados

## 1. Gráfico sin Datos Históricos

### **Problema**
El gráfico en el PDF no tiene los mismos datos que la vista web porque `historical_data` no se está guardando en la base de datos.

### **Causa**
El método `processMonitoreoEstadisticasData` no guarda el campo `historical_data`.

```php
// Línea 1094-1107 - TechnicianController.php
private function processMonitoreoEstadisticasData(Request $request)
{
    return [
        'total_monitored' => $request->input('total_monitored', 0),
        // ... otros campos
        // ❌ FALTA: 'historical_data' => ...
    ];
}
```

### **Solución**
Agregar el campo `historical_data` al método:

```php
private function processMonitoreoEstadisticasData(Request $request)
{
    // Obtener datos históricos del monitoreo completo
    $service = Service::find($request->route('service'));
    $monitoreoCompleto = $service->checklist_data['monitoreo_completo'] ?? [];
    $baitStations = $monitoreoCompleto['bait_stations'] ?? [];
    
    // Calcular datos históricos basados en las cebaderas
    $historicalData = [];
    $today = \Carbon\Carbon::today();
    
    for ($i = 6; $i >= 0; $i--) {
        $date = $today->copy()->subDays($i);
        
        // En el día actual, usar datos reales
        if ($i === 0) {
            $totalConsumption = 0;
            $totalCaptures = 0;
            $count = 0;
            
            foreach ($baitStations as $station) {
                if (isset($station['consumption'])) {
                    $totalConsumption += floatval($station['consumption']);
                    $count++;
                }
                if (isset($station['captures'])) {
                    $totalCaptures += intval($station['captures']);
                }
            }
            
            $avgConsumption = $count > 0 ? $totalConsumption / $count : 0;
            
            $historicalData[] = [
                'date' => $date->format('Y-m-d'),
                'consumption_percent' => round($avgConsumption, 1),
                'captures' => $totalCaptures
            ];
        } else {
            // Días anteriores sin datos
            $historicalData[] = [
                'date' => $date->format('Y-m-d'),
                'consumption_percent' => 0,
                'captures' => 0
            ];
        }
    }
    
    return [
        'total_monitored' => $request->input('total_monitored', 0),
        'total_active' => $request->input('total_active', 0),
        'total_problems' => $request->input('total_problems', 0),
        'total_traps' => $request->input('total_traps', 0),
        'total_consumption' => $request->input('total_consumption', 0),
        'average_consumption_percent' => $request->input('average_consumption_percent', 0),
        'detected_species' => $request->input('detected_species', ''),
        'activity_level' => $request->input('activity_level', ''),
        'executive_summary' => $request->input('executive_summary', ''),
        'historical_data' => $historicalData, // ✅ AGREGADO
    ];
}
```

## 2. Imágenes No se Guardan en BD

### **Problema**
Las imágenes no se ven en el detalle del servicio ni en el PDF.

### **Posibles Causas**

1. **El formulario no tiene `enctype="multipart/form-data"`**
2. **Las imágenes no se están procesando correctamente**
3. **Los datos no se están guardando en `checklist_data`**

### **Verificación Necesaria**

#### **A. Verificar formulario de croquis**
```bash
grep "enctype" resources/views/technician/checklist-stages/monitoreo-croquis.blade.php
```

Debe tener:
```html
<form method="POST" action="..." enctype="multipart/form-data">
```

#### **B. Verificar que se guarde en BD**
```bash
php artisan tinker
```
```php
$service = \App\Models\Service::latest()->first();
dd($service->checklist_data);
```

Debe mostrar:
```php
[
  "monitoreo_croquis" => [
    "croquis_file" => "storage/services/croquis/XXXXX.png",
    "croquis_notes" => "..."
  ],
  "monitoreo_completo" => [
    "bait_stations" => [
      [
        "photos" => ["storage/services/bait-stations/XXXXX.png"]
      ]
    ]
  ]
]
```

#### **C. Verificar logs**
```bash
tail -100 storage/logs/laravel.log | grep -i "croquis\|bait station photo"
```

Buscar:
- ✅ `Croquis file saved`
- ✅ `Bait station photo saved`
- ✅ `Saving checklist_data to database`

### **Solución Si Falta `enctype`**

Agregar al formulario:
```html
<form method="POST" action="..." data-stage="monitoreo-croquis" enctype="multipart/form-data">
```

## 3. Resumen de Acciones

### **Inmediato**
1. ✅ Agregar `historical_data` al método `processMonitoreoEstadisticasData`
2. ✅ Verificar `enctype` en formularios
3. ✅ Verificar logs de guardado

### **Verificación**
1. Completar un monitoreo nuevo
2. Verificar en tinker que los datos se guardaron
3. Generar PDF y verificar que se vean:
   - Gráfico con datos correctos
   - Imágenes de croquis
   - Imágenes de cebaderas

### **Comandos de Verificación**

```bash
# 1. Verificar archivos guardados
ls -lh storage/app/public/services/croquis/
ls -lh storage/app/public/services/bait-stations/

# 2. Verificar datos en BD
php artisan tinker
$service = \App\Models\Service::latest()->first();
$service->checklist_data['monitoreo_croquis'];
$service->checklist_data['monitoreo_completo']['bait_stations'][0]['photos'];
$service->checklist_data['monitoreo_estadisticas']['historical_data'];

# 3. Verificar logs
tail -50 storage/logs/laravel.log | grep "Saving checklist_data"
```
