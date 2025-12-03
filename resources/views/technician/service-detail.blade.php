@extends("layouts.app")

@section("content")
@php
    // PRIORIDAD 1: Verificar sesión PRIMERO (más confiable)
    $isTechnicianViewMode = false;
    if (auth()->check() && auth()->user()->hasRole('super-admin')) {
        $viewAsTechnician = session('view_as_technician', false);
        // También verificar en request()->session() por si acaso
        if (!$viewAsTechnician && request()->hasSession()) {
            $viewAsTechnician = request()->session()->get('view_as_technician', false);
        }
        if ($viewAsTechnician) {
            $isTechnicianViewMode = true;
        }
    }

    // PRIORIDAD 2: Verificar URL actual
    if (!$isTechnicianViewMode) {
        if (request()->is('admin/technician-view/*') || request()->routeIs('technician-view.*')) {
            $isTechnicianViewMode = true;
        }
    }

    // PRIORIDAD 3: Verificar ruta actual por nombre
    if (!$isTechnicianViewMode) {
        try {
            $routeName = request()->route()->getName();
            if ($routeName && (strpos($routeName, 'technician-view') !== false || strpos($routeName, 'admin.technician-view') !== false)) {
                $isTechnicianViewMode = true;
            }
        } catch (\Exception $e) {
            // Continuar
        }
    }

    // PRIORIDAD 4: Verificar HTTP_REFERER
    if (!$isTechnicianViewMode && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/admin/technician-view/') !== false) {
        $isTechnicianViewMode = true;
    }

    // PRIORIDAD 5: Usar variable del controlador si está disponible
    if (!$isTechnicianViewMode && isset($isTechnicianView) && $isTechnicianView) {
        $isTechnicianViewMode = true;
    }

    // Log para debug (solo en desarrollo)
    if (config('app.debug')) {
        \Log::info('Service Detail - Technician View Detection', [
            'isTechnicianViewMode' => $isTechnicianViewMode,
            'session_view_as_technician' => session('view_as_technician', false),
            'request_session_view_as_technician' => request()->hasSession() ? request()->session()->get('view_as_technician', false) : 'no_session',
            'current_url' => request()->url(),
            'current_path' => request()->path(),
            'route_name' => request()->route() ? request()->route()->getName() : 'no_route',
            'is_super_admin' => auth()->check() ? auth()->user()->hasRole('super-admin') : false,
        ]);
    }
@endphp
@push('scripts')
<script>
    // Agregar atributo al body para que JavaScript pueda detectarlo
    document.addEventListener('DOMContentLoaded', function() {
        @if($isTechnicianViewMode)
        document.body.setAttribute('data-technician-view', 'true');
        document.body.classList.add('technician-view-mode');
        console.log('✅ Modo technician-view detectado - atributo agregado al body');
        @else
        console.log('⚠️ Modo technician-view NO detectado');
        @endif
    });
</script>
@endpush
<!-- View Updated: {{ now() }} -->
<div class="max-w-4xl mx-auto pt-3 md:pt-0">
    <!-- Header con hamburguesa y título (Móvil) -->
    <div class="mb-4 md:hidden px-4" style="padding-top: 2.5rem;">
        <div class="flex items-center gap-3">
            <!-- Hamburguesa -->
            <button id="page-mobile-menu-button" class="flex-shrink-0 p-2 rounded-lg bg-white border border-gray-300 shadow-md hover:bg-gray-50 transition-colors cursor-pointer relative" style="z-index: 100;">
                <svg id="page-menu-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="color: #111827;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg id="page-close-icon" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="color: #111827;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <!-- Título -->
            <div class="flex-1">
                <h2 class="text-xl font-bold" style="color: #111827; font-weight: 700;">
                    Detalle del Servicio
                </h2>
            </div>

            <!-- Iconos Header Móvil -->
            <div class="flex items-center gap-4">
                <!-- Notificaciones -->
                <a href="{{ route('admin.notification-center') ?? '#' }}" class="text-gray-500 hover:text-gray-700 relative">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    @php
                        $unreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
                    @endphp
                    @if($unreadCount > 0)
                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white transform translate-x-1/4 -translate-y-1/4"></span>
                    @endif
                </a>

                <!-- Perfil -->
                <a href="{{ Route::has('admin.profile') ? route('admin.profile') : (Route::has('profile') ? route('profile') : '#') }}" class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-green-600 flex items-center justify-center shadow-sm flex-shrink-0">
                        <span class="text-white font-medium text-base">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
            <h1 class="text-2xl font-bold text-white" style="color: white">Detalle del Servicio</h1>
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Información del Cliente -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Información del Cliente</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Nombre</p>
                        <p class="font-medium text-gray-900">{{ $service->client->name ?? "N/A" }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Teléfono</p>
                        <p class="font-medium text-gray-900">{{ $service->client->phone ?? "N/A" }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Email</p>
                        <p class="font-medium text-gray-900">{{ $service->client->email ?? "N/A" }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Dirección</p>
                        <p class="font-medium text-gray-900">{{ $service->address ?? "N/A" }}</p>
                    </div>
                </div>
            </div>

            <!-- Ubicación del Servicio (Mapa) -->
            @if($service->hasCoordinates())
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Ubicación del Servicio</h2>
                <div class="bg-gray-100 rounded-lg overflow-hidden border border-gray-200 relative" style="height: 300px;">
                    <div id="mapbox-map" style="width: 100%; height: 100%;"></div>
                    <!-- Fallback si no carga el mapa interactivo -->
                    <div id="map-fallback" class="hidden absolute inset-0 flex items-center justify-center bg-gray-100">
                        <div class="text-center">
                            <p class="text-gray-500 mb-2">Mapa interactivo no disponible</p>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $service->latitude }},{{ $service->longitude }}" target="_blank" class="text-blue-600 hover:underline">
                                Ver en Google Maps
                            </a>
                        </div>
                    </div>
                </div>
                <div class="mt-2 text-sm text-gray-600 flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>{{ number_format($service->latitude, 6) }}, {{ number_format($service->longitude, 6) }}</span>
                        @if($service->location_accuracy)
                            <span class="ml-2 text-gray-500">(Precisión: {{ $service->location_accuracy }}m)</span>
                        @endif
                    </div>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $service->latitude }},{{ $service->longitude }}" target="_blank" class="text-blue-600 hover:text-blue-800 flex items-center">
                        <span>Abrir en Google Maps</span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
            </div>

            @push('styles')
            <link href='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css' rel='stylesheet' />
            <style>
                .mapboxgl-popup {
                    max-width: 200px;
                }
                .mapboxgl-popup-content {
                    text-align: center;
                    font-family: 'Open Sans', sans-serif;
                }
            </style>
            @endpush

            @push('scripts')
            <script src='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js'></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const accessToken = '{{ config('services.mapbox.access_token') ?: env('MAPBOX_ACCESS_TOKEN') }}';
                    
                    if (!accessToken) {
                        console.error('Mapbox Access Token no configurado');
                        document.getElementById('mapbox-map').style.display = 'none';
                        document.getElementById('map-fallback').classList.remove('hidden');
                        return;
                    }

                    try {
                        mapboxgl.accessToken = accessToken;
                        const map = new mapboxgl.Map({
                            container: 'mapbox-map',
                            style: 'mapbox://styles/mapbox/streets-v11',
                            center: [{{ $service->longitude }}, {{ $service->latitude }}],
                            zoom: 15
                        });

                        // Agregar controles de navegación
                        map.addControl(new mapboxgl.NavigationControl());

                        // Agregar marcador
                        new mapboxgl.Marker({ color: '#ef4444' }) // red-500
                            .setLngLat([{{ $service->longitude }}, {{ $service->latitude }}])
                            .setPopup(new mapboxgl.Popup({ offset: 25 })
                                .setHTML('<strong>{{ $service->client->name }}</strong><br>{{ $service->serviceType->name ?? "Servicio" }}'))
                            .addTo(map);
                            
                        // Asegurar que el mapa se redimensione correctamente
                        map.on('load', function() {
                            map.resize();
                        });
                    } catch (e) {
                        console.error('Error inicializando Mapbox:', e);
                        document.getElementById('mapbox-map').style.display = 'none';
                        document.getElementById('map-fallback').classList.remove('hidden');
                    }
                });
            </script>
            @endpush
            @endif
            <!-- Acciones del Servicio -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Acciones del Servicio</h2>

                @if($service->status == "pendiente")
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-medium text-blue-900">Servicio Pendiente</h3>
                            <p class="text-blue-700">El servicio está programado para {{ $service->scheduled_date->format("d/m/Y H:i") }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        @php
                            // Usar la variable global $isTechnicianViewMode que ya fue detectada arriba
                            $isTechnicianView = $isTechnicianViewMode;

                            // Generar URL directamente para evitar problemas con route()
                            if ($isTechnicianView) {
                                $startUrl = url('/admin/technician-view/services/' . $service->id . '/start');
                            } else {
                                try {
                                    $startUrl = route("technician.service.start", $service);
                                } catch (\Exception $e) {
                                    $startUrl = url('/technician/services/' . $service->id . '/start');
                                }
                            }
                        @endphp
                        <form method="POST" action="{{ $startUrl }}" id="start-service-form-{{ $service->id }}" style="display: inline-block;">
                            @csrf
                            <button type="submit" id="start-service-btn-{{ $service->id }}"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="btn-text">Iniciar Servicio</span>
                            </button>
                        </form>
                        <script>
                            (function() {
                                'use strict';
                                const formId = 'start-service-form-{{ $service->id }}';
                                const btnId = 'start-service-btn-{{ $service->id }}';

                                function initForm() {
                                    const form = document.getElementById(formId);
                                    const btn = document.getElementById(btnId);

                                    if (!form || !btn) {
                                        setTimeout(initForm, 100);
                                        return;
                                    }

                                    // Función para detectar si estamos en modo technician-view
                                    function detectTechnicianView() {
                                        const currentUrl = window.location.href;
                                        const pathname = window.location.pathname;

                                        console.log('🔍 Detectando modo technician-view...');
                                        console.log('  - URL completa:', currentUrl);
                                        console.log('  - Pathname:', pathname);

                                        // PRIORIDAD 1: Verificar atributo del body (más confiable - viene del servidor)
                                        const body = document.body;
                                        if (body) {
                                            const hasAttribute = body.getAttribute('data-technician-view') === 'true';
                                            const hasClass = body.classList.contains('technician-view-mode');
                                            console.log('  - Body data-technician-view:', hasAttribute);
                                            console.log('  - Body class technician-view-mode:', hasClass);

                                            if (hasAttribute || hasClass) {
                                                console.log('✅ Modo technician-view detectado por atributo del body (PRIORIDAD 1)');
                                                return true;
                                            }
                                        }

                                        // PRIORIDAD 2: Verificar URL actual
                                        if (currentUrl.includes('/admin/technician-view/') ||
                                            pathname.includes('/admin/technician-view/') ||
                                            currentUrl.includes('technician-view')) {
                                            console.log('✅ Modo technician-view detectado por URL (PRIORIDAD 2)');
                                            return true;
                                        }

                                        // PRIORIDAD 3: Verificar si hay algún indicador en localStorage o sessionStorage
                                        // (por si el servidor guardó algo ahí)
                                        try {
                                            const stored = localStorage.getItem('view_as_technician') || sessionStorage.getItem('view_as_technician');
                                            if (stored === 'true' || stored === '1') {
                                                console.log('✅ Modo technician-view detectado por storage (PRIORIDAD 3)');
                                                return true;
                                            }
                                        } catch (e) {
                                            // Ignorar errores de storage
                                        }

                                        console.log('❌ Modo technician-view NO detectado');
                                        return false;
                                    }

                                    // Detectar y corregir URL inmediatamente
                                    const isTechnicianView = detectTechnicianView();
                                    console.log('📋 URL inicial del formulario:', form.action);
                                    console.log('📋 Modo technician-view detectado:', isTechnicianView);

                                    // FORZAR corrección si detectamos technician-view
                                    if (isTechnicianView) {
                                        const correctUrl = '/admin/technician-view/services/{{ $service->id }}/start';
                                        if (form.action !== correctUrl && !form.action.includes('/admin/technician-view/')) {
                                            form.action = correctUrl;
                                            console.log('✅ URL FORZADA a technician-view:', form.action);
                                        }
                                    }

                                    form.addEventListener('submit', function(e) {
                                        e.preventDefault();
                                        e.stopPropagation();

                                        // Re-detectar antes de enviar (por si cambió algo)
                                        const isTechView = detectTechnicianView();
                                        let submitUrl = form.action;

                                        // FORZAR URL correcta si estamos en technician-view
                                        if (isTechView) {
                                            submitUrl = '/admin/technician-view/services/{{ $service->id }}/start';
                                            form.action = submitUrl; // Actualizar también el formulario
                                            console.log('✅ URL FORZADA antes de enviar:', submitUrl);
                                        } else if (!isTechView && submitUrl.includes('/admin/technician-view/')) {
                                            // Si NO estamos en technician-view pero la URL lo indica, corregir
                                            submitUrl = '/technician/services/{{ $service->id }}/start';
                                            form.action = submitUrl;
                                            console.log('⚠️ URL corregida a technician normal:', submitUrl);
                                        }

                                        console.log('Formulario enviado - Iniciando servicio');
                                        console.log('URL final:', submitUrl);

                                        const btnText = btn.querySelector('.btn-text');
                                        const originalText = btnText ? btnText.textContent : 'Iniciar Servicio';

                                        if (btnText) {
                                            btnText.textContent = 'Iniciando...';
                                        }
                                        btn.disabled = true;

                                        const formData = new FormData(form);

                                        fetch(submitUrl, {
                                            method: 'POST',
                                            body: formData,
                                            headers: {
                                                'X-Requested-With': 'XMLHttpRequest'
                                            },
                                            credentials: 'same-origin'
                                        })
                                        .then(function(response) {
                                            console.log('Respuesta recibida:', response.status, response.url);

                                            if (response.redirected) {
                                                window.location.href = response.url;
                                            } else if (response.ok) {
                                                return response.text().then(function(text) {
                                                    // Buscar redirección en la respuesta
                                                    const match = text.match(/window\.location\.href\s*=\s*['"]([^'"]+)['"]/);
                                                    if (match) {
                                                        window.location.href = match[1];
                                                    } else {
                                                        window.location.reload();
                                                    }
                                                });
                                            } else {
                                                throw new Error('HTTP ' + response.status);
                                            }
                                        })
                                        .catch(function(error) {
                                            console.error('Error:', error);
                                            alert('Error al iniciar el servicio: ' + error.message);
                                            btn.disabled = false;
                                            if (btnText) {
                                                btnText.textContent = originalText;
                                            }
                                        });

                                        return false;
                                    });
                                }

                                if (document.readyState === 'loading') {
                                    document.addEventListener('DOMContentLoaded', initForm);
                                } else {
                                    initForm();
                                }
                            })();
                        </script>

                    </div>
                </div>
                @elseif($service->status == "en_progreso")
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-medium text-yellow-900">Servicio en Progreso</h3>
                            <p class="text-yellow-700">Iniciado el {{ $service->started_at->format("d/m/Y H:i") }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex space-x-3">
                        <a href="{{ route("technician.service.checklist", $service) }}"
                           class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            Realizar Checklist
                        </a>
                        <a href="{{ route("technician.service.checklist-details", $service) }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Ver Detalle
                        </a>
                    </div>
                </div>
                @endif

                <!-- Checklist Completado -->
                @if($service->status === "finalizado")
                <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-medium text-green-900">Checklist Completado</h3>
                            <p class="text-green-700">Completado el {{ $service->checklist_completed_at ? $service->checklist_completed_at->format("d/m/Y H:i") : "Fecha no disponible" }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route("technician.service.checklist-details", $service) }}"
                           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Ver Observaciones Detalladas
                        </a>

                        <!-- Botón para Generar PDF -->
                        <a href="{{ route("technician.service.pdf", $service) }}" class="inline">

                            <span
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors" style="color: white !important;">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Generar PDF
                            </span></a>

                    </div>
                </div>
                @endif
            </div>

            <!-- Información Adicional -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Detalles del Servicio</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tipo:</span>
                            <span class="font-medium">{{ $service->serviceType->name ?? ucfirst(str_replace('-', ' ', $service->service_type)) ?? "Sanitización" }}</span>
                        </div>

                        @if($service->service_type === 'servicios-especiales' && $service->special_service_title)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Servicio Especial:</span>
                            <span class="font-semibold text-green-700 flex items-center">
                                <span class="mr-1">🏷️</span>
                                {{ $service->special_service_title }}
                            </span>
                        </div>
                        @endif

                        <div class="flex justify-between">
                            <span class="text-gray-600">Prioridad:</span>
                            <span class="font-medium">{{ ucfirst($service->priority ?? "Media") }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Programado:</span>
                            <span class="font-medium">{{ $service->scheduled_date->format("d/m/Y H:i") }}</span>
                        </div>
                        @if($service->started_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Iniciado:</span>
                            <span class="font-medium">{{ $service->started_at->format("d/m/Y H:i") }}</span>
                        </div>
                        @endif
                        @if($service->completed_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Completado:</span>
                            <span class="font-medium">{{ $service->checklist_completed_at ? $service->checklist_completed_at->format("d/m/Y H:i") : "Fecha no disponible" }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Estadísticas</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Observaciones:</span>
                            <span class="font-medium">{{ $service->observations->count() }}</span>
                        </div>
                        @if($service->started_at && $service->completed_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Duración:</span>
                            <span class="font-medium">{{ $service->started_at->diffInMinutes($service->completed_at) }} min</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">Checklist:</span>
                            <span class="font-medium">
                                @if($service->status === "finalizado")
                                    <span class="text-green-600">✓ Completado</span>
                                @else
                                    <span class="text-yellow-600">⏳ Pendiente</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Descripción -->
            @if($service->description)
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Descripción</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700">{{ $service->description }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 border-t">
            <div class="flex justify-between items-center">
                <a href="{{ route("technician.services") }}"
                   class="inline-flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver a Servicios
                </a>
                <div class="text-sm text-gray-500">
                    Última actualización: {{ $service->updated_at->format("d/m/Y H:i") }}
                </div>
            </div>
        </div>
    </div>
@push('scripts')
<script>
    // Page Mobile Menu Button
    (function() {
        function initPageMenu() {
            const pageMenuButton = document.getElementById('page-mobile-menu-button');
            const sidebar = document.getElementById('sidebar');
            const mobileOverlay = document.getElementById('mobile-overlay');
            
            if (!pageMenuButton) {
                setTimeout(initPageMenu, 100);
                return;
            }
            
            if (!sidebar) {
                console.error('Sidebar no encontrado');
                return;
            }
            
            function toggleMobileMenu() {
                const currentTransform = sidebar.style.transform || '';
                // Asumimos cerrado si tiene -100% o si no tiene la clase translate-x-0
                const isClosed = currentTransform.includes('-100%') || !sidebar.classList.contains('translate-x-0');
                
                if (isClosed) {
                    // Abrir
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                    sidebar.style.transform = 'translateX(0)';
                    
                    // Forzar estilos críticos
                    let styleTag = document.getElementById('mobile-menu-override-style');
                    if (!styleTag) {
                        styleTag = document.createElement('style');
                        styleTag.id = 'mobile-menu-override-style';
                        document.head.appendChild(styleTag);
                    }
                    styleTag.textContent = `#sidebar { transform: translateX(0) !important; display: flex !important; z-index: 9999 !important; position: fixed !important; left: 0 !important; top: 0 !important; height: 100vh !important; }`;
                    
                    if (mobileOverlay) {
                        mobileOverlay.classList.remove('hidden');
                        mobileOverlay.style.display = 'block';
                    }
                    
                    const menuIcon = document.getElementById('page-menu-icon');
                    const closeIcon = document.getElementById('page-close-icon');
                    if (menuIcon) menuIcon.classList.add('hidden');
                    if (closeIcon) closeIcon.classList.remove('hidden');
                    
                    document.body.style.overflow = 'hidden';
                } else {
                    // Cerrar
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');
                    sidebar.style.transform = 'translateX(-100%)';
                    
                    const styleTag = document.getElementById('mobile-menu-override-style');
                    if (styleTag) styleTag.remove();
                    
                    if (mobileOverlay) {
                        mobileOverlay.classList.add('hidden');
                        mobileOverlay.style.display = 'none';
                    }
                    
                    const menuIcon = document.getElementById('page-menu-icon');
                    const closeIcon = document.getElementById('page-close-icon');
                    if (menuIcon) menuIcon.classList.remove('hidden');
                    if (closeIcon) closeIcon.classList.add('hidden');
                    
                    document.body.style.overflow = '';
                }
            }
            
            pageMenuButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleMobileMenu();
            });
            
            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', function() {
                    toggleMobileMenu();
                });
            }
            
            if (sidebar) {
                const sidebarLinks = sidebar.querySelectorAll('a');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth < 768) {
                            toggleMobileMenu();
                        }
                    });
                });
            }
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPageMenu);
        } else {
            initPageMenu();
        }
    })();
</script>
@endpush
@endsection
