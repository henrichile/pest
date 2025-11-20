<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Client;
use App\Models\Product;
use App\Models\Pest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Normalizar query para búsqueda flexible
     */
    private function normalizeQuery($query)
    {
        // Limpiar y normalizar
        $query = trim($query);
        $query = mb_strtolower($query, 'UTF-8');
        
        // Separar en palabras (incluir palabras de 1 carácter también para búsquedas más flexibles)
        $words = preg_split('/\s+/', $query);
        $words = array_filter($words, function($word) {
            return strlen($word) >= 1; // Cambiado de 2 a 1 para incluir más resultados
        });
        
        // Reindexar el array
        $words = array_values($words);
        
        return [
            'original' => $query,
            'words' => $words,
            'full' => $query
        ];
    }
    
    /**
     * Crear condiciones de búsqueda flexible
     */
    private function buildSearchConditions($query, $fields)
    {
        $normalized = $this->normalizeQuery($query);
        
        return function($q) use ($normalized, $fields) {
            // Búsqueda exacta o parcial en cada campo
            foreach ($fields as $field) {
                $q->orWhere(DB::raw("LOWER({$field})"), 'like', "%{$normalized['full']}%");
            }
            
            // Búsqueda por palabras individuales (si hay más de una palabra)
            if (count($normalized['words']) > 1) {
                foreach ($normalized['words'] as $word) {
                    foreach ($fields as $field) {
                        $q->orWhere(DB::raw("LOWER({$field})"), 'like', "%{$word}%");
                    }
                }
            }
        };
    }
    
    /**
     * Búsqueda inteligente global
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q', '');
            
            if (strlen(trim($query)) < 2) {
                return response()->json([
                    'services' => [],
                    'clients' => [],
                    'products' => [],
                    'pests' => [],
                    'technicians' => [],
                ]);
            }
            
            $normalized = $this->normalizeQuery($query);
            $searchTerm = $normalized['full'];
            $words = $normalized['words'];
            
            $results = [
                'services' => [],
                'clients' => [],
                'products' => [],
                'pests' => [],
                'technicians' => [],
            ];
        
        // Buscar servicios - Obtener más servicios para filtrar en PHP
        $services = Service::with(['client', 'serviceType', 'assignedUser'])
            ->limit(50) // Obtener más para filtrar
            ->get();
        
        // Filtrar en PHP por dirección y otros campos si existen
        $filteredServices = $services->filter(function($service) use ($searchTerm, $words) {
            $matches = false;
            
            // Buscar en ID
            if (stripos((string)$service->id, $searchTerm) !== false) {
                $matches = true;
            }
            
            // Buscar en address si existe
            if (!$matches && isset($service->address) && stripos($service->address, $searchTerm) !== false) {
                $matches = true;
            }
            
            // Buscar en description si existe
            if (!$matches && isset($service->description) && stripos($service->description, $searchTerm) !== false) {
                $matches = true;
            }
            
            // Buscar en service_description si existe
            if (!$matches && isset($service->service_description) && stripos($service->service_description, $searchTerm) !== false) {
                $matches = true;
            }
            
            // Buscar en cliente si existe la relación
            if (!$matches && $service->client) {
                $clientName = $service->client->business_name ?? '';
                $clientRut = $service->client->rut ?? '';
                if (stripos($clientName, $searchTerm) !== false || stripos($clientRut, $searchTerm) !== false) {
                    $matches = true;
                }
            }
            
            // Buscar por palabras individuales
            if (!$matches && count($words) > 0) {
                foreach ($words as $word) {
                    if ((isset($service->address) && stripos($service->address, $word) !== false) ||
                        (isset($service->description) && stripos($service->description, $word) !== false) ||
                        (isset($service->service_description) && stripos($service->service_description, $word) !== false) ||
                        ($service->client && stripos($service->client->business_name ?? '', $word) !== false)) {
                        $matches = true;
                        break;
                    }
                }
            }
            
            return $matches;
        })->take(5);
        
        foreach ($filteredServices as $service) {
            $clientName = $service->client ? ($service->client->business_name ?? 'Sin cliente') : 'Sin cliente';
            $serviceType = $service->serviceType ? $service->serviceType->name : 'Sin tipo';
            
            $results['services'][] = [
                'id' => $service->id,
                'title' => 'Servicio #' . $service->id,
                'subtitle' => $clientName . ' - ' . $serviceType,
                'type' => 'service',
                'url' => route('admin.services.show', $service),
            ];
        }
        
        // Buscar clientes - Obtener todos y filtrar en PHP (más confiable)
        $allClients = Client::all(); // Obtener todos los clientes
        
        // Filtrar en PHP - Lógica simplificada y más robusta
        $filteredClients = $allClients->filter(function($client) use ($searchTerm, $words) {
            // Normalizar campos del cliente - La tabla usa 'name', no 'business_name'
            $clientName = mb_strtolower(trim($client->name ?? ''), 'UTF-8');
            $rut = mb_strtolower(trim($client->rut ?? ''), 'UTF-8');
            $businessType = mb_strtolower(trim($client->business_type ?? ''), 'UTF-8');
            $email = mb_strtolower(trim($client->email ?? ''), 'UTF-8');
            $phone = mb_strtolower(trim($client->phone ?? ''), 'UTF-8');
            $address = mb_strtolower(trim($client->address ?? ''), 'UTF-8');
            $contactPerson = mb_strtolower(trim($client->contact_person ?? ''), 'UTF-8');
            
            $searchTermClean = trim($searchTerm);
            
            // 1. Buscar término completo en name (nombre del cliente)
            if (!empty($clientName)) {
                if (strpos($clientName, $searchTermClean) !== false) {
                    return true;
                }
            }
            
            // 2. Buscar término completo en otros campos
            if ((!empty($rut) && strpos($rut, $searchTermClean) !== false) ||
                (!empty($businessType) && strpos($businessType, $searchTermClean) !== false) ||
                (!empty($email) && strpos($email, $searchTermClean) !== false) ||
                (!empty($phone) && strpos($phone, $searchTermClean) !== false) ||
                (!empty($address) && strpos($address, $searchTermClean) !== false) ||
                (!empty($contactPerson) && strpos($contactPerson, $searchTermClean) !== false)) {
                return true;
            }
            
            // 3. Buscar por palabras individuales
            if (count($words) > 0) {
                foreach ($words as $word) {
                    $wordClean = trim($word);
                    if (strlen($wordClean) >= 2) {
                        // Buscar en name
                        if (!empty($clientName) && strpos($clientName, $wordClean) !== false) {
                            return true;
                        }
                        // Buscar en otros campos
                        if ((!empty($rut) && strpos($rut, $wordClean) !== false) ||
                            (!empty($businessType) && strpos($businessType, $wordClean) !== false) ||
                            (!empty($email) && strpos($email, $wordClean) !== false) ||
                            (!empty($phone) && strpos($phone, $wordClean) !== false) ||
                            (!empty($address) && strpos($address, $wordClean) !== false) ||
                            (!empty($contactPerson) && strpos($contactPerson, $wordClean) !== false)) {
                            return true;
                        }
                    }
                }
            }
            
            return false;
        })->take(5);
        
        foreach ($filteredClients as $client) {
            // Construir información de contacto
            $contactInfo = '';
            if ($client->contact_person) {
                $contactInfo = $client->contact_person;
            }
            if ($client->email) {
                $contactInfo .= ($contactInfo ? ' - ' : '') . $client->email;
            }
            if ($client->phone) {
                $contactInfo .= ($contactInfo ? ' - ' : '') . $client->phone;
            }
            
            $results['clients'][] = [
                'id' => $client->id,
                'title' => $client->name ?? 'Sin nombre',
                'subtitle' => $contactInfo ?: ($client->rut ?? 'Sin RUT'),
                'type' => 'client',
                'url' => route('admin.clients.show', $client),
            ];
        }
        
        // Buscar productos - Simplificado
        $allProducts = Product::limit(50)->get();
        $filteredProducts = $allProducts->filter(function($product) use ($searchTerm, $words) {
            $name = mb_strtolower(trim($product->name ?? ''), 'UTF-8');
            $description = mb_strtolower(trim($product->description ?? ''), 'UTF-8');
            $sku = mb_strtolower(trim($product->sku ?? ''), 'UTF-8');
            
            if (strpos($name, $searchTerm) !== false ||
                strpos($description, $searchTerm) !== false ||
                strpos($sku, $searchTerm) !== false) {
                return true;
            }
            
            foreach ($words as $word) {
                if (strpos($name, $word) !== false ||
                    strpos($description, $word) !== false ||
                    strpos($sku, $word) !== false) {
                    return true;
                }
            }
            
            return false;
        })->take(5);
        
        foreach ($filteredProducts as $product) {
            $results['products'][] = [
                'id' => $product->id,
                'title' => $product->name,
                'subtitle' => 'SKU: ' . ($product->sku ?? 'N/A') . ' | Stock: ' . ($product->stock ?? 0),
                'type' => 'product',
                'url' => route('admin.products.show', $product),
            ];
        }
        
        // Buscar plagas - Simplificado
        $allPests = Pest::limit(50)->get();
        $filteredPests = $allPests->filter(function($pest) use ($searchTerm, $words) {
            $name = mb_strtolower(trim($pest->name ?? ''), 'UTF-8');
            $scientificName = mb_strtolower(trim($pest->scientific_name ?? ''), 'UTF-8');
            $description = mb_strtolower(trim($pest->description ?? ''), 'UTF-8');
            
            if (strpos($name, $searchTerm) !== false ||
                strpos($scientificName, $searchTerm) !== false ||
                strpos($description, $searchTerm) !== false) {
                return true;
            }
            
            foreach ($words as $word) {
                if (strpos($name, $word) !== false ||
                    strpos($scientificName, $word) !== false ||
                    strpos($description, $word) !== false) {
                    return true;
                }
            }
            
            return false;
        })->take(5);
        
        foreach ($filteredPests as $pest) {
            $results['pests'][] = [
                'id' => $pest->id,
                'title' => $pest->name,
                'subtitle' => $pest->scientific_name ?? 'Sin nombre científico',
                'type' => 'pest',
                'url' => route('admin.pests'),
            ];
        }
        
        // Buscar técnicos - Simplificado
        $allTechnicians = User::whereHas('roles', function($q) {
                $q->where('name', 'technician');
            })
            ->limit(50)
            ->get();
        
        $filteredTechnicians = $allTechnicians->filter(function($technician) use ($searchTerm, $words) {
            $name = mb_strtolower(trim($technician->name ?? ''), 'UTF-8');
            $email = mb_strtolower(trim($technician->email ?? ''), 'UTF-8');
            
            if (strpos($name, $searchTerm) !== false ||
                strpos($email, $searchTerm) !== false) {
                return true;
            }
            
            foreach ($words as $word) {
                if (strpos($name, $word) !== false ||
                    strpos($email, $word) !== false) {
                    return true;
                }
            }
            
            return false;
        })->take(5);
        
        foreach ($filteredTechnicians as $technician) {
            $results['technicians'][] = [
                'id' => $technician->id,
                'title' => $technician->name,
                'subtitle' => $technician->email,
                'type' => 'technician',
                'url' => route('admin.users.show', $technician),
            ];
        }
        
            return response()->json($results);
        } catch (\Exception $e) {
            \Log::error('Error en búsqueda: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'query' => $request->get('q', '')
            ]);
            
            return response()->json([
                'error' => 'Error al realizar la búsqueda',
                'message' => $e->getMessage(),
                'services' => [],
                'clients' => [],
                'products' => [],
                'pests' => [],
                'technicians' => [],
            ], 500);
        }
    }
}

