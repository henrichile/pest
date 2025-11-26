<!-- Desktop Header: Título + Buscador + Iconos (todo en la misma línea) -->
<div class="hidden md:flex md:items-center md:justify-between gap-4 mb-4">
    <!-- Título + Buscador -->
    <div class="flex items-center gap-4">
        <div class="flex-shrink-0">
            <h2 class="text-2xl sm:text-3xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:tracking-tight" style="color: #111827; font-weight: 700;">
                {{ $title ?? 'Dashboard' }}
            </h2>
            @if(isset($subtitle))
            <p class="mt-1 text-xs sm:text-sm dark:text-white" style="color: #6b7280;">
                {{ $subtitle }}
            </p>
            @elseif(isset($showDate) && $showDate)
            <p class="mt-1 text-xs sm:text-sm dark:text-white" style="color: #6b7280;">
                {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM') }}
            </p>
            @endif
        </div>

        <!-- Buscador al lado derecho del título -->
        <div class="relative global-search-container flex-shrink-0" style="min-width: 0;">
            <div class="relative">
                <svg class="absolute" style="left: 10px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: #9ca3af; pointer-events: none; z-index: 1;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    type="text"
                    id="admin-search-input-{{ $pageId ?? 'page' }}"
                    placeholder="{{ $searchPlaceholder ?? 'Buscar...' }}"
                    class="w-56 pr-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                    style="background: white; color: #111827; padding-left: 36px; font-size: 14px;"
                    autocomplete="off"
                />
            </div>

            <!-- Search Results Dropdown -->
            <div id="admin-search-results-{{ $pageId ?? 'page' }}" class="hidden absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto" style="box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); min-width: 400px;">
                <div id="admin-search-content-{{ $pageId ?? 'page' }}" class="p-2">
                    <!-- Results will be inserted here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Iconos de notificaciones y usuario (desktop) -->
    <div class="flex items-center gap-x-4 flex-shrink-0">
        <!-- Notificaciones -->
        <div class="relative" style="overflow: visible;">
            <button type="button" class="flex items-center justify-center text-gray-500 hover:text-gray-700 relative" title="Notificaciones" id="admin-notification-button-{{ $pageId ?? 'page' }}" style="width: 40px !important; height: 40px !important; padding: 8px !important; overflow: visible !important;">
                <svg style="width: 24px !important; height: 24px !important; display: block !important; flex-shrink: 0 !important;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                @php
                    $unreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
                @endphp
                @if($unreadCount > 0)
                <span class="absolute text-white text-xs rounded-full flex items-center justify-center font-semibold" style="background: #22c55e; min-width: 20px; height: 20px; padding: 0 6px; top: -2px; right: -2px; z-index: 20; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
                @endif
            </button>

            <!-- Notification Dropdown Menu -->
            <div id="admin-notification-menu-{{ $pageId ?? 'page' }}" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50" style="max-height: 400px; overflow-y: auto;">
                <div class="p-3 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-900">Notificaciones</h3>
                    <a href="{{ route('admin.notification-center') }}" class="text-sm text-green-600 hover:text-green-700">Ver todas</a>
                </div>
                <div class="p-2">
                    @php
                        $recentNotifications = auth()->check() ? auth()->user()->notifications()->take(5)->get() : collect();
                    @endphp
                    @if($recentNotifications->count() > 0)
                        @foreach($recentNotifications as $notification)
                            @php
                                $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                                $title = $data['title'] ?? 'Notificación';
                                $message = $data['message'] ?? '';
                                $isRead = !is_null($notification->read_at);
                            @endphp
                            <div class="p-3 hover:bg-gray-50 rounded-lg cursor-pointer {{ !$isRead ? 'bg-green-50' : '' }}">
                                <div class="flex justify-between items-start">
                                    <h4 class="font-semibold text-sm text-gray-900">{{ $title }}</h4>
                                    <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($message, 80) }}</p>
                            </div>
                        @endforeach
                    @else
                        <div class="p-6 text-center text-gray-500">
                            <p>No hay notificaciones</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Usuario -->
        <div class="relative">
            <button type="button" class="flex items-center justify-center hover:bg-gray-50 rounded-lg transition-colors" id="admin-user-button-{{ $pageId ?? 'page' }}" title="Menú de usuario" style="width: 40px !important; height: 40px !important; padding: 0 !important;">
                <div class="bg-green-600 rounded-full flex items-center justify-center" style="width: 32px !important; height: 32px !important;">
                    <span class="text-white font-medium" style="font-size: 13px !important; line-height: 1 !important;">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                </div>
            </button>

            <!-- User Dropdown Menu -->
            <div id="admin-user-menu-{{ $pageId ?? 'page' }}" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                <div class="p-3 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-medium text-white">
                                {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'U' }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->check() ? auth()->user()->name : 'Usuario' }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ auth()->check() ? auth()->user()->email : '' }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-2">
                    <a href="{{ route('profile') }}" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <span>Mi Perfil</span>
                    </a>
                    @if(Route::has('admin.settings'))
                    <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Configuración</span>
                    </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            <span>Cerrar Sesión</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Header (solo título) -->
<div class="md:hidden mb-6">
    <h2 class="text-2xl font-bold leading-7 text-gray-900" style="color: #111827; font-weight: 700;">
        {{ $title ?? 'Dashboard' }}
    </h2>
    @if(isset($subtitle))
    <p class="mt-1 text-sm" style="color: #6b7280;">
        {{ $subtitle }}
    </p>
    @elseif(isset($showDate) && $showDate)
    <p class="mt-1 text-sm" style="color: #6b7280;">
        {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM') }}
    </p>
    @endif
</div>

@push('scripts')
<script>
    // Admin Header Notification and User Menu Dropdowns
    (function() {
        const pageId = '{{ $pageId ?? "page" }}';
        const notificationButton = document.getElementById('admin-notification-button-' + pageId);
        const notificationMenu = document.getElementById('admin-notification-menu-' + pageId);
        const userButton = document.getElementById('admin-user-button-' + pageId);
        const userMenu = document.getElementById('admin-user-menu-' + pageId);

        // Toggle notification menu
        if (notificationButton && notificationMenu) {
            notificationButton.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationMenu.classList.toggle('hidden');
                if (userMenu) {
                    userMenu.classList.add('hidden');
                }
            });
        }

        // Toggle user menu
        if (userButton && userMenu) {
            userButton.addEventListener('click', function(e) {
                e.stopPropagation();
                userMenu.classList.toggle('hidden');
                if (notificationMenu) {
                    notificationMenu.classList.add('hidden');
                }
            });
        }

        // Close menus when clicking outside
        document.addEventListener('click', function(e) {
            if (notificationMenu && !notificationMenu.contains(e.target) && e.target !== notificationButton) {
                notificationMenu.classList.add('hidden');
            }
            if (userMenu && !userMenu.contains(e.target) && e.target !== userButton) {
                userMenu.classList.add('hidden');
            }
        });
    })();

    // Search Functionality
    (function() {
        const pageId = '{{ $pageId ?? "page" }}';
        const searchInput = document.getElementById('admin-search-input-' + pageId);
        const searchResults = document.getElementById('admin-search-results-' + pageId);
        const searchResultsContent = document.getElementById('admin-search-content-' + pageId);
        let searchTimeout = null;
        let currentSearch = '';

        if (!searchInput) return;

        const typeIcons = {
            'service': '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>',
            'client': '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>',
            'product': '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>',
            'pest': '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>',
            'technician': '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>'
        };

        function renderResults(data) {
            if (!data || Object.keys(data).length === 0) {
                searchResultsContent.innerHTML = '<div class="p-4 text-center text-gray-500">No se encontraron resultados</div>';
                return;
            }

            let html = '';
            let hasResults = false;

            // Servicios
            if (data.services && data.services.length > 0) {
                hasResults = true;
                html += '<div class="mb-2"><div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Servicios</div>';
                data.services.forEach(item => {
                    html += `<a href="${item.url}" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="text-green-600">${typeIcons.service}</div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-900 truncate">${item.title}</div>
                            <div class="text-sm text-gray-500 truncate">${item.subtitle}</div>
                        </div>
                    </a>`;
                });
                html += '</div>';
            }

            // Clientes
            if (data.clients && data.clients.length > 0) {
                hasResults = true;
                html += '<div class="mb-2"><div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Clientes</div>';
                data.clients.forEach(item => {
                    html += `<a href="${item.url}" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="text-blue-600">${typeIcons.client}</div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-900 truncate">${item.title}</div>
                            <div class="text-sm text-gray-500 truncate">${item.subtitle}</div>
                        </div>
                    </a>`;
                });
                html += '</div>';
            }

            // Productos
            if (data.products && data.products.length > 0) {
                hasResults = true;
                html += '<div class="mb-2"><div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Productos</div>';
                data.products.forEach(item => {
                    html += `<a href="${item.url}" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="text-purple-600">${typeIcons.product}</div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-900 truncate">${item.title}</div>
                            <div class="text-sm text-gray-500 truncate">${item.subtitle}</div>
                        </div>
                    </a>`;
                });
                html += '</div>';
            }

            // Plagas
            if (data.pests && data.pests.length > 0) {
                hasResults = true;
                html += '<div class="mb-2"><div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Plagas</div>';
                data.pests.forEach(item => {
                    html += `<a href="${item.url}" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="text-red-600">${typeIcons.pest}</div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-900 truncate">${item.title}</div>
                            <div class="text-sm text-gray-500 truncate">${item.subtitle}</div>
                        </div>
                    </a>`;
                });
                html += '</div>';
            }

            // Técnicos
            if (data.technicians && data.technicians.length > 0) {
                hasResults = true;
                html += '<div class="mb-2"><div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Técnicos</div>';
                data.technicians.forEach(item => {
                    html += `<a href="${item.url}" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="text-indigo-600">${typeIcons.technician}</div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-900 truncate">${item.title}</div>
                            <div class="text-sm text-gray-500 truncate">${item.subtitle}</div>
                        </div>
                    </a>`;
                });
                html += '</div>';
            }

            if (!hasResults) {
                html = '<div class="p-4 text-center text-gray-500">No se encontraron resultados</div>';
            }

            searchResultsContent.innerHTML = html;
        }

        function performSearch(query) {
            if (query.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }

            currentSearch = query;

            fetch(`{{ route('admin.search') }}?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (query === currentSearch) {
                    renderResults(data);
                    searchResults.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error en búsqueda:', error);
                if (query === currentSearch) {
                    searchResultsContent.innerHTML = '<div class="p-4 text-center text-red-500">Error al realizar la búsqueda</div>';
                    searchResults.classList.remove('hidden');
                }
            });
        }

        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.trim();
            clearTimeout(searchTimeout);

            if (query.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(() => {
                performSearch(query);
            }, 300);
        });

        searchInput.addEventListener('focus', function() {
            if (searchInput.value.trim().length >= 2 && !searchResults.classList.contains('hidden')) {
                searchResults.classList.remove('hidden');
            }
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.global-search-container')) {
                searchResults.classList.add('hidden');
            }
        });

        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                searchResults.classList.add('hidden');
                searchInput.blur();
            }
        });
    })();
</script>
@endpush

