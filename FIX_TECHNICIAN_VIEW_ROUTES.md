# Fix: Route [technician-view.service.checklist.submit] not defined

## Problema

Al acceder a la URL `https://pestcontroller.grupodita.cl/admin/technician-view/services/88/checklist` se generaba el error:

```
Route [technician-view.service.checklist.submit] not defined
```

## Causa del Problema

Las rutas `technician-view.*` estaban definidas dentro del grupo de rutas de admin que tiene el prefijo `admin.`, lo que causaba que las rutas se registraran con nombres como:

- `admin.technician-view.service.checklist.submit` (nombre real registrado)

Mientras que en las vistas se estaba intentando acceder a:

- `technician-view.service.checklist.submit` (nombre sin el prefijo admin)

## Solución Implementada

Se movieron todas las rutas de `technician-view` **fuera** del grupo de rutas de admin y se creó un **nuevo grupo separado** específicamente para ellas:

### Antes (líneas 98-118 aproximadamente):

```php
Route::middleware(['auth', 'role:super-admin'])->prefix('admin')->name('admin.')->group(function () {
    // ... otras rutas de admin ...
    
    // Rutas de vista de técnico para super-admins
    Route::get('/technician-view/dashboard', ...)->name('technician-view.dashboard');
    // ... más rutas technician-view dentro del grupo admin ...
});
```

### Después (líneas 146-164):

```php
// Cierre del grupo de admin
});

// Nuevo grupo separado para rutas de vista de técnico
Route::middleware(['auth', 'role:super-admin'])->group(function () {
    Route::get('/admin/technician-view/dashboard', ...)->name('technician-view.dashboard');
    Route::get('/admin/technician-view/services', ...)->name('technician-view.services');
    // ... todas las rutas technician-view con el prefijo /admin/ en la URL pero sin el prefijo en el nombre
    Route::post('/admin/technician-view/services/{service}/checklist/submit', ...)->name('technician-view.service.checklist.submit');
});
```

## Rutas Afectadas

Todas las siguientes rutas ahora están correctamente registradas con sus nombres sin el prefijo `admin.`:

- `technician-view.dashboard`
- `technician-view.services`
- `technician-view.service.detail`
- `technician-view.service.pdf`
- `technician-view.service.checklist-details`
- `technician-view.service.profile`
- `technician-view.service.start`
- `technician-view.service.complete`
- `technician-view.service.checklist`
- `technician-view.service.checklist.stage`
- `technician-view.service.checklist.location`
- `technician-view.service.checklist.location.post`
- `technician-view.service.checklist.process-location`
- **`technician-view.service.checklist.submit`** ← Esta era la ruta que causaba el error

## Cambios en el Archivo

**Archivo modificado:** `routes/web.php`

**Líneas afectadas:**
- Líneas 98-118: Rutas removidas del grupo admin
- Líneas 146-164: Nuevo grupo creado para rutas technician-view

## Verificación

Las rutas ahora están correctamente registradas y accesibles mediante los nombres que se usan en las vistas. Las URLs siguen siendo las mismas (`/admin/technician-view/...`) pero los nombres de ruta ya no tienen el prefijo `admin.`.

## Notas Importantes

1. **No se requieren cambios en las vistas**: Las vistas ya estaban usando los nombres correctos (`technician-view.*`), por lo que no necesitan modificaciones.

2. **Las URLs mantienen el prefijo /admin/**: Aunque se removió el grupo con `->name('admin.')`, se agregó manualmente el prefijo `/admin/` en cada URL de ruta para mantener la estructura de URLs existente.

3. **Middleware aplicado**: Todas las rutas siguen protegidas con `['auth', 'role:super-admin']` para garantizar que solo los super-admins puedan acceder a la vista de técnico.

## Fecha de la Corrección

19 de noviembre de 2025
