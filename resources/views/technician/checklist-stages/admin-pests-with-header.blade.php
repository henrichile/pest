@extends('layouts.app')

@section('title', 'Catálogo de Plagas')

@section('content')
<div class="space-y-4 sm:space-y-6 pt-12 md:pt-0">
    @include('admin.partials.header', [
        'title' => 'Catálogo de Plagas',
        'subtitle' => 'Información sobre plagas comunes',
        'searchPlaceholder' => 'Buscar plagas...',
        'pageId' => 'pests'
    ])

    <!-- Botón Crear Nueva Plaga -->
    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.pests.create') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-green-600 hover:bg-green-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Crear Nueva Plaga
        </a>
    </div>

    <!-- Search Bar -->
    <div class="mb-4 sm:mb-6">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-600 dark:text-gray-300">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>
            <input type="text" id="search-input" class="block w-full pl-9 sm:pl-10 pr-3 py-2 sm:py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" style="border: 1px solid #e5e7eb !important; color: #111827;" placeholder="Buscar por nombre..." value="{{ request('search') }}">
        </div>
    </div>

    <!-- Pests Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($pests as $pest)
            <div class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg overflow-hidden transition-shadow hover:shadow-md cursor-pointer pest-card border border-gray-200 dark:border-gray-700" data-pest-id="{{ $pest->id }}">
                <div class="p-5">
                    <!-- Pest Name -->
                    <div class="mb-3">
                        <h3 class="text-lg font-bold mb-1 text-gray-900 dark:text-white">{{ $pest->name ?? 'Sin nombre' }}</h3>
                    </div>

                    <!-- Category Tag -->
                    <div class="mb-3">
                        @php
                            $categoryColors = [
                                'Roedores' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                                'Cucarachas' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                                'Moscas' => ['bg' => '#e0e7ff', 'text' => '#3730a3'],
                                'Termitas' => ['bg' => '#fce7f3', 'text' => '#9f1239'],
                                'Hormigas' => ['bg' => '#f3e8ff', 'text' => '#6b21a8'],
                                'Aves' => ['bg' => '#f0fdf4', 'text' => '#166534'],
                                'Arañas' => ['bg' => '#fce7f3', 'text' => '#9f1239'],
                                'Otros' => ['bg' => '#f3f4f6', 'text' => '#374151'],
                            ];
                            $color = $categoryColors[$pest->category] ?? ['bg' => '#f3f4f6', 'text' => '#374151'];
                        @endphp
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium" style="background: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                            {{ ucfirst($pest->category ?? 'otro') }}
                        </span>
                    </div>

                    <!-- Description / Technical Notes -->
                    @if($pest->technical_notes)
                        <p class="text-sm mb-4">
                            {{ Str::limit($pest->technical_notes, 120) }}
                        </p>
                    @endif

                    <!-- Control Methods / Treatment -->
                    @if($pest->control_methods)
                        <div>
                            <p class="text-sm font-semibold mb-2">Tratamiento:</p>
                            <ul class="list-disc list-inside text-sm space-y-1 text-gray-600 dark:text-gray-300">
                                @if(is_array($pest->control_methods))
                                    @foreach(array_slice($pest->control_methods, 0, 3) as $method)
                                        <li>{{ $method }}</li>
                                    @endforeach
                                    @if(count($pest->control_methods) > 3)
                                        <li class="text-xs italic">+{{ count($pest->control_methods) - 3 }} más...</li>
                                    @endif
                                @else
                                    <li>{{ $pest->control_methods }}</li>
                                @endif
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg p-8 text-center border border-gray-200 dark:border-gray-700">
                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-300">No se encontraron plagas</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal for Pest Details -->
<div id="pest-modal" class="fixed hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="z-index: 9999; display: none; top: 0; left: 0; right: 0; bottom: 0;">
    <!-- Background overlay -->
    <div class="fixed bg-gray-900 transition-opacity" id="modal-overlay" style="z-index: 9998; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.75) !important;"></div>

    <!-- Modal container - Centered -->
    <div class="fixed z-50 flex items-center justify-center p-2 sm:p-4" style="z-index: 10000; pointer-events: none; top: 0; left: 0; right: 0; bottom: 0;" id="modal-container">
        <!-- Modal panel -->
        <div class="relative bg-white rounded-lg sm:rounded-xl shadow-2xl w-full sm:max-w-3xl max-h-[95vh] sm:max-h-[85vh] overflow-y-auto" style="border: 1px solid #e5e7eb !important; pointer-events: auto; margin: 0.5rem;" id="modal-content">
            <!-- Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-4 sm:px-6 py-3 sm:py-4 flex items-start justify-between z-10" style="border-bottom: 1px solid #e5e7eb !important;">
                <div class="flex-1 pr-4">
                    <h3 class="text-xl sm:text-2xl font-bold mb-2 text-gray-900 dark:text-white" id="modal-pest-name">Cargando...</h3>
                    <div class="mt-2">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium" id="modal-category"></span>
                    </div>
                </div>
                <button type="button" class="flex-shrink-0 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors" id="close-modal" style="cursor: pointer;">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="px-4 sm:px-6 py-4 sm:py-6 space-y-4 sm:space-y-6">
                <!-- Description -->
                <div id="modal-description" class="hidden">
                    <h4 class="text-lg font-semibold mb-3 text-gray-900 dark:text-white">
                        <svg class="inline-block w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-600 dark:text-gray-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                        Descripción
                    </h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm leading-relaxed" id="modal-description-text"></p>
                    </div>
                </div>

                <!-- Treatment / Control Methods -->
                <div id="modal-treatment" class="hidden">
                    <h4 class="text-lg font-semibold mb-3 text-gray-900 dark:text-white">
                        <svg class="inline-block w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                        </svg>
                        Métodos de Control
                    </h4>
                    <div class="bg-green-50 rounded-lg p-4">
                        <ul class="list-disc list-inside text-sm space-y-2 text-gray-700 dark:text-gray-300" id="modal-treatment-list"></ul>
                    </div>
                </div>

                <!-- Recommendations Section -->
                <div id="modal-recommendations" class="hidden">
                    <h4 class="text-lg font-semibold mb-3 text-gray-900 dark:text-white">
                        <svg class="inline-block w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                        </svg>
                        Recomendaciones
                    </h4>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <ul class="list-disc list-inside text-sm space-y-2 text-gray-700 dark:text-gray-300" id="modal-recommendations-list"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        'use strict';

        // Pest data from server
        @php
            $pestsArray = [];
            foreach($pests as $pest) {
                $pestsArray[$pest->id] = [
                    'id' => $pest->id,
                    'name' => $pest->name,
                    'category' => $pest->category,
                    'technical_notes' => $pest->technical_notes,
                    'control_methods' => $pest->control_methods,
                    'description' => $pest->description ?? null,
                ];
            }
            $pestsJson = json_encode($pestsArray ?? []);
        @endphp
        const pestsData = {!! $pestsJson !!};

        console.log('Pests data loaded:', Object.keys(pestsData).length, 'pests');

        function initPestsModal() {
            // Search functionality
            const searchInput = document.getElementById('search-input');
            if (searchInput) {
                let searchTimeout;

                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const searchTerm = this.value.trim();

                    searchTimeout = setTimeout(() => {
                        if (searchTerm.length >= 2 || searchTerm.length === 0) {
                            const url = new URL(window.location.href);
                            if (searchTerm) {
                                url.searchParams.set('search', searchTerm);
                            } else {
                                url.searchParams.delete('search');
                            }
                            window.location.href = url.toString();
                        }
                    }, 500);
                });
            }

            // Modal functionality
            const modal = document.getElementById('pest-modal');
            const modalOverlay = document.getElementById('modal-overlay');
            const closeModalBtn = document.getElementById('close-modal');
            const pestCards = document.querySelectorAll('.pest-card');

            if (!modal) {
                console.error('Modal element not found');
                return;
            }

            if (!modalOverlay) {
                console.error('Modal overlay not found');
                return;
            }

            if (!closeModalBtn) {
                console.error('Close modal button not found');
                return;
            }

            console.log('Found', pestCards.length, 'pest cards');

            // Category colors
            const categoryColors = {
                'Roedores': { bg: '#fef3c7', text: '#92400e' },
                'Cucarachas': { bg: '#dbeafe', text: '#1e40af' },
                'Moscas': { bg: '#e0e7ff', text: '#3730a3' },
                'Termitas': { bg: '#fce7f3', text: '#9f1239' },
                'Hormigas': { bg: '#f3e8ff', text: '#6b21a8' },
                'Aves': { bg: '#f0fdf4', text: '#166534' },
                'Arañas': { bg: '#fce7f3', text: '#9f1239' },
                'Otros': { bg: '#f3f4f6', text: '#374151' }
            };

            // Generate recommendations based on pest data
            function generateRecommendations(pest) {
                const recommendations = [];

                // Recomendaciones basadas en la categoría
                if (pest.category === 'Arañas') {
                    recommendations.push('Mantener áreas limpias y libres de escombros donde puedan esconderse.');
                    recommendations.push('Sellar grietas y hendiduras en paredes y techos.');
                    recommendations.push('Usar guantes al manipular objetos almacenados por mucho tiempo.');
                } else if (pest.category === 'Roedores') {
                    recommendations.push('Eliminar fuentes de alimento y agua accesibles.');
                    recommendations.push('Sellar puntos de entrada (grietas, agujeros) con materiales resistentes.');
                    recommendations.push('Mantener el área alrededor de la propiedad libre de vegetación densa.');
                } else if (pest.category === 'Cucarachas') {
                    recommendations.push('Mantener limpieza estricta, especialmente en áreas de cocina.');
                    recommendations.push('Eliminar fuentes de humedad y reparar goteras.');
                    recommendations.push('Almacenar alimentos en recipientes herméticos.');
                } else if (pest.category === 'Termitas') {
                    recommendations.push('Realizar inspecciones regulares de estructuras de madera.');
                    recommendations.push('Mantener la madera alejada del contacto con el suelo.');
                    recommendations.push('Eliminar fuentes de humedad cerca de la estructura.');
                } else if (pest.category === 'Hormigas') {
                    recommendations.push('Limpiar derrames de comida inmediatamente.');
                    recommendations.push('Sellar puntos de entrada con masilla o sellador.');
                    recommendations.push('Mantener áreas de almacenamiento de alimentos limpias y organizadas.');
                } else if (pest.category === 'Moscas') {
                    recommendations.push('Mantener basura en recipientes cerrados y limpiarlos regularmente.');
                    recommendations.push('Instalar mosquiteros en ventanas y puertas.');
                    recommendations.push('Eliminar materia orgánica en descomposición.');
                } else {
                    recommendations.push('Mantener limpieza general del área afectada.');
                    recommendations.push('Eliminar fuentes de alimento y refugio.');
                    recommendations.push('Consultar con un profesional para tratamiento específico.');
                }

                // Recomendaciones adicionales basadas en la descripción
                if (pest.technical_notes) {
                    const notes = pest.technical_notes.toLowerCase();
                    if (notes.includes('veneno') || notes.includes('peligroso') || notes.includes('tóxico')) {
                        recommendations.push('⚠️ PRECAUCIÓN: Esta plaga puede ser peligrosa. Use equipo de protección personal.');
                        recommendations.push('Mantenga niños y mascotas alejados del área tratada.');
                    }
                    if (notes.includes('nocturno') || notes.includes('noche')) {
                        recommendations.push('Realizar inspecciones durante la noche para mejor detección.');
                    }
                }

                return recommendations;
            }

            function openModal(pestId) {
                console.log('Opening modal for pest ID:', pestId, 'Type:', typeof pestId);
                console.log('Available pests:', Object.keys(pestsData));

                // Convertir a número si es string
                const id = typeof pestId === 'string' ? parseInt(pestId, 10) : pestId;

                if (!id || !pestsData || !pestsData[id]) {
                    console.error('Pest not found. ID:', id, 'Available:', Object.keys(pestsData));
                    alert('No se encontró la información de la plaga');
                    return;
                }

                const pest = pestsData[id];
                console.log('Pest data:', pest);

                // Set pest name
                const nameEl = document.getElementById('modal-pest-name');
                if (nameEl) {
                    nameEl.textContent = pest.name || 'Sin nombre';
                }

                // Set category
                const category = pest.category || 'Otros';
                const color = categoryColors[category] || categoryColors['Otros'];
                const categorySpan = document.getElementById('modal-category');
                if (categorySpan) {
                    categorySpan.textContent = category;
                    categorySpan.style.background = color.bg;
                    categorySpan.style.color = color.text;
                }

                // Set description
                const descriptionDiv = document.getElementById('modal-description');
                const descriptionText = document.getElementById('modal-description-text');
                if (descriptionDiv && descriptionText) {
                    if (pest.technical_notes) {
                        descriptionText.textContent = pest.technical_notes;
                        descriptionDiv.classList.remove('hidden');
                    } else {
                        descriptionDiv.classList.add('hidden');
                    }
                }

                // Set treatment
                const treatmentDiv = document.getElementById('modal-treatment');
                const treatmentList = document.getElementById('modal-treatment-list');
                if (treatmentDiv && treatmentList) {
                    if (pest.control_methods) {
                        treatmentList.innerHTML = '';
                        let methods = [];
                        if (Array.isArray(pest.control_methods)) {
                            methods = pest.control_methods;
                        } else if (typeof pest.control_methods === 'string') {
                            // Intentar parsear si es JSON string
                            try {
                                methods = JSON.parse(pest.control_methods);
                            } catch (e) {
                                methods = [pest.control_methods];
                            }
                        }

                        methods.forEach(method => {
                            if (method) {
                                const li = document.createElement('li');
                                li.textContent = method;
                                li.style.marginBottom = '0.5rem';
                                treatmentList.appendChild(li);
                            }
                        });
                        treatmentDiv.classList.remove('hidden');
                    } else {
                        treatmentDiv.classList.add('hidden');
                    }
                }

                // Generate and set recommendations
                const recommendationsDiv = document.getElementById('modal-recommendations');
                const recommendationsList = document.getElementById('modal-recommendations-list');
                if (recommendationsDiv && recommendationsList) {
                    recommendationsList.innerHTML = '';

                    // Generar recomendaciones basadas en la información de la plaga
                    const recommendations = generateRecommendations(pest);

                    if (recommendations.length > 0) {
                        recommendations.forEach(rec => {
                            const li = document.createElement('li');
                            li.textContent = rec;
                            li.style.marginBottom = '0.5rem';
                            recommendationsList.appendChild(li);
                        });
                        recommendationsDiv.classList.remove('hidden');
                    } else {
                        recommendationsDiv.classList.add('hidden');
                    }
                }

                // Show modal
                if (modal) {
                    // Detectar si estamos en móvil o desktop
                    const isMobile = window.innerWidth < 768; // md breakpoint de Tailwind
                    const sidebar = document.getElementById('sidebar');
                    let sidebarWidth = 0;

                    // En desktop, calcular el ancho del sidebar solo si está visible
                    if (!isMobile && sidebar) {
                        const sidebarRect = sidebar.getBoundingClientRect();
                        // Si el sidebar está visible (no está fuera de la pantalla)
                        if (sidebarRect.left >= 0) {
                            sidebarWidth = sidebar.offsetWidth || 288; // 288px = w-72
                        }
                    }

                    // Aplicar estilos según el tamaño de pantalla
                    if (isMobile) {
                        // En móvil: ocupar toda la pantalla
                        modal.style.left = '0';
                        modal.style.top = '0';
                        modal.style.right = '0';
                        modal.style.bottom = '0';

                        const overlay = document.getElementById('modal-overlay');
                        if (overlay) {
                            overlay.style.left = '0';
                            overlay.style.top = '0';
                            overlay.style.right = '0';
                            overlay.style.bottom = '0';
                        }

                        const modalContainer = document.getElementById('modal-container');
                        if (modalContainer) {
                            modalContainer.style.left = '0';
                            modalContainer.style.top = '0';
                            modalContainer.style.right = '0';
                            modalContainer.style.bottom = '0';
                        }

                        // En móvil, bloquear el scroll del body
                        document.body.style.overflow = 'hidden';
                    } else {
                        // En desktop: solo cubrir el área de contenido (no el sidebar)
                        modal.style.left = sidebarWidth + 'px';
                        modal.style.top = '0';
                        modal.style.right = '0';
                        modal.style.bottom = '0';

                        const overlay = document.getElementById('modal-overlay');
                        if (overlay) {
                            overlay.style.left = sidebarWidth + 'px';
                            overlay.style.top = '0';
                            overlay.style.right = '0';
                            overlay.style.bottom = '0';
                        }

                        const modalContainer = document.getElementById('modal-container');
                        if (modalContainer) {
                            modalContainer.style.left = sidebarWidth + 'px';
                            modalContainer.style.top = '0';
                            modalContainer.style.right = '0';
                            modalContainer.style.bottom = '0';
                        }

                        // En desktop, solo bloquear el scroll del contenido principal
                        const mainContent = document.querySelector('main');
                        if (mainContent) {
                            mainContent.style.overflow = 'hidden';
                        }
                    }

                    modal.classList.remove('hidden');
                    modal.style.display = 'block';
                    modal.style.zIndex = '9999';

                    // Forzar reflow para asegurar que se muestre
                    void modal.offsetHeight;
                }
            }

            function closeModal() {
                if (modal) {
                    modal.classList.add('hidden');
                    modal.style.display = 'none';
                    modal.style.zIndex = '';

                    // Restaurar scroll
                    document.body.style.overflow = '';
                    const mainContent = document.querySelector('main');
                    if (mainContent) {
                        mainContent.style.overflow = '';
                    }
                }
            }

            // Ajustar modal al redimensionar la ventana
            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    if (modal && !modal.classList.contains('hidden')) {
                        // Recalcular posición si el modal está abierto
                        const isMobile = window.innerWidth < 768;
                        const sidebar = document.getElementById('sidebar');
                        let sidebarWidth = 0;

                        if (!isMobile && sidebar) {
                            const sidebarRect = sidebar.getBoundingClientRect();
                            // Si el sidebar está visible (no está fuera de la pantalla)
                            if (sidebarRect.left >= 0) {
                                sidebarWidth = sidebar.offsetWidth || 288;
                            }
                        }

                        if (isMobile) {
                            modal.style.left = '0';
                            const overlay = document.getElementById('modal-overlay');
                            const modalContainer = document.getElementById('modal-container');
                            if (overlay) {
                                overlay.style.left = '0';
                            }
                            if (modalContainer) {
                                modalContainer.style.left = '0';
                            }
                            document.body.style.overflow = 'hidden';
                        } else {
                            modal.style.left = sidebarWidth + 'px';
                            const overlay = document.getElementById('modal-overlay');
                            const modalContainer = document.getElementById('modal-container');
                            if (overlay) {
                                overlay.style.left = sidebarWidth + 'px';
                            }
                            if (modalContainer) {
                                modalContainer.style.left = sidebarWidth + 'px';
                            }
                            document.body.style.overflow = '';
                            const mainContent = document.querySelector('main');
                            if (mainContent) {
                                mainContent.style.overflow = 'hidden';
                            }
                        }
                    }
                }, 250);
            });

            // Open modal on card click
            if (pestCards && pestCards.length > 0) {
                pestCards.forEach(card => {
                    card.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();

                        const pestIdAttr = this.getAttribute('data-pest-id');
                        console.log('Card clicked, data-pest-id:', pestIdAttr);

                        if (pestIdAttr) {
                            const pestId = parseInt(pestIdAttr, 10);
                            if (pestId && !isNaN(pestId)) {
                                openModal(pestId);
                            } else {
                                console.error('Invalid pest ID:', pestIdAttr);
                            }
                        }
                    });
                });
            } else {
                console.warn('No pest cards found');
            }

            // Close modal handlers
            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    closeModal();
                });
            }

            if (modalOverlay) {
                modalOverlay.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    closeModal();
                });
            }

            // Close modal on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        }

        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPestsModal);
        } else {
            initPestsModal();
        }
    })();

    // Page Mobile Menu Button
    (function() {
        const pageMenuButton = document.getElementById('page-mobile-menu-button');
        const mainMenuButton = document.getElementById('mobile-menu-button');
        const sidebar = document.getElementById('sidebar');
        const mobileOverlay = document.getElementById('mobile-overlay');

        function toggleMobileMenu() {
            if (mainMenuButton) {
                mainMenuButton.click();
            } else {
                const menuIcon = document.getElementById('page-menu-icon');
                const closeIcon = document.getElementById('page-close-icon');

                if (sidebar && sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                    if (mobileOverlay) mobileOverlay.classList.remove('hidden');
                    if (menuIcon) menuIcon.classList.add('hidden');
                    if (closeIcon) closeIcon.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                } else {
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');
                    if (mobileOverlay) mobileOverlay.classList.add('hidden');
                    if (menuIcon) menuIcon.classList.remove('hidden');
                    if (closeIcon) closeIcon.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            }
        }

        if (pageMenuButton) {
            pageMenuButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleMobileMenu();
            });
        }
    })();
</script>
@endpush
@endsection

