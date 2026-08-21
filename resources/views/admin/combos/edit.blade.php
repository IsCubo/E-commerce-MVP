@extends('layouts.admin')

@section('title', 'Editar Combo')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Editar Combo: {{ $combo->name }}</h3>
            </div>
            <form action="{{ route('combos.update', $combo) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nombre</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name', $combo->name) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="price">Precio del Combo</label>
                                <input type="number" step="0.01" class="form-control" name="price" value="{{ old('price', $combo->price) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="image">Imagen</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                                        <label class="custom-file-label" for="image">Elegir archivo</label>
                                    </div>
                                </div>
                                @if($combo->image_path)
                                    <div class="mt-2">
                                        <img src="{{ upload_url($combo->image_path) }}" alt="Imagen actual" style="max-height: 100px;">
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="description">Descripción</label>
                                <textarea class="form-control" name="description" rows="3">{{ old('description', $combo->description) }}</textarea>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ old('is_active', $combo->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Activo</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Productos del Combo</label>
                            <div id="products-container">
                                @forelse ($combo->products as $index => $comboProduct)
                                    <div class="row product-row mb-2">
                                        <div class="col-8">
                                            <select name="products[{{ $index }}][id]" class="form-control product-select" required>
                                                <option value="">Seleccione Producto...</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" {{ $product->id == $comboProduct->id ? 'selected' : '' }}>{{ $product->name }} (Normal: ${{ $product->price }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-3">
                                            <input type="number" name="products[{{ $index }}][quantity]" class="form-control" value="{{ $comboProduct->pivot->quantity }}" min="1" required placeholder="Cant.">
                                        </div>
                                        <div class="col-1">
                                            <button type="button" class="btn btn-danger btn-sm remove-row" {{ $loop->first ? 'disabled' : '' }}><i class="fas fa-trash"></i></button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="row product-row mb-2">
                                        <div class="col-8">
                                            <select name="products[0][id]" class="form-control product-select" required>
                                                <option value="">Seleccione Producto...</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }} (Normal: ${{ $product->price }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-3">
                                            <input type="number" name="products[0][quantity]" class="form-control" value="1" min="1" required placeholder="Cant.">
                                        </div>
                                        <div class="col-1">
                                            <button type="button" class="btn btn-danger btn-sm remove-row" disabled><i class="fas fa-trash"></i></button>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                            <button type="button" class="btn btn-success btn-sm mt-2" id="add-product"><i class="fas fa-plus"></i> Agregar Producto</button>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                    <a href="{{ route('combos.index') }}" class="btn btn-default float-right">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let productIndex = {{ $combo->products->count() > 0 ? $combo->products->count() : 1 }};

        document.getElementById('add-product').addEventListener('click', function() {
            const container = document.getElementById('products-container');
            const row = document.createElement('div');
            row.className = 'row product-row mb-2';
            row.innerHTML = `
                <div class="col-8">
                    <select name="products[${productIndex}][id]" class="form-control product-select" required>
                        <option value="">Seleccione Producto...</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} (Normal: ${{ $product->price }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-3">
                    <input type="number" name="products[${productIndex}][quantity]" class="form-control" value="1" min="1" required placeholder="Cant.">
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                </div>
            `;
            container.appendChild(row);
            productIndex++;
        });

        document.getElementById('products-container').addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                e.target.closest('.row').remove();
            }
        });
    });
</script>
@endsection
