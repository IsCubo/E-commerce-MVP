@extends('layouts.admin')

@section('title', 'Nuevo Producto')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Crear Producto</h3>
            </div>
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nombre</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="category_id">Categoría</label>
                                <select class="form-control" name="category_id" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="price">Precio Normal</label>
                                <input type="number" step="0.01" class="form-control" name="price" value="{{ old('price') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="discount_price">Precio con Descuento (Opcional)</label>
                                <input type="number" step="0.01" class="form-control" name="discount_price" value="{{ old('discount_price') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="description">Descripción</label>
                                <textarea class="form-control" name="description" rows="5">{{ old('description') }}</textarea>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_offer" name="is_offer" {{ old('is_offer') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_offer">Es Oferta</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" checked>
                                    <label class="custom-control-label" for="is_active">Activo</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="images">Imágenes (Máx 3)</label>
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
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('products.index') }}" class="btn btn-default float-right">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
