@extends('admin.layout')

@section('title', 'Add Closure')
@section('header', 'Add Closure')

@section('content')
<form method="POST" action="{{ route('admin.closures.store') }}" class="bg-white shadow rounded-lg p-6 space-y-4">
    @include('admin.closures._form')
</form>
@endsection
