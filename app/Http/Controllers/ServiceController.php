<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Client;
use App\Models\Product;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with("client", "assignedUser", "serviceType")->orderBy("created_at", "desc")->paginate(10);
        return view("services.index", compact("services"));
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

    public function store(Request $request)
    {
        Log::info("ServiceController@store called", ["request" => $request->all()]);
        
        $request->validate([
            "client_id" => "required|exists:clients,id",
            "service_type_id" => "required|exists:service_types,id",
            "scheduled_date" => "required|date",
            "address" => "required|string|max:255",
            "priority" => "required|in:alta,media,baja",
            "description" => "nullable|string",
            "assigned_to" => "nullable|exists:users,id",
        ]);

        $service = Service::create([
            "client_id" => $request->client_id,
            "service_type_id" => $request->service_type_id,
            "scheduled_date" => $request->scheduled_date,
            "address" => $request->address,
            "priority" => $request->priority,
            "description" => $request->description,
            "assigned_to" => $request->assigned_to,
            "status" => "pendiente",
        ]);

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

    public function update(Request $request, Service $service)
    {
        $request->validate([
            "client_id" => "required|exists:clients,id",
            "service_type_id" => "required|exists:service_types,id",
            "scheduled_date" => "required|date",
            "address" => "required|string|max:255",
            "priority" => "required|in:alta,media,baja",
            "description" => "nullable|string",
        ]);

        $service->update($request->all());

        return redirect()->route("admin.services.index")->with("success", "Servicio actualizado exitosamente");
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route("admin.services.index")->with("success", "Servicio eliminado exitosamente");
    }


}
