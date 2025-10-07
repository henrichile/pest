# 🔄 Flujo Especial para Sanitización

## 📋 Requerimiento

Para servicios de tipo **sanitización**, el flujo del checklist debe ser diferente:
- ❌ **NO** debe mostrar la etapa de **results** (resultados)
- ✅ Debe ir directamente de **products** → **observations**

---

## 🔀 Comparación de Flujos

### Flujo Estándar (Otros Servicios)

```
┌─────────┐    ┌──────────┐    ┌─────────┐    ┌──────────────┐    ┌───────┐    ┌─────────────┐
│ Points  │ → │ Products │ → │ Results │ → │ Observations │ → │ Sites │ → │ Description │
└─────────┘    └──────────┘    └─────────┘    └──────────────┘    └───────┘    └─────────────┘
```

**Ejemplo:** Desratización, Desinsectación, Desinfección, Fumigación, Servicios Especiales

---

### Flujo para Sanitización ✨

```
┌──────────┐    ┌──────────────┐    ┌───────┐    ┌─────────────┐
│ Products │ → │ Observations │ → │ Sites │ → │ Description │
└──────────┘    └──────────────┘    └───────┘    └─────────────┘
```

**Se omite:** Etapa de Results (Resultados)

---

## ✅ Cambios Implementados

### 1. Controlador: `TechnicianController.php`

**Método modificado:** `getNextStage()`

**Antes:** Flujo único para todos los servicios

**Ahora:** Flujo especial para sanitización

```php
private function getNextStage($currentStage, $serviceType)
{
    // Flujo especial para sanitización: products → observations (saltarse results)
    if ($serviceType === 'sanitizacion') {
        $stageFlow = [
            'products' => 'observations',
            'observations' => 'sites',
            'sites' => 'description',
            'description' => null // Final stage
        ];
        
        return $stageFlow[$currentStage] ?? null;
    }
    
    // Flujo estándar para otros tipos de servicio
    $stageFlow = [
        'points' => 'products',
        'products' => 'results',
        'results' => 'observations',
        'observations' => 'sites',
        'sites' => 'description',
        'description' => null // Final stage
    ];

    return $stageFlow[$currentStage] ?? null;
}
```

**Cambios clave:**
- ✅ Detecta si `$serviceType === 'sanitizacion'`
- ✅ Usa un flujo diferente donde `products → observations` directamente
- ✅ Mantiene el flujo estándar para otros tipos de servicio

---

### 2. Vista: `products.blade.php`

**Campo modificado:** `next_stage` (hidden input)

**Antes:** Siempre iba a `results`

```blade
<input type="hidden" name="next_stage" value="results">
```

**Ahora:** Dinámico según el tipo de servicio

```blade
<input type="hidden" name="next_stage" value="{{ $service->service_type === 'sanitizacion' ? 'observations' : 'results' }}">
```

**Lógica:**
- Si es **sanitización** → `observations`
- Si es **otro tipo** → `results`

---

## 🧪 Pruebas de Verificación

### Prueba 1: Servicio de Sanitización

1. **Crear servicio de sanitización:**
   ```
   - Tipo: sanitizacion
   - Cliente: [cualquiera]
   - Técnico: [asignar]
   - Estado: en_progreso
   ```

2. **Completar checklist:**
   - ✅ Etapa Products: Seleccionar producto + dosis + agua
   - ✅ Hacer clic en "Siguiente"
   - ✅ **Verificar:** Debe ir directamente a **Observations**
   - ❌ **NO debe mostrar:** Etapa Results

3. **Continuar checklist:**
   - ✅ Observations → Sites → Description → Finalizar

4. **Verificar en logs:**
   ```bash
   tail -f storage/logs/laravel.log | grep "getNextStage"
   ```

---

### Prueba 2: Otros Servicios (Desratización, etc.)

1. **Crear servicio de desratización:**
   ```
   - Tipo: desratizacion
   - Cliente: [cualquiera]
   - Técnico: [asignar]
   ```

2. **Completar checklist:**
   - ✅ Points → Products → Results → Observations → Sites → Description
   - ✅ **Verificar:** Debe mostrar TODAS las etapas normalmente
   - ✅ **Results debe aparecer** después de Products

---

### Prueba 3: Navegación hacia atrás

1. **En servicio de sanitización:**
   - Ir a Observations
   - Hacer clic en "Anterior"
   - ✅ **Debe volver a Products**
   - ❌ **NO debe pasar por Results**

---

## 📊 Tabla de Flujos por Tipo de Servicio

| Tipo de Servicio | Points | Products | Results | Observations | Sites | Description |
|------------------|--------|----------|---------|--------------|-------|-------------|
| Desratización | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Desinsectación | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Desinfección | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Sanitización** | ❌ | ✅ | ❌ | ✅ | ✅ | ✅ |
| Fumigación Jardines | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Servicios Especiales | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |

**Leyenda:**
- ✅ = Etapa incluida en el flujo
- ❌ = Etapa omitida

---

## 🎯 Razón del Cambio

### ¿Por qué sanitización no necesita "Results"?

La etapa **Results** típicamente incluye:
- Evaluación de eficacia
- Condiciones de plagas
- Niveles de infestación
- Áreas afectadas
- Resultados cuantitativos

Para **sanitización**, el proceso es más simple:
- Se aplica el producto desinfectante
- Se registra dosis y agua aplicada
- Se documentan observaciones generales
- **No hay "resultados" que medir** como en control de plagas

---

## 📝 Archivos Modificados

| Archivo | Líneas | Cambios |
|---------|--------|---------|
| `app/Http/Controllers/TechnicianController.php` | 607-632 | Modificado `getNextStage()` para detectar sanitización |
| `resources/views/technician/checklist-stages/products.blade.php` | 13 | Cambio de `next_stage` a dinámico |

---

## 🔧 Detalles Técnicos

### Detección del Tipo de Servicio

El tipo de servicio se obtiene de:
```php
$service->service_type
```

**Valores posibles:**
- `'desratizacion'`
- `'desinsectacion'`
- `'desinfeccion'`
- `'sanitizacion'` ← Este activa el flujo especial
- `'fumigacion-de-jardines'`
- `'servicios-especiales'`

---

### Próxima Etapa Dinámica

El método `getNextStage()` es llamado en varios lugares:

1. **Al mostrar una etapa:**
   ```php
   $nextStage = $this->getNextStage($service->checklist_stage, $service->service_type);
   ```

2. **Al procesar el submit:**
   ```php
   $nextStage = ($request->input('next_stage') ?? 
                 $request->input('data_next_stage') ?? 
                 $this->getNextStage($stage, $service->service_type));
   ```

El hidden input en la vista asegura que el valor correcto se envíe en el formulario.

---

## ✅ Checklist de Validación

Después de implementar, verificar:

- [ ] Sanitización: Products → Observations (sin Results)
- [ ] Sanitización: Observations → Sites funciona correctamente
- [ ] Sanitización: NO se puede acceder manualmente a `/results`
- [ ] Otros servicios: Products → Results → Observations (flujo normal)
- [ ] Botón "Anterior" funciona correctamente en ambos flujos
- [ ] El progreso del checklist se guarda correctamente
- [ ] No hay errores 404 o 500 durante la navegación
- [ ] El PDF final se genera correctamente (sin sección Results para sanitización)

---

## 🐛 Posibles Problemas y Soluciones

### Problema 1: "Stage 'results' no encontrado" para sanitización

**Causa:** El servicio intenta acceder a la etapa results

**Solución:** Verificar que `getNextStage()` retorna `observations` para sanitización

```bash
# Verificar en tinker
php artisan tinker
>>> $controller = new App\Http\Controllers\TechnicianController();
>>> $controller->getNextStage('products', 'sanitizacion')
=> "observations"  // ✅ Correcto
```

---

### Problema 2: Botón "Siguiente" lleva a Results en sanitización

**Causa:** El hidden input `next_stage` está hardcodeado a `results`

**Solución:** Ya corregido con la lógica ternaria:
```blade
value="{{ $service->service_type === 'sanitizacion' ? 'observations' : 'results' }}"
```

---

### Problema 3: Acceso directo a URL `/results` en sanitización

**Causa:** El usuario puede intentar acceder manualmente

**Solución:** El método `showChecklistStage()` debe validar el acceso:

```php
// En showChecklistStage()
if ($serviceType === 'sanitizacion' && $stage === 'results') {
    abort(403, 'Esta etapa no está disponible para servicios de sanitización');
}
```

**Nota:** Esta validación podría agregarse si es necesario.

---

## 📅 Información del Cambio

- **Fecha:** 6 de Octubre de 2025
- **Tipo de Cambio:** Modificación de flujo de negocio
- **Ámbito:** Checklist de servicios
- **Tipo de Servicio Afectado:** Sanitización
- **Impacto:** Medio - Cambia el flujo de navegación
- **Retrocompatibilidad:** ✅ Compatible con servicios existentes
- **Breaking Changes:** ❌ Ninguno (otros servicios no se ven afectados)

---

## 🎉 IMPLEMENTACIÓN COMPLETADA

✅ **Flujo especial** para sanitización implementado  
✅ **Products → Observations** (sin Results)  
✅ **Flujo estándar** preservado para otros servicios  
✅ **Hidden input dinámico** en la vista  
✅ **Método getNextStage()** actualizado  

**Próximo paso:** Crear un servicio de sanitización de prueba y verificar que el flujo funcione correctamente omitiendo la etapa Results.

---

## 🔄 Resumen Visual

```
ANTES (Todos los servicios):
Products → Results → Observations → Sites → Description

DESPUÉS:

Sanitización:
Products → Observations → Sites → Description
           ↑
           └── Saltea Results ✨

Otros servicios:
Products → Results → Observations → Sites → Description
           ↑
           └── Mantiene Results ✅
```
