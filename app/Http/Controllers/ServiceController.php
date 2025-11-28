<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Client;
use App\Models\Product;
use App\Models\ServiceType;
use App\Models\User;
use App\Notifications\ServiceAssignedNotification;
use App\Http\Requests\ServiceUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $serviceTypes = ServiceType::where('is_active', true)->get();
        
        // Construir query base
        $query = Service::with("client", "assignedUser", "serviceType");
        
        // Aplicar filtros
        if ($request->filled('status')) {
            $status = $request->status;
            // Mapear "completado" a "finalizado" para compatibilidad
            if ($status === 'completado') {
                $status = 'finalizado';
            }
            $query->where('status', $status);
        }
        
        if ($request->filled('type')) {
            $query->where('service_type', $request->type);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        // Ordenar y paginar
        $services = $query->orderBy("created_at", "desc")->paginate(10);
        
        // Mantener los parámetros de filtro en la paginación
        $services->appends($request->query());
        
        return view("admin.services", compact("services", "serviceTypes"));
    }

    public function create()
    {
        $clients = Client::all();
        $serviceTypes = ServiceType::where('is_active', true)->get();
        $products = Product::all();
        $technicians = User::whereHas("roles", function($query) {
            $query->where("name", "technician");
        })->get();

        return view("services.create", compact("clients", "products", "serviceTypes", "technicians"));
    }

    public function store(ServiceUpdateRequest $request)
    {
        Log::info("ServiceController@store called", ["request" => $request->all()]);

        $serviceData = [
            "client_id" => $request->client_id,
            "service_type" => $request->service_type,
            "special_service_title" => $request->special_service_title, // Título para servicios especiales
            "scheduled_date" => $request->scheduled_date,
            "address" => $request->address,
            "priority" => $request->priority,
            "description" => $request->description,
            "assigned_to" => $request->assigned_to,
            "status" => "pendiente",
        ];

        // Solo agregar precio si el usuario es super-admin y el campo está presente
        if (auth()->check() && auth()->user()->hasRole('super-admin') && $request->filled('price')) {
            $serviceData["price"] = $request->price;
        }

        $service = Service::create($serviceData);

        // Enviar notificación si hay un técnico asignado
        if ($service->assigned_to) {
            try {
                $service->load('assignedUser', 'client', 'serviceType');
                
                if ($service->assignedUser) {
                    // Notificación Laravel nativa (se guarda en la tabla notifications)
                    $service->assignedUser->notify(new ServiceAssignedNotification($service));
                }
            } catch (\Exception $e) {
                Log::error('Error enviando notificación de servicio asignado: ' . $e->getMessage());
            }
        }

        return redirect()->route("admin.services.index")->with("success", "Servicio creado exitosamente");
    }

    public function show(Service $service)
    {
        $service->load("client", "serviceType", "assignedUser");
        return view("services.show", compact("service"));
    }

    public function edit(Service $service)
    {
        $clients = Client::all();
        $serviceTypes = ServiceType::where('is_active', true)->get();
        $products = Product::all();
        $technicians = User::whereHas("roles", function($query) {
            $query->where("name", "technician");
        })->get();

        return view("services.edit", compact("service", "clients", "products", "serviceTypes", "technicians"));
    }

    public function update(ServiceUpdateRequest $request, Service $service)
    {
        Log::info("ServiceController@update called", ["request" => $request->all()]);
        $oldAssignedTo = $service->assigned_to;
        $service->update($request->all());

        // Si se cambió la asignación del técnico
        if ($oldAssignedTo != $request->assigned_to && $request->assigned_to) {
            try {
                $service->load('assignedUser', 'client', 'serviceType');
                
                if ($service->assignedUser) {
                    // Marcar como reasignación en el request para que la notificación lo detecte
                    request()->merge(['reassigned' => true]);
                    
                    // Notificación Laravel nativa (se guarda en la tabla notifications)
                    $service->assignedUser->notify(new ServiceAssignedNotification($service));
                }
            } catch (\Exception $e) {
                Log::error('Error enviando notificación de servicio reasignado: ' . $e->getMessage());
            }
        }

        return redirect()->route("admin.services.index")->with("success", "Servicio actualizado exitosamente");
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route("admin.services.index")->with("success", "Servicio eliminado exitosamente");
    }


}
