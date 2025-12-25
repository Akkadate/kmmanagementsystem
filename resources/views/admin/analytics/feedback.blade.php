@extends('layouts.app')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Feedback Statistics</h1>
            <p class="mt-2 text-gray-600">See how helpful your articles are to users</p>
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Article</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Helpful</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Not Helpful</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($feedbackStats as $stat)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $stat['article']['title'] }}</div>
                        @if($stat['article']['excerpt'])
                            <div class="text-sm text-gray-500">{{ Str::limit($stat['article']['excerpt'], 60) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $stat['article']['category']['name'] }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">{{ $stat['helpful_count'] }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M18 9.5a1.5 1.5 0 11-3 0v-6a1.5 1.5 0 013 0v6zM14 9.667v-5.43a2 2 0 00-1.105-1.79l-.05-.025A4 4 0 0011.055 2H5.64a2 2 0 00-1.962 1.608l-1.2 6A2 2 0 004.44 12H8v4a2 2 0 002 2 1 1 0 001-1v-.667a4 4 0 01.8-2.4l1.4-1.866a4 4 0 00.8-2.4z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">{{ $stat['not_helpful_count'] }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $stat['total_feedback'] }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex flex-col items-center">
                            <span class="text-lg font-bold
                                {{ $stat['helpful_percentage'] >= 75 ? 'text-green-600' : '' }}
                                {{ $stat['helpful_percentage'] >= 50 && $stat['helpful_percentage'] < 75 ? 'text-yellow-600' : '' }}
                                {{ $stat['helpful_percentage'] < 50 ? 'text-red-600' : '' }}">
                                {{ $stat['helpful_percentage'] }}%
                            </span>
                            <div class="w-full max-w-[100px] bg-gray-200 rounded-full h-2 mt-1">
                                <div class="h-2 rounded-full
                                    {{ $stat['helpful_percentage'] >= 75 ? 'bg-green-600' : '' }}
                                    {{ $stat['helpful_percentage'] >= 50 && $stat['helpful_percentage'] < 75 ? 'bg-yellow-600' : '' }}
                                    {{ $stat['helpful_percentage'] < 50 ? 'bg-red-600' : '' }}"
                                    style="width: {{ $stat['helpful_percentage'] }}%">
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium">
                        <a href="{{ route('articles.show', $stat['article']['slug']) }}"
                           class="text-blue-600 hover:underline"
                           target="_blank">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        No feedback data available yet
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(count($feedbackStats) > 0)
<div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Best Rated -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"></path>
            </svg>
            <div class="text-sm text-green-800">
                <p class="font-medium">Highly Rated (≥75%)</p>
                <p class="mt-1">{{ collect($feedbackStats)->where('helpful_percentage', '>=', 75)->count() }} articles</p>
            </div>
        </div>
    </div>

    <!-- Moderate -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div class="text-sm text-yellow-800">
                <p class="font-medium">Moderate (50-74%)</p>
                <p class="mt-1">{{ collect($feedbackStats)->whereBetween('helpful_percentage', [50, 74])->count() }} articles</p>
            </div>
        </div>
    </div>

    <!-- Needs Improvement -->
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-red-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M18 9.5a1.5 1.5 0 11-3 0v-6a1.5 1.5 0 013 0v6zM14 9.667v-5.43a2 2 0 00-1.105-1.79l-.05-.025A4 4 0 0011.055 2H5.64a2 2 0 00-1.962 1.608l-1.2 6A2 2 0 004.44 12H8v4a2 2 0 002 2 1 1 0 001-1v-.667a4 4 0 01.8-2.4l1.4-1.866a4 4 0 00.8-2.4z"></path>
            </svg>
            <div class="text-sm text-red-800">
                <p class="font-medium">Needs Review (<50%)</p>
                <p class="mt-1">{{ collect($feedbackStats)->where('helpful_percentage', '<', 50)->count() }} articles</p>
            </div>
        </div>
    </div>
</div>

<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex items-start">
        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div class="text-sm text-blue-800">
            <p class="font-medium">About Feedback Statistics</p>
            <p class="mt-1">Articles with low helpful ratings (below 50%) may need updating or clarification. Consider reviewing these articles and incorporating user feedback to improve their quality.</p>
        </div>
    </div>
</div>
@endif
@endsection
