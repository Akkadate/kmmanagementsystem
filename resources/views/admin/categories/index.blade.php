@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Manage Categories</h1>
        <p class="mt-2 text-gray-600">Organize articles into hierarchical categories</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Create Category
    </a>
</div>

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Articles</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sort Order</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($categories as $category)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($category->parent_id)
                                <span class="text-gray-400 mr-2">└─</span>
                            @endif
                            <span class="text-sm font-medium text-gray-900">{{ $category->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $category->slug }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $category->parent ? $category->parent->name : '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                            {{ $category->articles_count }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $category->sort_order }}</td>
                    <td class="px-6 py-4 text-right text-sm font-medium space-x-3">
                        <a href="{{ route('categories.show', $category->slug) }}" class="text-blue-600 hover:underline" target="_blank">
                            View
                        </a>
                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="text-indigo-600 hover:underline">
                            Edit
                        </a>
                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <p class="mb-4">No categories found</p>
                        <a href="{{ route('admin.categories.create') }}" class="text-blue-600 hover:underline">Create your first category</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($categories->count() > 0)
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="font-medium text-blue-900 mb-2">Category Hierarchy Tips:</h3>
        <ul class="list-disc list-inside text-sm text-blue-800 space-y-1">
            <li>Parent categories appear first, followed by their subcategories (indicated with └─)</li>
            <li>Use sort_order to control the display sequence within the same level</li>
            <li>Categories with articles cannot be deleted until articles are reassigned</li>
            <li>Categories with subcategories must have their children deleted or reassigned first</li>
        </ul>
    </div>
@endif
@endsection
