@extends('layouts.app')

@section('content')
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h1 class="text-3xl font-bold text-gray-900">Welcome to the Knowledge Base</h1>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Find answers, guides, and documentation
        </p>
    </div>
    <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div class="bg-blue-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900">Browse Articles</h3>
                <p class="mt-2 text-sm text-gray-600">
                    Explore our comprehensive collection of articles and guides
                </p>
                <a href="{{ route('articles.index') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                    View Articles &rarr;
                </a>
            </div>
            <div class="bg-green-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900">Browse Categories</h3>
                <p class="mt-2 text-sm text-gray-600">
                    Navigate through organized topics and categories
                </p>
                <a href="{{ route('categories.index') }}" class="mt-4 inline-block text-green-600 hover:text-green-800">
                    View Categories &rarr;
                </a>
            </div>
            <div class="bg-purple-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900">Search</h3>
                <p class="mt-2 text-sm text-gray-600">
                    Find exactly what you're looking for quickly
                </p>
                <form action="{{ route('articles.index') }}" method="GET" class="mt-4">
                    <input type="text" name="search" placeholder="Search articles..." class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
