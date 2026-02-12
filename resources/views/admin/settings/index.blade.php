@extends('layouts.admin')

@section('title', 'Configuración General')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Editar Configuración</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="form-group">
                        <label for="brand_name">Nombre de la Marca</label>
                        <input type="text" class="form-control" id="brand_name" name="brand_name" value="{{ $settings['brand_name'] ?? '' }}" required>
                    </div>

                    <div class="form-group">
                        <label for="whatsapp_number">Número de WhatsApp</label>
                        <input type="text" class="form-control" id="whatsapp_number" name="whatsapp_number" value="{{ $settings['whatsapp_number'] ?? '' }}" required>
                        <small class="form-text text-muted">Incluye el código de país sin símbolos (ej: 573001234567)</small>
                    </div>

                    <div class="form-group">
                        <label for="welcome_message">Mensaje de Bienvenida (WhatsApp)</label>
                        <textarea class="form-control" id="welcome_message" name="welcome_message" rows="3">{{ $settings['welcome_message'] ?? '' }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="logo">Logo de la Empresa</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="logo" name="logo">
                                <label class="custom-file-label" for="logo">Elegir archivo</label>
                            </div>
                        </div>
                        @if(isset($settings['logo_path']) && $settings['logo_path'])
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $settings['logo_path']) }}" alt="Logo Actual" style="max-height: 100px;">
                            </div>
                        @endif
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
