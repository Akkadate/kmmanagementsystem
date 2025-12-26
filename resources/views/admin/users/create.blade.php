@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create User</h1>
        <p class="mt-2 text-gray-600">Add a new user account with role assignment</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

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
                        Full Name <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                    <p class="mt-1 text-sm text-gray-500">Minimum 8 characters</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm Password <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        Role <span class="text-red-600">*</span>
                    </label>
                    <select
                        id="role"
                        name="role"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                        <option value="">Select a role</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin - Full system access</option>
                        <option value="editor" {{ old('role') == 'editor' ? 'selected' : '' }}>Editor - Can publish articles</option>
                        <option value="contributor" {{ old('role') == 'contributor' ? 'selected' : '' }}>Contributor - Can create articles (needs approval)</option>
                        <option value="viewer" {{ old('role') == 'viewer' ? 'selected' : '' }}>Viewer - Read-only access</option>
                    </select>
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Department
                    </label>
                    <select
                        id="department_id"
                        name="department_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">No department assigned</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Assign user to specific department (optional)</p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-blue-900 mb-2">Role Permissions:</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li><strong>Admin:</strong> Full system access, user management, all CRUD operations</li>
                        <li><strong>Editor:</strong> Publish articles, manage categories/tags, approve contributor drafts</li>
                        <li><strong>Contributor:</strong> Create articles (saved as drafts), edit own articles</li>
                        <li><strong>Viewer:</strong> Read articles, bookmark, provide feedback (no creation)</li>
                    </ul>
                </div>
            </div>

            <div class="flex items-center justify-between mt-6 pt-6 border-t">
                <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-800">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
