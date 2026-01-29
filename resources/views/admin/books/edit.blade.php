@extends('admin.layout')

@section('title', 'Edit Book')
@section('header', 'Edit Book')

@section('content')
    <form method="POST" action="{{ route('admin.books.update', $book) }}" enctype="multipart/form-data"
        class="bg-white shadow rounded-lg p-6 space-y-4">
        @method('PUT')
        @include('admin.books._form')
    </form>
@endsection