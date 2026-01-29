@extends('admin.layout')

@section('title', 'Add Table')
@section('header', 'Add Table')

@section('content')
<form method="POST" action="{{ route('admin.tables.store') }}" class="bg-white shadow rounded-lg p-6 space-y-4">
    @include('admin.tables._form')
</form>
@endsection
