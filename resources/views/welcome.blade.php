@extends('layouts.app')

<div class="fixed top-0 left-0 w-full z-[100] bg-yellow-400 text-black text-center font-black py-2 shadow-[0_020px#13ec13] border-b-4 border-primary animate-bounce">
    SI VES ESTA BARRA AMARILLA REBOTANDO, TAILWIND ESTÁ FUNCIONANDO
</div>

@section('title', 'Agropecuaria Venezuela - Soluciones Integrales para el Campo')

@section('content')
    @include('components.hero')
    @include('components.trust-banner')
    @include('components.categories-grid')
    @include('components.featured-products')
    @include('components.newsletter-cta')
@endsection