@extends('layouts.app')

@section('title', 'Agropecuaria Venezuela - Soluciones Integrales para el Campo')

@section('content')
    @include('components.hero')
    @include('components.trust-banner')
    @include('components.categories-grid')
    @include('components.featured-products')
    @include('components.newsletter-cta')
@endsection