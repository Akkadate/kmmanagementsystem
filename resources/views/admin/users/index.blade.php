@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Manage Users</h1>
        <p class="mt-2 text-gray-600">Create and manage user accounts with role assignments</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Create User
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

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center space-x-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Search by name or email...">
        </div>
        <div>
            <select name="role" class="px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="editor" {{ request('role') == 'editor' ? 'selected' : '' }}>Editor</option>
                <option value="contributor" {{ request('role') == 'contributor' ? 'selected' : '' }}>Contributor</option>
                <option value="viewer" {{ request('role') == 'viewer' ? 'selected' : '' }}>Viewer</option>
            </select>
        </div>
        <div>
            <select name="department" class="px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Filter
        </button>
        <a href="{{ route('admin.users.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
            Clear
        </a>
    </form>
</div>

<!-- Users Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Articles</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-semibold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-xs font-medium rounded-full
                            @if($user->role === 'admin') bg-red-100 text-red-800
                            @elseif($user->role === 'editor') bg-purple-100 text-purple-800
                            @elseif($user->role === 'contributor') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        @if($user->department)
                            <span class="px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded">
                                {{ $user->department->name }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $user->articles()->count() }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium space-x-3">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="text-indigo-600 hover:underline">
                            Edit
                        </a>
                        @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        No users found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($users->hasPages())
    <div class="mt-6">
        {{ $users->links() }}
    </div>
@endif
@endsection
