@extends('layouts.app')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Top Articles</h1>
            <p class="mt-2 text-gray-600">Most viewed articles in your knowledge base</p>
        </div>
        <a href="{{ route('admin.analytics.dashboard') }}" class="text-gray-600 hover:text-gray-800">
            ← Back to Analytics
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">Rank</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Article</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Helpful</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($topArticles as $index => $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center">
                            @if($index < 3)
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full
                                    {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $index === 1 ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $index === 2 ? 'bg-orange-100 text-orange-800' : '' }}
                                    font-bold">
                                    {{ $index + 1 }}
                                </span>
                            @else
                                <span class="text-gray-600 font-medium">{{ $index + 1 }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $item['article']['title'] }}</div>
                        @if($item['article']['excerpt'])
                            <div class="text-sm text-gray-500">{{ Str::limit($item['article']['excerpt'], 80) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $item['article']['author']['name'] }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $item['article']['category']['name'] }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">{{ number_format($item['view_count']) }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($item['helpful_ratio'] !== null)
                            <span class="px-2 py-1 text-xs font-medium rounded
                                {{ $item['helpful_ratio'] >= 75 ? 'bg-green-100 text-green-800' : '' }}
                                {{ $item['helpful_ratio'] >= 50 && $item['helpful_ratio'] < 75 ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $item['helpful_ratio'] < 50 ? 'bg-red-100 text-red-800' : '' }}">
                                {{ $item['helpful_ratio'] }}%
                            </span>
                        @else
                            <span class="text-sm text-gray-400">No feedback</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium">
                        <a href="{{ route('articles.show', $item['article']['slug']) }}"
                           class="text-blue-600 hover:underline"
                           target="_blank">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        No articles found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(count($topArticles) > 0)
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex items-start">
        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div class="text-sm text-blue-800">
            <p class="font-medium">Top performing articles</p>
            <p class="mt-1">These articles have the highest view counts. Consider creating similar content or updating them with fresh information to maintain their popularity.</p>
        </div>
    </div>
</div>
@endif
@endsection
