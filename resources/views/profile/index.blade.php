@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
        <p class="mt-2 text-gray-600">Manage your account and preferences</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="text-center">
                    <div class="mx-auto h-24 w-24 rounded-full bg-blue-100 flex items-center justify-center mb-4">
                        <span class="text-3xl font-bold text-blue-800">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ auth()->user()->name }}</h2>
                    <p class="text-gray-600">{{ auth()->user()->email }}</p>
                    <span class="mt-2 inline-block px-3 py-1 rounded-full text-sm font-medium
                        @if(auth()->user()->role === 'admin') bg-red-100 text-red-800
                        @elseif(auth()->user()->role === 'editor') bg-purple-100 text-purple-800
                        @elseif(auth()->user()->role === 'contributor') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </div>

                <div class="mt-6 space-y-2">
                    <a href="{{ route('profile.edit') }}" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Edit Profile
                    </a>
                    <a href="{{ route('bookmarks.index') }}" class="block w-full text-center px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                        My Bookmarks
                    </a>
                </div>
            </div>

            <!-- Member Info -->
            <div class="bg-white shadow rounded-lg p-6 mt-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Member Information</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Member Since:</dt>
                        <dd class="text-gray-900 font-medium">{{ auth()->user()->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Email Verified:</dt>
                        <dd class="text-gray-900 font-medium">{{ auth()->user()->email_verified_at ? 'Yes' : 'No' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Articles Created:</dt>
                        <dd class="text-gray-900 font-medium">{{ auth()->user()->articles()->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Bookmarks:</dt>
                        <dd class="text-gray-900 font-medium">{{ auth()->user()->bookmarks()->count() }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="lg:col-span-2">
            <!-- My Articles -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">My Articles</h3>
                @php
                    $myArticles = auth()->user()->articles()->latest()->limit(5)->get();
                @endphp

                @if($myArticles->count() > 0)
                    <div class="space-y-4">
                        @foreach($myArticles as $article)
                            <div class="border-b pb-4 last:border-b-0">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <a href="{{ route('articles.show', $article->slug) }}" class="text-lg font-medium text-gray-900 hover:text-blue-600">
                                            {{ $article->title }}
                                        </a>
                                        <div class="flex items-center mt-1 text-sm text-gray-600">
                                            <span class="px-2 py-0.5 rounded text-xs font-medium
                                                {{ $article->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($article->status) }}
                                            </span>
                                            <span class="mx-2">•</span>
                                            <span>{{ $article->view_count }} views</span>
                                            <span class="mx-2">•</span>
                                            <span>{{ $article->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('articles.edit', $article->slug) }}" class="ml-4 text-blue-600 hover:underline text-sm">Edit</a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(auth()->user()->articles()->count() > 5)
                        <div class="mt-4 text-center">
                            <a href="{{ route('articles.index', ['author_id' => auth()->id()]) }}" class="text-blue-600 hover:underline">
                                View all articles ({{ auth()->user()->articles()->count() }})
                            </a>
                        </div>
                    @endif
                @else
                    <p class="text-gray-500">You haven't created any articles yet.</p>
                    <a href="{{ route('articles.create') }}" class="mt-2 inline-block text-blue-600 hover:underline">
                        Create your first article
                    </a>
                @endif
            </div>

            <!-- Recent Bookmarks -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Recent Bookmarks</h3>
                    <a href="{{ route('bookmarks.index') }}" class="text-blue-600 hover:underline text-sm">View all</a>
                </div>

                @php
                    $recentBookmarks = auth()->user()->bookmarks()->with('article')->latest()->limit(5)->get();
                @endphp

                @if($recentBookmarks->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentBookmarks as $bookmark)
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
                                </svg>
                                <div class="flex-1">
                                    <a href="{{ route('articles.show', $bookmark->article->slug) }}" class="text-gray-900 hover:text-blue-600">
                                        {{ $bookmark->article->title }}
                                    </a>
                                    <p class="text-xs text-gray-500 mt-1">Bookmarked {{ $bookmark->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">You haven't bookmarked any articles yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
