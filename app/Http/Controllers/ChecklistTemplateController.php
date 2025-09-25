<?php

namespace App\Http\Controllers;

use App\Models\ChecklistTemplate;
use App\Models\ChecklistItem;
use App\Models\ChecklistStage;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChecklistTemplateController extends Controller
{
    private function checkSuperAdmin()
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (!$user || !$user->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }
    }

    public function index()
    {
        $this->checkSuperAdmin();
        
        $templates = ChecklistTemplate::with(['serviceType', 'stages.items'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.checklist-templates.index', compact('templates'));
    }

    public function create()
    {
        $this->checkSuperAdmin();
        
        $serviceTypes = ServiceType::where('is_active', true)->get();
        
        return view('admin.checklist-templates.create-staged', compact('serviceTypes'));
    }

    public function store(Request $request)
    {
        $this->checkSuperAdmin();
        
        // Debug: Ver qué datos están llegando
        Log::info('Datos recibidos en store:', $request->all());
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_type_id' => 'required|exists:service_types,id',
            'stages' => 'required|array|min:1',
            'stages.*.name' => 'required|string|max:255',
            'stages.*.description' => 'nullable|string',
            'stages.*.is_required' => 'boolean',
            'stages.*.items' => 'required|array|min:1',
            'stages.*.items.*.title' => 'required|string|max:255',
            'stages.*.items.*.description' => 'nullable|string',
            'stages.*.items.*.type' => 'required|in:text,number,select,checkbox,file',
            'stages.*.items.*.options' => 'nullable|string',
            'stages.*.items.*.is_required' => 'boolean',
        ]);

        // Crear el template
        $template = ChecklistTemplate::create([
            'name' => $request->name,
            'description' => $request->description,
            'service_type_id' => $request->service_type_id,
            'is_active' => true,
        ]);

        // Crear las etapas y sus items
        foreach ($request->stages as $stageIndex => $stageData) {
            $stage = ChecklistStage::create([
                'checklist_template_id' => $template->id,
                'name' => $stageData['name'],
                'description' => $stageData['description'] ?? null,
                'order_index' => $stageIndex,
                'is_required' => $stageData['is_required'] ?? true,
            ]);

            foreach ($stageData['items'] as $itemIndex => $itemData) {
                ChecklistItem::create([
                    'checklist_template_id' => $template->id,
                    'checklist_stage_id' => $stage->id,
                    'title' => $itemData['title'],
                    'description' => $itemData['description'] ?? null,
                    'type' => $itemData['type'],
                    'options' => $itemData['type'] === 'select' && !empty($itemData['options']) 
                        ? explode(',', $itemData['options']) 
                        : null,
                    'is_required' => $itemData['is_required'] ?? true,
                    'order' => $itemIndex, // Para compatibilidad con el campo anterior
                    'order_index' => $itemIndex,
                ]);
            }
        }

        return redirect()->route('admin.checklist-templates.index')
            ->with('success', 'Template de checklist creado exitosamente');
    }

    public function show(ChecklistTemplate $checklistTemplate)
    {
        $this->checkSuperAdmin();
        
        $checklistTemplate->load(['serviceType', 'stages.items']);
        
        return view('admin.checklist-templates.show-staged', compact('checklistTemplate'));
    }

    public function edit(ChecklistTemplate $checklistTemplate)
    {
        $this->checkSuperAdmin();
        
        $checklistTemplate->load('items');
        $serviceTypes = ServiceType::where('is_active', true)->get();
        
        return view('admin.checklist-templates.edit', compact('checklistTemplate', 'serviceTypes'));
    }

    public function update(Request $request, ChecklistTemplate $checklistTemplate)
    {
        $this->checkSuperAdmin();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_type_id' => 'required|exists:service_types,id',
            'is_active' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.title' => 'required|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.type' => 'required|in:text,number,select,checkbox,file',
            'items.*.options' => 'nullable|string',
            'items.*.is_required' => 'boolean',
        ]);

        $checklistTemplate->update([
            'name' => $request->name,
            'description' => $request->description,
            'service_type_id' => $request->service_type_id,
            'is_active' => $request->is_active ?? true,
        ]);

        // Eliminar items existentes
        ChecklistItem::where('checklist_template_id', $checklistTemplate->id)->delete();

        // Crear nuevos items
        foreach ($request->items as $index => $item) {
            ChecklistItem::create([
                'title' => $item['title'],
                'description' => $item['description'] ?? null,
                'type' => $item['type'],
                'options' => $item['type'] === 'select' && !empty($item['options']) 
                    ? json_encode(explode(',', $item['options'])) 
                    : null,
                'is_required' => $item['is_required'] ?? false,
                'order' => $index + 1,
                'checklist_template_id' => $checklistTemplate->id,
            ]);
        }

        return redirect()->route('admin.checklist-templates.index')
            ->with('success', 'Template de checklist actualizado exitosamente');
    }

    public function destroy(ChecklistTemplate $checklistTemplate)
    {
        $this->checkSuperAdmin();
        
        $checklistTemplate->delete();

        return redirect()->route('admin.checklist-templates.index')
            ->with('success', 'Template de checklist eliminado exitosamente');
    }

    public function duplicate(ChecklistTemplate $checklistTemplate)
    {
        $this->checkSuperAdmin();
        
        $newTemplate = ChecklistTemplate::create([
            'name' => $checklistTemplate->name . ' (Copia)',
            'description' => $checklistTemplate->description,
            'service_type_id' => $checklistTemplate->service_type_id,
            'is_active' => false,
        ]);

        foreach ($checklistTemplate->items as $item) {
            ChecklistItem::create([
                'title' => $item->title,
                'description' => $item->description,
                'type' => $item->type,
                'options' => $item->options,
                'is_required' => $item->is_required,
                'order' => $item->order,
                'checklist_template_id' => $newTemplate->id,
            ]);
        }

        return redirect()->route('admin.checklist-templates.edit', $newTemplate)
            ->with('success', 'Template duplicado exitosamente. Puedes editarlo ahora.');
    }
}
