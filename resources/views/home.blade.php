@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="max-w-md mx-auto bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Bienvenido, {{ auth()->user()->name }}!</h2>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-semibold">
                Cerrar Sesión
            </button>
        </form>
    </div>
</div>
@endsection