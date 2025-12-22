@extends('layouts.app-tec')

@section('content')

    <!-- Etapa 2: Productos Aplicados -->
    <div class="stage-title">Productos Aplicados</div>
    <div class="stage-instruction">{{ $stageInstruction ?? 'Seleccione el producto utilizado para este servicio' }}</div>

    @php
        $isViewingAsTechnician = (session('view_as_technician', false) && auth()->check() && auth()->user()->hasRole('super-admin'))
            || request()->is('admin/technician-view/*')
            || (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/admin/technician-view/') !== false);
        $submitRoute = $isViewingAsTechnician ? route('admin.technician-view.service.checklist.submit', $service) : route('technician.service.checklist.submit', $service);
    @endphp
    <form method="POST" action="{{ $submitRoute }}" data-stage="products">
        <input type="hidden" name="stage" value="products">
        @csrf
        <input type="hidden" name="next_stage"
            value="{{ $service->service_type === 'sanitizacion' ? 'observations' : 'results' }}">
        <input type="hidden" name="current_stage" value="products">

        <div class="radio-group">
            @if($products && $products->count() > 0)
                @foreach($products as $index => $product)
                    <div class="radio-item">
                        <input type="radio" name="applied_product"
                            value="{{ $product->name }} ({{ $product->active_ingredient }}){{ $product->sag_registration ? ' (' . $product->sag_registration . ')' : '' }}"
                            {{ ($service->checklist_data["products"]["applied_product"] ?? "") === $product->name . ' (' . $product->active_ingredient . ')' . ($product->sag_registration ? ' (' . $product->sag_registration . ')' : '') ? "checked" : "" }} id="product_{{ $product->id }}" data-product-id="{{ $product->id }}">
                        <input type="hidden" name="product_id" value="{{ $product->id }}" class="product-id-field" disabled>
                        <label for="product_{{ $product->id }}">
                            <div class="product-info">
                                <div class="product-name">{{ $product->name }}</div>
                                <div class="product-details">
                                    <span class="active-ingredient">{{ $product->active_ingredient }}</span>
                                    @if($product->sag_registration)
                                        <span class="registration">{{ $product->sag_registration }}</span>
                                    @endif
                                    @if($product->isp_registration)
                                        <span class="registration">{{ $product->isp_registration }}</span>
                                    @endif
                                </div>
                                <div class="stock-info">
                                    Stock disponible: {{ $product->stock }} {{ $product->unit }}
                                </div>
                                @if($product->description)
                                    <div class="product-description">{{ $product->description }}</div>
                                @endif
                            </div>
                        </label>
                    </div>
                @endforeach
            @else
                <div class="no-products-message">
                    @php
                        $serviceTypeNames = [
                            'desratizacion' => 'Desratización',
                            'desinsectacion' => 'Desinsectación',
                            'sanitizacion' => 'Sanitización',
                            'fumigacion-de-jardines' => 'Fumigación de Jardines',
                            'servicios-especiales' => 'Servicios Especiales'
                        ];
                    @endphp
                    <p>No hay productos disponibles para el servicio de:
                        <strong>{{ $serviceTypeNames[$service->service_type] ?? ucfirst(str_replace('-', ' ', $service->service_type)) }}</strong>
                    </p>
                    <p>Contacte al administrador para agregar productos a este tipo de servicio.</p>
                </div>
            @endif
        </div>

        @if (in_array($service->service_type, ['desinfeccion', 'sanitizacion', 'desinsectacion', 'fumigacion-de-jardines']))

            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    Dosis y agua aplicadas
                </h2>

                <div class="flex gap-4 mb-2">
                    <!-- Campo Dosis -->
                    <div class="flex-1 " style="padding-top: 12px;">
                        <div class="flex  gap-2">
                            <input type="number" name="dosis" id="dosis"
                                value="{{ old('dosis', $service->checklist_data['products']['dosis'] ?? '') }}"
                                placeholder="Ej: 500" step="0.01" style="height: 40px; width: 100px;" class="form-control">
                            <span class="text-gray-600 font-medium">cc</span>/
                            <input type="number" name="agua" id="agua"
                                value="{{ old('agua', $service->checklist_data['products']['agua'] ?? '') }}"
                                placeholder="Ej: 10" step="0.01" class="form-control" style="height: 40px; width: 100px;">
                            <span class="text-gray-600 font-medium">Litros de agua</span>
                            @error('dosis')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            @error('agua')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>
        @endif

        <div class="buttons-container">
            @if($service->service_type === 'desratizacion')
                <a href="{{ route("technician.service.checklist.stage", ["service" => $service, "stage" => "points"]) }}"
                    class="back-button">
                    <span class="arrow">←</span> Anterior
                </a>
            @endif
            @if($products && $products->count() > 0)
                <button type="submit" class="next-button">
                    Siguiente <span class="arrow">→</span>
                </button>
            @else
                <button type="button" class="next-button disabled" disabled>
                    Sin productos disponibles
                </button>
            @endif
        </div>
    </form>

    <style>
        .product-info {
            text-align: left;
            width: 100%;
        }

        .product-name {
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 5px;
            color: #2c5530;
        }

        .product-details {
            margin-bottom: 5px;
        }

        .active-ingredient {
            color: #666;
            font-style: italic;
        }

        .registration {
            background-color: #e8f5e8;
            color: #2c5530;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.85em;
            margin-left: 8px;
        }

        .stock-info {
            color: #059669;
            font-size: 0.9em;
            font-weight: 500;
            margin-top: 5px;
        }

        .product-description {
            color: #666;
            font-size: 0.9em;
            margin-top: 5px;
            font-style: italic;
        }

        .no-products-message {
            text-align: center;
            padding: 2rem;
            background-color: #fef3cd;
            border: 1px solid #fecaca;
            border-radius: 8px;
            color: #92400e;
        }

        .no-products-message p {
            margin-bottom: 0.5rem;
        }

        .no-products-message strong {
            color: #7c2d12;
        }

        .next-button.disabled {
            background-color: #d1d5db;
            color: #9ca3af;
            cursor: not-allowed;
        }

        .radio-item {
            margin-bottom: 1rem;
        }

        .radio-item label {
            display: block;
            width: 100%;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .radio-item input[type="radio"]:checked+label {
            border-color: #2c5530;
            background-color: #f0f9f0;
        }

        .radio-item label:hover {
            border-color: #9ca3af;
            background-color: #f9fafb;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radioButtons = document.querySelectorAll('input[name="applied_product"]');

            radioButtons.forEach(function (radio) {
                radio.addEventListener('change', function () {
                    // Deshabilitar todos los campos product_id
                    document.querySelectorAll('.product-id-field').forEach(function (field) {
                        field.disabled = true;
                    });

                    // Habilitar solo el campo product_id del producto seleccionado
                    if (this.checked) {
                        const productIdField = this.parentElement.querySelector('.product-id-field');
                        if (productIdField) {
                            productIdField.disabled = false;
                        }
                    }
                });
            });

            // Al cargar la página, habilitar el campo del producto ya seleccionado
            const checkedRadio = document.querySelector('input[name="applied_product"]:checked');
            if (checkedRadio) {
                const productIdField = checkedRadio.parentElement.querySelector('.product-id-field');
                if (productIdField) {
                    productIdField.disabled = false;
                }
            }
        });
    </script>

@endsection