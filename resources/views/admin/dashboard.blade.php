@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Products -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['products'] }}</h3>
                <p>Total Productos</p>
            </div>
            <div class="icon">
                <i class="fas fa-box"></i>
            </div>
            <a href="{{ route('products.index') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- Offers -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['offers'] }}</h3>
                <p>En Oferta</p>
            </div>
            <div class="icon">
                <i class="fas fa-percent"></i>
            </div>
            <a href="{{ route('products.index') }}?offer=1" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- Combos -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['combos'] }}</h3>
                <p>Combos Activos</p>
            </div>
            <div class="icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <a href="{{ route('combos.index') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- Categories -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['categories'] }}</h3>
                <p>Categorías</p>
            </div>
            <div class="icon">
                <i class="fas fa-tags"></i>
            </div>
            <a href="{{ route('categories.index') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>
@endsection
