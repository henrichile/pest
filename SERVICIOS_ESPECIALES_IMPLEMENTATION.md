# 🚀 IMPLEMENTACIÓN: Servicios Especiales con Título Personalizado

## 📋 REQUERIMIENTO

Implementar funcionalidad para **"servicios-especiales"** que permite:

1. ✅ **Campo de título** al crear el servicio
2. ✅ **Flujo especial** para técnicos: Localización → Observaciones → Sitios → Descripción
3. ✅ **Visualización del título** en detalles y PDF

---

## 🗄️ CAMBIOS EN BASE DE DATOS

### Nueva Migración: `add_special_service_title_to_services_table.php`

```php
Schema::table('services', function (Blueprint $table) {
    $table->string('special_service_title')->nullable()->after('service_type');
});
```

**Campo agregado:**
- `special_service_title` (string, nullable)
- Posición: Después de `service_type`
- Propósito: Almacenar el título personalizado del servicio especial

---

## 💻 CAMBIOS EN EL BACKEND

### 1. Modelo Service (`app/Models/Service.php`)

#### Cambio 1: Agregar campo a fillable

```php
protected $fillable = [
    "client_id",
    "service_type",
    "special_service_title", // ✅ NUEVO
    "service_type_id",
    // ... resto de campos
];
```

#### Cambio 2: Actualizar método getStages()

```php
public function getStages($type = null) {
    if ($type == 'desratizacion') {
        $stages = ["points", "products", "results", "observations", "sites", "description"];
    } elseif ($type == 'desinsectacion') {
        $stages = ["products", "results", "observations", "sites", "description"];
    } elseif ($type == 'servicios-especiales') {
        // ✅ NUEVO: Flujo especial para servicios especiales
        $stages = ["observations", "sites", "description"];
    } else {
        $stages = ["products", "observations", "sites", "description"];
    }
    return $stages;
}
```

**Flujo de servicios especiales:**
```
Localización (captura GPS) → Observations → Sites → Description
```

---

### 2. ServiceController (`app/Http/Controllers/ServiceController.php`)

```php
public function store(ServiceUpdateRequest $request)
{
    $service = Service::create([
        "client_id" => $request->client_id,
        "service_type" => $request->service_type,
        "special_service_title" => $request->special_service_title, // ✅ NUEVO
        "scheduled_date" => $request->scheduled_date,
        // ... resto de campos
    ]);
}
```

---

### 3. ServiceUpdateRequest (`app/Http/Requests/ServiceUpdateRequest.php`)

#### Validaciones Actualizadas

```php
public function rules(): array
{
    return [
        "client_id" => "required|exists:clients,id",
        "service_type" => "required|exists:service_types,slug",
        // ✅ NUEVO: Campo requerido solo si service_type es 'servicios-especiales'
        "special_service_title" => "required_if:service_type,servicios-especiales|nullable|string|max:255",
        "scheduled_date" => "required|date",
        // ... resto de validaciones
    ];
}

public function messages(): array
{
    return [
        // ... otros mensajes
        "special_service_title.required_if" => "El título del servicio especial es obligatorio.",
    ];
}
```

---

### 4. TechnicianController (`app/Http/Controllers/TechnicianController.php`)

#### Actualización del método getNextStage()

```php
private function getNextStage($currentStage, $serviceType)
{
    // ✅ NUEVO: Flujo especial para servicios especiales
    if ($serviceType === 'servicios-especiales') {
        $stageFlow = [
            'observations' => 'sites',
            'sites' => 'description',
            'description' => null // Final stage
        ];
        return $stageFlow[$currentStage] ?? null;
    }

    // Flujo especial para sanitización/desinfección
    if ($serviceType === 'sanitizacion' || $serviceType === 'desinfeccion') {
        $stageFlow = [
            'products' => 'observations',
            'observations' => 'sites',
            'sites' => 'description',
            'description' => null
        ];
        return $stageFlow[$currentStage] ?? null;
    }

    // Flujo estándar para otros tipos
    $stageFlow = [
        'points' => 'products',
        'products' => 'results',
        'results' => 'observations',
        'observations' => 'sites',
        'sites' => 'description',
        'description' => null
    ];
    return $stageFlow[$currentStage] ?? null;
}
```

---

## 🎨 CAMBIOS EN EL FRONTEND

### 1. Formulario de Creación (`resources/views/services/create.blade.php`)

#### Campo de Título Especial

```blade
<!-- Título del Servicio Especial (solo visible para servicios-especiales) -->
<div id="special-service-title-container" style="display: none;">
    <label for="special_service_title" class="block text-sm font-medium text-gray-700 mb-2">
        Título del Servicio Especial *
    </label>
    <input type="text" id="special_service_title" name="special_service_title"
           value="{{ old('special_service_title') }}"
           placeholder="Ej: Desinfección COVID-19, Fumigación de Bodega, etc."
           class="w-full border border-gray-300 rounded-lg px-3 py-2...">
    <p class="text-sm text-gray-500 mt-1">Este título aparecerá en el detalle y PDF del servicio</p>
</div>
```

#### JavaScript para mostrar/ocultar campo

```javascript
document.addEventListener('DOMContentLoaded', function() {
    const serviceTypeSelect = document.getElementById('service_type');
    const specialTitleContainer = document.getElementById('special-service-title-container');
    const specialTitleInput = document.getElementById('special_service_title');
    
    function toggleSpecialServiceTitle() {
        const selectedValue = serviceTypeSelect.value;
        
        if (selectedValue === 'servicios-especiales') {
            specialTitleContainer.style.display = 'block';
            specialTitleInput.setAttribute('required', 'required');
        } else {
            specialTitleContainer.style.display = 'none';
            specialTitleInput.removeAttribute('required');
            specialTitleInput.value = '';
        }
    }
    
    toggleSpecialServiceTitle();
    serviceTypeSelect.addEventListener('change', toggleSpecialServiceTitle);
});
```

**Comportamiento:**
- Campo oculto por defecto
- Se muestra solo cuando se selecciona "Servicios Especiales"
- Campo obligatorio cuando está visible
- Se limpia automáticamente al cambiar a otro tipo de servicio

---

### 2. Vista PDF (`resources/views/technician/service-pdf.blade.php`)

```blade
<div class="info-row">
    <span class="info-label">Tipo de Servicio:</span>
    <span class="info-value">{{ $service->serviceType->name ?? "N/A" }}</span>
</div>

<!-- ✅ NUEVO: Mostrar título del servicio especial -->
@if($service->service_type === 'servicios-especiales' && $service->special_service_title)
<div class="info-row">
    <span class="info-label">Título del Servicio:</span>
    <span class="info-value" style="font-weight: bold; color: #059669;">
        {{ $service->special_service_title }}
    </span>
</div>
@endif

<div class="info-row">
    <span class="info-label">Técnico Asignado:</span>
    <span class="info-value">{{ $service->assignedUser->name ?? "N/A" }}</span>
</div>
```

**Estilos del título:**
- `font-weight: bold` - Negrita para destacar
- `color: #059669` - Verde para diferenciarlo

---

### 3. Vista de Detalles (`resources/views/technician/service-checklist-details.blade.php`)

```blade
<h1 class="text-2xl font-bold text-gray-900">Detalles Completos del servicio</h1>
<p class="text-gray-600 mt-1">Servicio #{{ $service->id }} - {{ $service->client->name ?? 'Cliente' }}</p>
<p class="text-gray-600">Tipo de Servicio: <strong>{{ ucfirst($service->service_type) }}</strong></p>

<!-- ✅ NUEVO: Mostrar título del servicio especial -->
@if($service->service_type === 'servicios-especiales' && $service->special_service_title)
<p class="text-green-700 font-semibold text-lg mt-2">
    📋 {{ $service->special_service_title }}
</p>
@endif
```

**Estilos del título:**
- `text-green-700` - Color verde
- `font-semibold` - Texto semi-negrita
- `text-lg` - Tamaño grande
- `mt-2` - Margen superior
- Emoji 📋 para visual adicional

---

## 📊 FLUJOS DE TRABAJO

### Flujo Comparativo por Tipo de Servicio

#### 1. Desratización (Completo)
```
┌──────────┐
│  Points  │ → Check de puntos
└────┬─────┘
     │
     ▼
┌──────────┐
│ Products │ → Selección de productos
└────┬─────┘
     │
     ▼
┌──────────┐
│ Results  │ → Resultados observados
└────┬─────┘
     │
     ▼
┌──────────────┐
│ Observations │ → Observaciones
└──────┬───────┘
       │
       ▼
┌──────────┐
│  Sites   │ → Sitios tratados
└────┬─────┘
     │
     ▼
┌─────────────┐
│ Description │ → Descripción final
└─────────────┘
```

#### 2. Desinsectación
```
┌──────────┐
│ Products │ → Selección de productos
└────┬─────┘
     │
     ▼
┌──────────┐
│ Results  │ → Resultados observados
└────┬─────┘
     │
     ▼
┌──────────────┐
│ Observations │ → Observaciones
└──────┬───────┘
       │
       ▼
┌──────────┐
│  Sites   │ → Sitios tratados
└────┬─────┘
     │
     ▼
┌─────────────┐
│ Description │ → Descripción final
└─────────────┘
```

#### 3. Sanitización / Desinfección
```
┌──────────┐
│ Products │ → Selección de productos + Dosis/Agua
└────┬─────┘
     │
     ▼
┌──────────────┐
│ Observations │ → Observaciones (Salta Results)
└──────┬───────┘
       │
       ▼
┌──────────┐
│  Sites   │ → Sitios tratados
└────┬─────┘
     │
     ▼
┌─────────────┐
│ Description │ → Descripción final
└─────────────┘
```

#### 4. ✅ Servicios Especiales (NUEVO)
```
┌──────────────┐
│ Localización │ → Captura de GPS (antes del checklist)
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ Observations │ → Observaciones específicas del servicio
└──────┬───────┘
       │
       ▼
┌──────────┐
│  Sites   │ → Sitios del servicio especial
└────┬─────┘
     │
     ▼
┌─────────────┐
│ Description │ → Descripción final del trabajo
└─────────────┘
```

**Características del flujo:**
- ❌ NO tiene Points
- ❌ NO tiene Products
- ❌ NO tiene Results
- ✅ Comienza directo con Observations
- ✅ Solo 3 etapas de checklist
- ✅ Enfoque en observaciones del trabajo específico

---

## 🎯 CASOS DE USO

### Caso 1: Crear Servicio Especial

**Pasos:**

1. Admin va a `/admin/services/create`
2. Selecciona cliente
3. Selecciona "Servicios Especiales" en tipo de servicio
4. **Campo de título aparece automáticamente**
5. Ingresa título descriptivo: "Desinfección de Oficinas COVID-19"
6. Completa resto de datos
7. Guarda el servicio

**Validación:**
- Si no ingresa título → Error: "El título del servicio especial es obligatorio"
- Si ingresa título → Servicio creado exitosamente

---

### Caso 2: Técnico Realiza Servicio Especial

**Flujo del técnico:**

1. **Acepta el servicio**
   - Ve tipo: "Servicios Especiales"
   - Ve título: "Desinfección de Oficinas COVID-19"

2. **Captura ubicación**
   - Botón "Capturar Localización"
   - GPS se guarda automáticamente

3. **Inicia checklist** → Va directo a **Observations**
   - NO pasa por Points
   - NO pasa por Products
   - NO pasa por Results

4. **Observations**
   - Describe el trabajo realizado
   - Agrega fotos si es necesario
   - Click "Siguiente" → Va a **Sites**

5. **Sites**
   - Marca sitios tratados/trabajados
   - Click "Siguiente" → Va a **Description**

6. **Description**
   - Descripción final del servicio
   - Firma del técnico
   - Firma del cliente
   - **Finaliza servicio**

---

### Caso 3: Visualizar Detalles del Servicio

**En pantalla de detalles:**

```
Detalles Completos del servicio
Servicio #123 - Empresa ABC
Tipo de Servicio: Servicios Especiales
📋 Desinfección de Oficinas COVID-19    ← Título destacado
```

---

### Caso 4: Generar PDF

**En el PDF aparece:**

```
INFORMACIÓN DEL SERVICIO

Dirección: Av. Principal 123
Tipo de Servicio: Servicios Especiales
Título del Servicio: Desinfección de Oficinas COVID-19    ← En verde y negrita
Técnico Asignado: Juan Pérez
Fecha de Servicio: 07/10/2025 14:30
```

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

### Base de Datos
- [x] ✅ Migración creada
- [x] ✅ Campo `special_service_title` agregado
- [x] ✅ Migración ejecutada

### Modelo
- [x] ✅ Campo agregado a `fillable`
- [x] ✅ Método `getStages()` actualizado con flujo especial

### Controladores
- [x] ✅ `ServiceController` guarda el título
- [x] ✅ `TechnicianController` implementa flujo especial
- [x] ✅ Validación en `ServiceUpdateRequest`

### Vistas - Creación
- [x] ✅ Campo de título agregado al formulario
- [x] ✅ JavaScript para mostrar/ocultar campo
- [x] ✅ Validación del lado del cliente

### Vistas - Visualización
- [x] ✅ Título en detalles del servicio
- [x] ✅ Título en PDF del servicio
- [x] ✅ Estilos diferenciados (verde, negrita)

### Flujo del Técnico
- [x] ✅ Etapas correctas para servicios especiales
- [x] ✅ Sin Points, Products ni Results
- [x] ✅ Flujo: Observations → Sites → Description

---

## 🧪 PRUEBAS RECOMENDADAS

### Test 1: Validación del Formulario

```
1. Ir a crear servicio
2. Seleccionar "Servicios Especiales"
3. NO ingresar título
4. Intentar guardar
✅ Esperado: Error de validación
```

### Test 2: Campo Dinámico

```
1. Seleccionar "Servicios Especiales"
✅ Campo título visible y requerido

2. Cambiar a "Desratización"
✅ Campo título oculto y no requerido

3. Volver a "Servicios Especiales"
✅ Campo título visible nuevamente (vacío)
```

### Test 3: Flujo del Técnico

```
1. Crear servicio especial con título "Test Service"
2. Asignar a técnico
3. Técnico acepta servicio
4. Técnico captura ubicación
5. Técnico inicia checklist
✅ Debe ir directo a Observations
✅ NO debe mostrar Points, Products ni Results
```

### Test 4: Visualización en PDF

```
1. Completar servicio especial
2. Generar PDF
✅ Debe mostrar:
   - Tipo de Servicio: Servicios Especiales
   - Título del Servicio: [título ingresado]
   - Título en verde y negrita
```

---

## 📅 INFORMACIÓN DEL CAMBIO

- **Fecha:** 7 de Octubre de 2025
- **Archivos Creados:**
  - `database/migrations/2025_10_07_033338_add_special_service_title_to_services_table.php`

- **Archivos Modificados:**
  - `app/Models/Service.php`
  - `app/Http/Controllers/ServiceController.php`
  - `app/Http/Controllers/TechnicianController.php`
  - `app/Http/Requests/ServiceUpdateRequest.php`
  - `resources/views/services/create.blade.php`
  - `resources/views/technician/service-pdf.blade.php`
  - `resources/views/technician/service-checklist-details.blade.php`

- **Tipo de Cambio:** Feature - Nueva funcionalidad
- **Impacto:** Alto - Nuevo flujo de trabajo para servicios especiales
- **Breaking Changes:** ❌ Ninguno
- **Retrocompatibilidad:** ✅ Totalmente compatible

---

## 🎉 RESULTADO FINAL

### Antes
❌ Servicios especiales no tenían personalización  
❌ Mismo flujo genérico para todos  
❌ Sin identificación clara del propósito  

### Ahora
✅ Título personalizado para cada servicio especial  
✅ Flujo optimizado (solo 3 etapas)  
✅ Título visible en detalles y PDF  
✅ Validación automática del título  
✅ Campo dinámico que aparece/desaparece  
✅ Interfaz intuitiva y clara  

---

## 🚀 PRÓXIMOS PASOS

1. ✅ Probar creación de servicio especial
2. ✅ Verificar que campo aparezca/desaparezca
3. ✅ Validar que título sea obligatorio
4. ✅ Completar servicio como técnico
5. ✅ Verificar flujo simplificado
6. ✅ Revisar título en detalles
7. ✅ Generar PDF y verificar título

**¡IMPLEMENTACIÓN COMPLETA! 🎊**
