<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::withCount("services")->latest()->paginate(10);
        return view("clients.index", compact("clients"));
    }

    public function create()
    {
        return view("clients.create");
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "rut" => "required|string|max:20|unique:clients",
            "email" => "required|email|max:255",
            "phone" => "required|string|max:20",
            "address" => "required|string|max:255",
            "business_type" => "nullable|string|max:255",
            "contact_person" => "nullable|string|max:255",
        ]);

        Client::create($request->all());

        return redirect()->route("admin.clients.index")->with("success", "Cliente creado exitosamente");
    }

    public function show(Client $client)
    {
        $client->load("services");
        return view("clients.show", compact("client"));
    }

    public function edit(Client $client)
    {
        return view("clients.edit", compact("client"));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "rut" => "required|string|max:20|unique:clients,rut," . $client->id,
            "email" => "required|email|max:255",
            "phone" => "required|string|max:20",
            "address" => "required|string|max:255",
            "business_type" => "nullable|string|max:255",
            "contact_person" => "nullable|string|max:255",
        ]);

        $client->update($request->all());

        return redirect()->route("admin.clients.index")->with("success", "Cliente actualizado exitosamente");
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route("admin.clients.index")->with("success", "Cliente eliminado exitosamente");
    }
}
