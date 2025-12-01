# 📋 Guía: Completar Servicio para Probar el Sistema

## ✅ Servicio Creado

- **ID**: 128
- **Cliente**: Venta Renta
- **Fecha**: 2025-12-01
- **Estado**: `checklist_data` vacío (normal, recién creado)

## 🎯 Pasos para Completar el Servicio

### **PASO 1: Iniciar el Servicio**

1. Ir a la lista de servicios del técnico
2. Buscar el servicio ID 128 (Venta Renta)
3. Hacer clic en "Iniciar Servicio"

### **PASO 2: Completar Monitoreo de Datos**

1. Ir a la etapa "Monitoreo - Datos"
2. Llenar los campos necesarios
3. Guardar

### **PASO 3: Subir Croquis** ⭐ IMPORTANTE

1. Ir a la etapa "Monitoreo - Croquis"
2. **Subir una imagen REAL** (no de prueba, > 1KB)
   - Puede ser una foto del celular
   - Formato: JPG, PNG
   - Tamaño: entre 100KB y 5MB
3. Agregar notas (opcional)
4. Guardar

**Verificar**: El formulario ahora tiene `enctype="multipart/form-data"` ✅

### **PASO 4: Completar Monitoreo de Cebaderas** ⭐ IMPORTANTE

1. Ir a la etapa "Monitoreo - Completo"
2. Agregar al menos 1 cebadera:
   - Código: CB-001
   - Ubicación: Cocina
   - Consumo: 15%
   - Capturas: 2
   - **Subir FOTO REAL** (no de prueba, > 1KB)
3. Guardar

**Verificar**: Las fotos deben ser > 1KB (no 70 bytes)

### **PASO 5: Completar Estadísticas** ⭐ CRÍTICO

1. Ir a la etapa "Monitoreo - Estadísticas"
2. Los datos se calculan automáticamente
3. Revisar y guardar

**Esto guardará `historical_data` automáticamente** ✅

### **PASO 6: Completar Análisis y Firma**

1. Completar "Monitoreo - Análisis"
2. Completar "Monitoreo - Firma"
3. Finalizar servicio

### **PASO 7: Generar PDF**

1. Ir al detalle del servicio
2. Hacer clic en "Generar PDF"
3. Verificar que se vea:
   - ✅ Gráfico con línea roja (datos del día actual)
   - ✅ Imagen del croquis
   - ✅ Imágenes de cebaderas

## 🔍 Verificar Después de Cada Paso

Después de completar cada etapa, ejecuta:

```bash
php verify-db.php
```

### **Después del Paso 3 (Croquis):**
```
2. CROQUIS:
   ✅ Existe
   Archivo: storage/services/croquis/XXXXX.png
   ✅ Archivo existe (> 50 KB)  ← Debe ser > 1KB
```

### **Después del Paso 4 (Cebaderas):**
```
1. MONITOREO COMPLETO:
   ✅ Existe
   Cebaderas: 1
   - Consumo: 15%
   - Capturas: 2
   - Fotos: 1
   ✅ Archivo existe (> 50 KB)  ← Debe ser > 1KB
```

### **Después del Paso 5 (Estadísticas):**
```
3. ESTADÍSTICAS:
   ✅ Existe
   Total monitoreadas: 1
   Consumo promedio: 15%
   ✅ Historical data existe
   Días: 7
   Últimos 3 días:
     - 2025-11-29: 0% consumo, 0 capturas
     - 2025-11-30: 0% consumo, 0 capturas
     - 2025-12-01: 15% consumo, 2 capturas  ← Día actual con datos
```

## ⚠️ Problemas Comunes

### **1. Imágenes muy pequeñas (< 1KB)**
- ❌ Problema: Archivos de prueba corruptos
- ✅ Solución: Usar fotos reales del celular

### **2. Croquis no se sube**
- ❌ Problema: Formulario sin `enctype`
- ✅ Solución: Ya corregido, verificar con `grep enctype`

### **3. Historical data no existe**
- ❌ Problema: No se completó la etapa de estadísticas
- ✅ Solución: Completar "Monitoreo - Estadísticas"

### **4. Gráfico vacío en PDF**
- ❌ Problema: `historical_data` no existe en BD
- ✅ Solución: Completar estadísticas primero

## 📊 Resultado Final Esperado

### **En la Base de Datos:**
```json
{
  "monitoreo_completo": {
    "bait_stations": [
      {
        "code": "CB-001",
        "consumption": 15,
        "captures": 2,
        "photos": ["storage/services/bait-stations/XXXXX.png"]
      }
    ]
  },
  "monitoreo_croquis": {
    "croquis_file": "storage/services/croquis/XXXXX.png",
    "croquis_notes": "..."
  },
  "monitoreo_estadisticas": {
    "total_monitored": 1,
    "average_consumption_percent": 15,
    "historical_data": [
      {"date": "2025-11-25", "consumption_percent": 0, "captures": 0},
      {"date": "2025-11-26", "consumption_percent": 0, "captures": 0},
      {"date": "2025-11-27", "consumption_percent": 0, "captures": 0},
      {"date": "2025-11-28", "consumption_percent": 0, "captures": 0},
      {"date": "2025-11-29", "consumption_percent": 0, "captures": 0},
      {"date": "2025-11-30", "consumption_percent": 0, "captures": 0},
      {"date": "2025-12-01", "consumption_percent": 15, "captures": 2}
    ]
  }
}
```

### **En el PDF:**
- ✅ Gráfico con línea roja subiendo en el último día
- ✅ Línea gris con 2 capturas en el último día
- ✅ Grid horizontal con valores 0-20
- ✅ Fechas del 25/11 al 01/12
- ✅ Imagen del croquis visible
- ✅ Imagen de la cebadera CB-001 visible

## 🎯 Comando Rápido de Verificación

```bash
# Verificar datos en BD
php verify-db.php

# Ver logs recientes
tail -50 storage/logs/laravel.log | grep -E "Estadísticas|Croquis|Bait"

# Ver archivos guardados
ls -lh storage/app/public/services/croquis/ | tail -5
ls -lh storage/app/public/services/bait-stations/ | tail -5
```

## ✅ Checklist de Completado

- [ ] Servicio iniciado
- [ ] Monitoreo de datos completado
- [ ] **Croquis subido (imagen real > 1KB)**
- [ ] **Cebaderas con fotos (imágenes reales > 1KB)**
- [ ] **Estadísticas completadas** (genera historical_data)
- [ ] Análisis completado
- [ ] Firma completada
- [ ] PDF generado
- [ ] Verificado en BD con `php verify-db.php`
- [ ] Gráfico visible en PDF
- [ ] Imágenes visibles en PDF

---

**IMPORTANTE**: Usa **imágenes reales** (fotos del celular), no archivos de prueba pequeños.
