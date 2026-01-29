@extends('admin.layout')

@section('title', 'Edit Closure')
@section('header', 'Edit Closure')

@section('content')
<form method="POST" action="{{ route('admin.closures.update', $closure) }}" class="bg-white shadow rounded-lg p-6 space-y-4">
    @csrf
    @method('PUT')
    @include('admin.closures._form')
</form>
@endsection
