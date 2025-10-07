# 💧 Implementación: Dosis y Agua para Sanitización

## 📋 Requerimiento

Cuando el servicio es de tipo **sanitización**, al seleccionar un producto se deben solicitar:
- **Dosis aplicada** (en cc)
- **Agua aplicada** (en litros)

Estos datos deben:
1. ✅ Guardarse junto con el producto seleccionado
2. ✅ Mostrarse en el PDF generado

---

## ✅ Cambios Implementados

### 1. Vista de Productos (`products.blade.php`)

**Antes:** Solo se mostraban los campos para `desinfeccion`

**Ahora:** Se muestran para `desinfeccion` Y `sanitizacion`

```blade
@if (in_array($service->service_type, ['desinfeccion', 'sanitizacion']))
    <div class="mb-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">
            Dosis y agua aplicadas
        </h2>

        <div class="flex gap-4 mb-2">
            <div class="flex-1" style="padding-top: 12px;">
                <div class="flex gap-2">
                    <input type="number"
                           name="dosis"
                           value="{{ old('dosis', $service->checklist_data['products']['dosis'] ?? '') }}"
                           placeholder="Ej: 500"
                           step="0.01"
                           class="form-control">
                    <span>cc</span>/
                    <input type="number"
                           name="agua"
                           value="{{ old('agua', $service->checklist_data['products']['agua'] ?? '') }}"
                           placeholder="Ej: 10"
                           step="0.01"
                           class="form-control">
                    <span>Litros de agua</span>
                </div>
            </div>
        </div>
    </div>
@endif
```

**Cambios clave:**
- ✅ Cambió de `$service->service_type === 'desinfeccion'` a `in_array($service->service_type, ['desinfeccion', 'sanitizacion'])`
- ✅ Los valores ahora se guardan en `$service->checklist_data['products']['dosis']` y `['products']['agua']`
- ✅ Agregado `step="0.01"` para permitir decimales
- ✅ Corregido "Tenientes agua" → "Litros de agua"

---

### 2. Controlador (`TechnicianController.php`)

**Método modificado:** `processProductsData()`

**Antes:** Solo guardaba producto, product_id y quantity

**Ahora:** También guarda dosis y agua

```php
private function processProductsData(Request $request)
{
    $data = [];
    
    // Capturar el producto seleccionado del radio button
    if ($request->has('applied_product')) {
        $data['applied_product'] = $request->input('applied_product');
    }
    
    // Capturar el ID del producto si está disponible
    if ($request->has('product_id')) {
        $data['product_id'] = $request->input('product_id');
    }
    
    // Capturar cantidad si está disponible
    if ($request->has('quantity')) {
        $data['quantity'] = $request->input('quantity');
    }
    
    // ✅ NUEVO: Capturar dosis y agua para desinfección y sanitización
    if ($request->has('dosis')) {
        $data['dosis'] = $request->input('dosis');
    }
    
    if ($request->has('agua')) {
        $data['agua'] = $request->input('agua');
    }
    
    $data['applied_at'] = now()->format('Y-m-d H:i:s');
    
    return $data;
}
```

**Resultado en la BD:**
```json
{
    "products": {
        "applied_product": "VIREX TB (Quaternary ammonium compounds) (Reg. ISP: F-XIX/52-14)",
        "product_id": 23,
        "dosis": "500",
        "agua": "10",
        "applied_at": "2025-10-06 15:30:00"
    }
}
```

---

### 3. PDF (`service-pdf.blade.php`)

**Antes:** Solo mostraba el nombre del producto

**Ahora:** Muestra producto + dosis + agua (cuando aplique)

```blade
{{-- Insumos Utilizados (Producto + Lote) --}}
@if($service->checklist_data && isset($service->checklist_data["products"]["applied_product"]))
<div class="section">
    <div class="section-title">Insumos Utilizados</div>
    <div class="product-info">
        <strong>Producto:</strong> {{ $service->checklist_data["products"]["applied_product"] }}
        
        {{-- ✅ NUEVO: Mostrar dosis y agua para desinfección y sanitización --}}
        @if(in_array($service->service_type, ['desinfeccion', 'sanitizacion']))
            @if(isset($service->checklist_data["products"]["dosis"]) || isset($service->checklist_data["products"]["agua"]))
                <br><br>
                <div style="margin-top: 8px; padding: 10px; background-color: #f0f9f0; border-left: 3px solid #2c5530;">
                    @if(isset($service->checklist_data["products"]["dosis"]))
                        <strong>Dosis aplicada:</strong> {{ $service->checklist_data["products"]["dosis"] }} cc
                    @endif
                    @if(isset($service->checklist_data["products"]["agua"]))
                        <br><strong>Agua aplicada:</strong> {{ $service->checklist_data["products"]["agua"] }} litros
                    @endif
                </div>
            @endif
        @endif
    </div>
@else
    <div class="section">
        <div class="section-title">Insumos Utilizados</div>
        <div class="product-info">No hay productos aplicados registrados</div>
    </div>
@endif
```

**Vista en el PDF:**

```
╔════════════════════════════════════════════════════════╗
║                  Insumos Utilizados                    ║
╠════════════════════════════════════════════════════════╣
║ Producto: VIREX TB (Quaternary ammonium compounds)    ║
║           (Reg. ISP: F-XIX/52-14)                      ║
║                                                        ║
║ ┌────────────────────────────────────────────────┐   ║
║ │ Dosis aplicada: 500 cc                         │   ║
║ │ Agua aplicada: 10 litros                       │   ║
║ └────────────────────────────────────────────────┘   ║
╚════════════════════════════════════════════════════════╝
```

---

## 🧪 Pruebas de Verificación

### Prueba 1: Servicio de Sanitización con Dosis y Agua

1. **Crear/Editar servicio de sanitización:**
   ```
   - Tipo: sanitizacion
   - Cliente: [cualquiera]
   - Técnico: [asignar]
   ```

2. **Completar checklist:**
   - Etapa Productos:
     - Seleccionar producto: ej. "VIREX TB"
     - Dosis: 500
     - Agua: 10
     - Siguiente

3. **Verificar en BD:**
   ```bash
   php artisan tinker
   $s = Service::find([id]);
   dd($s->checklist_data['products']);
   ```
   
   **Resultado esperado:**
   ```php
   [
     "applied_product" => "VIREX TB (Quaternary ammonium compounds) (Reg. ISP: F-XIX/52-14)",
     "product_id" => 23,
     "dosis" => "500",
     "agua" => "10",
     "applied_at" => "2025-10-06 15:30:00"
   ]
   ```

4. **Generar PDF:**
   - Completar todas las etapas
   - Finalizar servicio
   - Generar PDF
   
   **Verificar:**
   - ✅ Aparece el nombre del producto
   - ✅ Aparece "Dosis aplicada: 500 cc"
   - ✅ Aparece "Agua aplicada: 10 litros"
   - ✅ Caja con fondo verde claro

---

### Prueba 2: Servicio de Desinfección (Ya Existente)

Verificar que sigue funcionando como antes:

1. **Servicio de desinfección:**
   - Debe mostrar los mismos campos
   - Debe guardar dosis y agua
   - Debe aparecer en el PDF

---

### Prueba 3: Otros Tipos de Servicio

Verificar que NO muestren los campos:

1. **Desratización:**
   - ❌ NO debe mostrar campos de dosis/agua
   - ✅ Solo debe mostrar producto

2. **Desinsectación:**
   - ❌ NO debe mostrar campos de dosis/agua
   - ✅ Solo debe mostrar producto

3. **Verificar PDF de estos servicios:**
   - ✅ Solo debe aparecer el nombre del producto
   - ❌ NO debe aparecer la caja de dosis/agua

---

## 📊 Estructura de Datos

### En la Base de Datos

**Tabla:** `services`  
**Campo:** `checklist_data` (JSON)

```json
{
    "products": {
        "applied_product": "VIREX TB (Quaternary ammonium compounds) (Reg. ISP: F-XIX/52-14)",
        "product_id": 23,
        "dosis": "500",
        "agua": "10",
        "applied_at": "2025-10-06 15:30:00"
    },
    "results": { ... },
    "observations": [ ... ],
    ...
}
```

---

## 🎯 Tipos de Servicio Afectados

| Tipo de Servicio | Muestra Dosis/Agua | Guarda Dosis/Agua | Aparece en PDF |
|------------------|-------------------|-------------------|----------------|
| Desratización | ❌ No | ❌ No | ❌ No |
| Desinsectación | ❌ No | ❌ No | ❌ No |
| **Desinfección** | ✅ Sí | ✅ Sí | ✅ Sí |
| **Sanitización** | ✅ Sí | ✅ Sí | ✅ Sí |
| Fumigación de Jardines | ❌ No | ❌ No | ❌ No |
| Servicios Especiales | ❌ No | ❌ No | ❌ No |

---

## 📝 Archivos Modificados

| Archivo | Líneas | Cambios |
|---------|--------|---------|
| `resources/views/technician/checklist-stages/products.blade.php` | 69 | Cambió condición de `===` a `in_array()` para incluir sanitización |
| `resources/views/technician/checklist-stages/products.blade.php` | 83, 91 | Actualizó path de datos a `['products']['dosis']` y `['products']['agua']` |
| `resources/views/technician/checklist-stages/products.blade.php` | 85, 93 | Agregado `step="0.01"` para decimales |
| `app/Http/Controllers/TechnicianController.php` | 458-465 | Agregado captura de dosis y agua en `processProductsData()` |
| `resources/views/technician/service-pdf.blade.php` | 448-466 | Agregado renderizado condicional de dosis y agua en PDF |

---

## 🔧 Detalles Técnicos

### Validación de Campos

Los campos `dosis` y `agua` son **opcionales**. Si el usuario no los llena:
- Se guardan como `null` o no se incluyen en el JSON
- No aparecen en el PDF

### Formato de Números

- Tipo: `number`
- Step: `0.01` (permite decimales)
- Placeholder: "Ej: 500" y "Ej: 10"

### Estilo en PDF

```css
background-color: #f0f9f0;  /* Verde claro */
border-left: 3px solid #2c5530;  /* Verde oscuro */
padding: 10px;
```

---

## ✅ Checklist de Validación

Después de implementar, verificar:

- [ ] Los campos aparecen solo para desinfección y sanitización
- [ ] Los campos NO aparecen para otros tipos de servicio
- [ ] Los valores se guardan correctamente en `checklist_data['products']`
- [ ] Los valores persisten al editar el checklist
- [ ] Los valores aparecen en el PDF cuando existen
- [ ] El PDF NO muestra la caja si no hay dosis/agua
- [ ] Se permiten valores decimales (ej: 12.5)
- [ ] Los placeholders son claros y útiles
- [ ] Las unidades están correctas (cc y litros)

---

## 📅 Información del Cambio

- **Fecha:** 6 de Octubre de 2025
- **Tipo de Cambio:** Nueva funcionalidad
- **Ámbito:** Checklist de productos + PDF
- **Tipos de Servicio Afectados:** Desinfección, Sanitización
- **Impacto:** Medio - Mejora la captura de datos técnicos
- **Retrocompatibilidad:** ✅ Compatible con servicios existentes
- **Breaking Changes:** ❌ Ninguno

---

## 🎉 IMPLEMENTACIÓN COMPLETADA

✅ **Campos agregados** para desinfección y sanitización  
✅ **Datos guardados** en la estructura correcta  
✅ **PDF actualizado** para mostrar dosis y agua  
✅ **Retrocompatible** con servicios existentes  
✅ **Validaciones** opcionales implementadas

**Próximo paso:** Probar creando un servicio de sanitización completo y verificar que todo funcione correctamente.
