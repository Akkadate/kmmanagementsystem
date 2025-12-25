@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Category</h1>
        <p class="mt-2 text-gray-600">Update category information</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Name <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $category->name) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                    <p class="mt-1 text-sm text-gray-500">The display name for this category</p>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="A brief description of what this category contains"
                    >{{ old('description', $category->description) }}</textarea>
                </div>

                <div>
                    <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Parent Category
                    </label>
                    <select
                        id="parent_id"
                        name="parent_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">None (Root Category)</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Select a parent category to create a subcategory</p>
                </div>

                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                        Sort Order
                    </label>
                    <input
                        type="number"
                        id="sort_order"
                        name="sort_order"
                        value="{{ old('sort_order', $category->sort_order) }}"
                        min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    <p class="mt-1 text-sm text-gray-500">Lower numbers appear first (default: 0)</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Category Information</h3>
                    <dl class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Slug:</dt>
                            <dd class="text-gray-900 font-mono">{{ $category->slug }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Articles:</dt>
                            <dd class="text-gray-900">{{ $category->articles()->count() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Subcategories:</dt>
                            <dd class="text-gray-900">{{ $category->children()->count() }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="flex items-center justify-between mt-6 pt-6 border-t">
                <a href="{{ route('admin.categories.index') }}" class="text-gray-600 hover:text-gray-800">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Update Category
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
