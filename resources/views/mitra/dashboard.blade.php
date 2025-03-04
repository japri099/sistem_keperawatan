@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Dashboard Mitra</h1>
        <p>Selamat datang di dashboard mitra.</p>
        <form action="{{ route('logout') }}" method="POST" class="inline">
    @csrf
    <button type="submit" class="btn btn-danger">Logout</button>
</form>

    </div>
@endsection
