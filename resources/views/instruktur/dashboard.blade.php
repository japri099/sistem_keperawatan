@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Dashboard Instruktur</h1>
        <p>Selamat datang di dashboard instruktur.</p>
        <form action="{{ route('logout') }}" method="POST" class="inline">
    @csrf
    <button type="submit" class="btn btn-danger">Logout</button>
</form>

    </div>
@endsection
