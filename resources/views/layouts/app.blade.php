<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield("title", "Pest Controller SAT")</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar-transition { transition: all 0.3s ease-in-out; }
        .animate-fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }                                                                                            
        
        /* Scroll mejorado para el sidebar */
        #sidebar {
            overflow-y: auto;
            max-height: 100vh;
            scrollbar-width: thin;
            scrollbar-color: #4B5563 #1F2937;
        }
        
        #sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        #sidebar::-webkit-scrollbar-track {
            background: #1F2937;
        }
        
        #sidebar::-webkit-scrollbar-thumb {
            background: #4B5563;
            border-radius: 3px;
        }
        
        #sidebar::-webkit-scrollbar-thumb:hover {
            background: #6B7280;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="bg-black text-white w-64 sidebar-transition transform -translate-x-full lg:translate-x-0 fixed lg:static inset-y-0 z-50">                                                              
            <div class="flex items-center justify-center h-24 bg-green-800">
                <a href="@if(auth()->user()->hasRole('technician')){{ route('technician.dashboard') }}@else{{ route('admin.dashboard') }}@endif" class="block">
                    <img src="https://pestcontroller.cl/wp-content/uploads/2022/07/pestcontroller-logo.png"
                         alt="Pest Controller" class="h-20 w-auto hover:scale-105 transition-transform duration-200">
                </a>
            </div>
            
            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    @php
                        $isTechnicianView = auth()->user()->hasRole("technician");
                    @endphp
                    
                    @if($isTechnicianView)
                        <!-- Vista de Técnico -->
                        <a href="{{ route("technician.dashboard") }}" 
                           class="flex items-center px-4 py-3 text-gray-300 hover:bg-green-800 hover:text-white rounded-lg transition-colors {{ request()->routeIs("technician.dashboard") ? "bg-green-800 text-white" : "" }}">                                                                                                    
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>                                                                               
                            </svg>
                            Dashboard de Técnico
                        </a>
                        
                        <a href="{{ route("technician.services") }}" 
                           class="flex items-center px-4 py-3 text-gray-300 hover:bg-green-800 hover:text-white rounded-lg transition-colors {{ request()->routeIs("technician.services*") ? "bg-green-800 text-white" : "" }}">                                                                                                    
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Mis Gestión de Servicios
                        </a>
                        
                        <a href="{{ route("technician.profile") }}" 
                           class="flex items-center px-4 py-3 text-gray-300 hover:bg-green-800 hover:text-white rounded-lg transition-colors {{ request()->routeIs("technician.profile") ? "bg-green-800 text-white" : "" }}">                                                                                                      
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>                                                                           
                            </svg>
                            Mi Perfil
                        </a>
                    @else
                        <!-- Vista de Administrador -->
                        <a href="{{ route("admin.dashboard") }}" 
                           class="flex items-center px-4 py-3 text-gray-300 hover:bg-green-800 hover:text-white rounded-lg transition-colors {{ request()->routeIs("admin.dashboard") ? "bg-green-800 text-white" : "" }}">   
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>                                                                               
                            </svg>
                            Dashboard de Admin
                        
                        <a href="{{ route("admin.services.index") }}" 
                           class="flex items-center px-4 py-3 text-gray-300 hover:bg-green-800 hover:text-white rounded-lg transition-colors {{ request()->routeIs("services.*") ? "bg-green-800 text-white" : "" }}">  
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Gestión de Servicios
                        
                        <a href="{{ route("admin.clients.index") }}" 
                           class="flex items-center px-4 py-3 text-gray-300 hover:bg-green-800 hover:text-white rounded-lg transition-colors {{ request()->routeIs("clients.*") ? "bg-green-800 text-white" : "" }}">   
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>                                                                             
                            </svg>
                            Clientes
                        </a>
                        
                        <a href="{{ route("admin.products.index") }}" 
                           class="flex items-center px-4 py-3 text-gray-300 hover:bg-green-800 hover:text-white rounded-lg transition-colors {{ request()->routeIs("admin.products.*") ? "bg-green-800 text-white" : "" }}">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Productos
                        </a>

                        <div class="mt-8" x-data="{ configOpen: false }">
                            <button @click="configOpen = !configOpen" 
                                    class="flex items-center justify-between w-full px-4 py-3 text-gray-300 hover:bg-green-800 hover:text-white rounded-lg transition-colors mb-2">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-xs font-semibold leading-6">CONFIGURACIONES</span>
                                </div>
                                <svg class="w-4 h-4 transform transition-transform duration-200" :class="{rotate-180: configOpen}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>

                            <div x-show="configOpen" x-transition class="pl-4 space-y-1">
                                <a href="{{ route("admin.users.index") }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-green-700 hover:text-white rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 115 5v1H1v-1a5 5 0 115-5z"/>
                                    </svg>
                                    Usuarios
                                </a>
                                <a href="{{ route("admin.roles-permissions") }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-green-700 hover:text-white rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 3.5a1.5 1.5 0 113 0V4a1 1 0 111 1h3a1 1 0 111 1v3a1 1 0 11-1 1h-.5a1.5 1.5 0 100 3h.5a1 1 0 111 1v3a1 1 0 11-1 1h-3a1 1 0 11-1-1v-.5a1.5 1.5 0 10-3 0v.5a1 1 0 11-1 1H6a1 1 0 11-1-1v-3a1 1 0 111-1h.5a1.5 1.5 0 100-3H6a1 1 0 11-1-1V6a1 1 0 111-1h3a1 1 0 111-1v-.5z"/>
                                    </svg>
                                    Roles y Permisos
                                </a>
                                <a href="{{ route("admin.notification-center") }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-green-700 hover:text-white rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM11 14H9V12H11V14ZM11 10H9V6H11V10Z"/>
                                    </svg>
                                    Centro de Notificaciones
                                </a>
                                <a href="{{ route("admin.service-types.index") }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-green-800 hover:text-white rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                    </svg>
                                    Tipos de Servicios
                                </a>
                                <a href="{{ route("admin.checklist-management") }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-green-700 hover:text-white rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 111-1h12a1 1 0 110 2H4a1 1 0 11-1-1zM3 8a1 1 0 111-1h12a1 1 0 110 2H4a1 1 0 11-1-1zM3 12a1 1 0 111-1h12a1 1 0 110 2H4a1 1 0 11-1-1zM3 16a1 1 0 111-1h12a1 1 0 110 2H4a1 1 0 11-1-1z"/>
                                    </svg>
                                    Gestión de Checklist
                                </a>
                                <a href="{{ route("admin.checklist-templates.index") }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-green-800 hover:text-white rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Templates de Checklist
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <button id="sidebar-toggle" class="lg:hidden text-gray-600 hover:text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>                                                                               
                            </svg>
                        </button>
                        <h1 class="ml-4 text-2xl font-bold text-gray-900">
                            @if($isTechnicianView)
                                @yield("page-title", "Dashboard")
                            @else
                                @yield("page-title", "Dashboard")
                            @endif
                        </h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Vista Actual Indicator -->
                        
                        <!-- Notifications Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9zM13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                                @php
                                    $unreadCount = \App\Models\SystemNotification::active()
                                        ->unread()
                                        ->where('user_id', auth()->id())
                                        ->count();
                                @endphp
                                @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold notification-badge">{{ $unreadCount }}</span>
                                @endif
                            </button>
                            
                            <!-- Notifications Dropdown Menu -->
                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50">
                                <!-- Header -->
                                <div class="px-4 py-3 border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-gray-900">Notificaciones</h3>
                                        <a href="{{ route("technician.notifications.index") }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Ver todas</a>
                                    </div>
                                </div>
                                
                                <!-- Notifications List -->
                                <div class="max-h-64 overflow-y-auto">
                                    @php
                                        $systemNotifications = \App\Models\SystemNotification::active()
                                            ->unread()
                                            ->where('user_id', auth()->id())
                                            ->with(['service'])
                                            ->orderBy('created_at', 'desc')
                                            ->limit(5)
                                            ->get();
                                    @endphp
                                    
                                    @if($systemNotifications->count() > 0)
                                        @foreach($systemNotifications as $notification)
                                            <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                                <div class="flex items-start space-x-3">
                                                    <div class="flex-shrink-0">
                                                        <div class="w-8 h-8 
                                                            @if($notification->type == 'info') bg-blue-100
                                                            @elseif($notification->type == 'success') bg-green-100
                                                            @elseif($notification->type == 'warning') bg-yellow-100
                                                            @elseif($notification->type == 'error') bg-red-100
                                                            @else bg-gray-100
                                                            @endif
                                                            rounded-full flex items-center justify-center">
                                                            <svg class="w-4 h-4 
                                                                @if($notification->type == 'info') text-blue-600
                                                                @elseif($notification->type == 'success') text-green-600
                                                                @elseif($notification->type == 'warning') text-yellow-600
                                                                @elseif($notification->type == 'error') text-red-600
                                                                @else text-gray-600
                                                                @endif
                                                                " fill="currentColor" viewBox="0 0 20 20">
                                                                @if($notification->type == 'success')
                                                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                @elseif($notification->type == 'warning')
                                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                                @elseif($notification->type == 'error')
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                                @else
                                                                    <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"></path>
                                                                @endif
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                                                        <p class="text-sm text-gray-500">{{ Str::limit($notification->message, 50) }}</p>
                                                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="px-4 py-3 text-center text-gray-500">
                                            <p class="text-sm">No hay notificaciones nuevas</p>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Footer -->                                <div class="px-4 py-3 border-t border-gray-200">
                                    <a href="{{ route("technician.notifications.index") }}" class="block w-full text-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                                        Ir al Centro de Notificaciones
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Menu Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-3 text-gray-700 hover:text-gray-900">                                                                                        
                                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">                                                                                                        
                                    <span class="text-white text-sm font-medium">{{ substr(auth()->user()->name, 0, 1) }}</span>                                                                                        
                                </div>
                                <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>        
                                </svg>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">                                                           
                                <div class="px-4 py-2 text-sm text-gray-500 border-b">
                                    <div class="font-medium">{{ auth()->user()->name }}</div>
                                    <div class="text-xs">{{ auth()->user()->email }}</div>
                                </div>
                                
                                <div class="border-t">
                                    <form method="POST" action="{{ route("logout") }}" class="block">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Cerrar Sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                <div class="animate-fade-in">
                    @yield("content")
                </div>
            </main>
        </div>
    </div>
    
    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        // Sidebar toggle for mobile
        document.getElementById("sidebar-toggle").addEventListener("click", function() {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("sidebar-overlay");
            
            sidebar.classList.toggle("-translate-x-full");
            overlay.classList.toggle("hidden");
        });
        
        // Función para marcar notificaciones como leídas
        function markAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/read`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                }
            }).then(response => {
                if (response.ok) {
                    location.reload();
                }
            });
        }
        
        // Auto-refresh del contador de notificaciones cada 30 segundos
        setInterval(function() {
            fetch("/notifications/count")
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const badge = document.querySelector(".notification-badge");
                    if (badge && data.count !== undefined) {
                        badge.textContent = data.count;
                    }
                })
                .catch(error => {
                    console.log('Error fetching notification count:', error);
                });
        }, 30000);
        
        // Close sidebar when clicking overlay
        document.getElementById("sidebar-overlay").addEventListener("click", function() {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("sidebar-overlay");
            
            sidebar.classList.add("-translate-x-full");
            overlay.classList.add("hidden");
        });
    </script>
    
    @yield("scripts")
</body>
</html>
