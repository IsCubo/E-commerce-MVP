@extends('layouts.admin')

@section('title', 'Editar Producto')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Editar Producto: {{ $product->name }}</h3>
            </div>
            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nombre</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name', $product->name) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="category_id">Categoría</label>
                                <select class="form-control" name="category_id" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="price">Precio Normal</label>
                                <input type="number" step="0.01" class="form-control" name="price" value="{{ old('price', $product->price) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="discount_price">Precio con Descuento (Opcional)</label>
                                <input type="number" step="0.01" class="form-control" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}">
                            </div>
                            <div class="form-group">
                                <label for="stock">Stock (unidades disponibles)</label>
                                <input type="number" step="1" min="0" class="form-control" name="stock" value="{{ old('stock', $product->stock) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="description">Descripción</label>
                                <textarea class="form-control" name="description" rows="5">{{ old('description', $product->description) }}</textarea>
                            </div>
                            <!-- ... switches ... -->
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_offer" name="is_offer" {{ old('is_offer', $product->is_offer) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_offer">Es Oferta</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Activo</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="images">Agregar Nuevas Imágenes</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="images" name="images[]" multiple accept="image/*">
                                        <label class="custom-file-label" for="images">Elegir archivos</label>
                                    </div>
                                </div>
                                <div id="image-preview-container" class="row mt-3"></div>
                            </div>
                        </div>
                    </div>

                    @if($product->images->isNotEmpty())
                        <hr>
                        <h5>Imágenes Actuales</h5>
                        <div class="row">
                            @foreach($product->images as $image)
                                <div class="col-md-3 text-center mb-3">
                                    <img src="{{ upload_url($image->image_path) }}" class="img-thumbnail" style="height: 150px; object-fit: cover;">
                                    <div class="mt-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="del_{{ $image->id }}" name="delete_images[]" value="{{ $image->id }}">
                                            <label class="custom-control-label text-danger" for="del_{{ $image->id }}">Eliminar</label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                    <a href="{{ route('products.index') }}" class="btn btn-default float-right">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
