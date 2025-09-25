<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Client;
use App\Models\Product;
use App\Models\ServiceType;
use App\Models\User;
use App\Models\SystemNotification;
use App\Notifications\ServiceAssignedNotification;
use App\Http\Requests\ServiceUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    public function index()
    {
        $serviceTypes = ServiceType::all();
        $services = Service::with("client", "assignedUser", "serviceType")->orderBy("created_at", "desc")->paginate(10);
        return view("services.index", compact("services", "serviceTypes"));
    }

    public function create()
    {
        $clients = Client::all();
        $serviceTypes = ServiceType::all();
        $products = Product::all();
        $technicians = User::whereHas("roles", function($query) { 
            $query->where("name", "technician"); 
        })->get();

        return view("services.create", compact("clients", "products", "serviceTypes", "technicians"));
    }

    public function store(ServiceUpdateRequest $request)
    {
        Log::info("ServiceController@store called", ["request" => $request->all()]);

        $service = Service::create([
            "client_id" => $request->client_id,
            "service_type" => $request->service_type,
            "scheduled_date" => $request->scheduled_date,
            "address" => $request->address,
            "priority" => $request->priority,
            "description" => $request->description,
            "assigned_to" => $request->assigned_to,
            "status" => "pendiente",
        ]);

        // Enviar notificación si hay un técnico asignado
        if ($service->assigned_to) {
            $service->load('assignedUser', 'client', 'serviceType');
            
            // Notificación Laravel nativa
            $service->assignedUser->notify(new ServiceAssignedNotification($service));
            
            // Notificación en el sistema
            SystemNotification::create([
                'title' => 'Nuevo Servicio Asignado',
                'message' => "Se te ha asignado un nuevo servicio para {$service->client->name} el " . $service->scheduled_date->format('d/m/Y H:i'),
                'type' => 'info',
                'priority' => 'medium',
                'user_id' => $service->assigned_to,
                'service_id' => $service->id,
                'metadata' => [
                    'service_type' => $service->serviceType->name ?? 'N/A',
                    'client_name' => $service->client->name,
                    'priority' => $service->priority,
                ]
            ]);
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
        $serviceTypes = ServiceType::all();
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
            $service->load('assignedUser', 'client', 'serviceType');
            // Notificación Laravel nativa
            $service->assignedUser->notify(new ServiceAssignedNotification($service));
            
            // Notificación en el sistema
            SystemNotification::create([
                'title' => 'Servicio Reasignado',
                'message' => "Se te ha reasignado el servicio para {$service->client->name} el " . $service->scheduled_date->format('d/m/Y H:i'),
                'type' => 'info',
                'priority' => 'medium',
                'user_id' => $service->assigned_to,
                'service_id' => $service->id,
                'metadata' => [
                    'service_type' => $service->serviceType->name ?? 'N/A',
                    'client_name' => $service->client->name,
                    'priority' => $service->priority,
                    'action' => 'reassigned'
                ]
            ]);
        }

        return redirect()->route("admin.services.index")->with("success", "Servicio actualizado exitosamente");
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route("admin.services.index")->with("success", "Servicio eliminado exitosamente");
    }


}
