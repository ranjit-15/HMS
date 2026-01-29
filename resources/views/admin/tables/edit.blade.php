@extends('admin.layout')

@section('title', 'Edit Table')
@section('header', 'Edit Table')

@section('content')
<form method="POST" action="{{ route('admin.tables.update', $table) }}" class="bg-white shadow rounded-lg p-6 space-y-4">
    @csrf
    @method('PUT')
    @include('admin.tables._form')
</form>
@endsection
