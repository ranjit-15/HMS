@extends('admin.layout')

@section('title', 'Add Book')
@section('header', 'Add Book')

@section('content')
    <form method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data"
        class="bg-white shadow rounded-lg p-6 space-y-4">
        @include('admin.books._form')
    </form>
@endsection