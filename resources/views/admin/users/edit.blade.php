@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit User</h1>
        <p class="mt-2 text-gray-600">Update user information and role assignment</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
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
                        Full Name <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
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
                        value="{{ old('email', $user->email) }}"
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
                        @if($user->id === auth()->id()) disabled @endif
                    >
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin - Full system access</option>
                        <option value="editor" {{ old('role', $user->role) == 'editor' ? 'selected' : '' }}>Editor - Can publish articles</option>
                        <option value="contributor" {{ old('role', $user->role) == 'contributor' ? 'selected' : '' }}>Contributor - Can create articles</option>
                        <option value="viewer" {{ old('role', $user->role) == 'viewer' ? 'selected' : '' }}>Viewer - Read-only access</option>
                    </select>
                    @if($user->id === auth()->id())
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <p class="mt-1 text-sm text-yellow-600">You cannot change your own role</p>
                    @endif
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
                            <option value="{{ $department->id }}" {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Assign user to specific department (optional)</p>
                </div>

                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Change Password (Optional)</h3>
                    <p class="text-sm text-gray-600 mb-4">Leave blank to keep current password</p>

                    <div class="space-y-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                New Password
                            </label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <p class="mt-1 text-sm text-gray-500">Minimum 8 characters</p>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm New Password
                            </label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Email Verification</h3>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Current Status:</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    @if($user->email_verified_at)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Verified on {{ $user->email_verified_at->format('M d, Y H:i') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            Not Verified
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input
                                type="checkbox"
                                id="mark_email_verified"
                                name="mark_email_verified"
                                value="1"
                                {{ $user->email_verified_at ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            >
                        </div>
                        <div class="ml-3">
                            <label for="mark_email_verified" class="text-sm font-medium text-gray-700">
                                Mark Email as Verified
                            </label>
                            <p class="text-sm text-gray-500 mt-1">
                                Check this box to manually verify the user's email address.
                                @if($user->email_verified_at)
                                    Uncheck to revoke verification.
                                @else
                                    This will allow the user to access all features without email verification.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">User Statistics</h3>
                    <dl class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Articles Created:</dt>
                            <dd class="text-gray-900 font-medium">{{ $user->articles()->count() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Member Since:</dt>
                            <dd class="text-gray-900 font-medium">{{ $user->created_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="flex items-center justify-between mt-6 pt-6 border-t">
                <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-800">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
