@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Activity Log Details</h1>
                    <p class="text-gray-600 dark:text-gray-400">Detailed information about this specific activity log entry.</p>
                </div>
                <a href="{{ route('activity-logs.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Logs
                </a>
            </div>
        </div>

        <!-- Activity Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Basic Information Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        <svg class="w-6 h-6 inline mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Basic Information
                    </h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">ID</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $activityLog->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Action</dt>
                            <dd class="text-sm">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $activityLog->action === 'create' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300' : '' }}
                                    {{ $activityLog->action === 'update' ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300' : '' }}
                                    {{ $activityLog->action === 'delete' ? 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300' : '' }}
                                    {{ $activityLog->action === 'view' ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300' : '' }}
                                    {{ $activityLog->action === 'export' ? 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-300' : '' }}
                                    {{ $activityLog->action === 'import' ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-300' : '' }}
                                    {{ $activityLog->action === 'login' ? 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300' : '' }}
                                    {{ $activityLog->action === 'logout' ? 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300' : '' }}">
                                    {{ $activityLog->action_label }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Description</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $activityLog->description }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Created At</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $activityLog->created_at->format('M j, Y g:i:s A') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- User Information Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        <svg class="w-6 h-6 inline mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        User Information
                    </h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">User</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $activityLog->user->name ?? 'Unknown' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Email</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $activityLog->user->email ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">IP Address</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $activityLog->ip_address ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Session ID</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100 font-mono text-xs">{{ $activityLog->session_id ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Model Information Card -->
        @if($activityLog->model_type)
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    <svg class="w-6 h-6 inline mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Affected Model
                </h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Model Type</dt>
                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $activityLog->model_type_label }}</dd>
                    </div>
                    @if($activityLog->model_id)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Model ID</dt>
                        <dd class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $activityLog->model_id }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
        @endif

        <!-- Changes Card (for updates) -->
        @if($activityLog->action === 'update' && ($activityLog->old_values || $activityLog->new_values))
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    <svg class="w-6 h-6 inline mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Changes Made
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($activityLog->old_values)
                    <div>
                        <h4 class="text-md font-medium text-gray-700 dark:text-gray-200 mb-2">Previous Values</h4>
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded border border-gray-200 dark:border-gray-600">
                            <pre class="text-xs text-gray-600 dark:text-gray-300 overflow-x-auto">{{ json_encode($activityLog->old_values, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                    @endif
                    @if($activityLog->new_values)
                    <div>
                        <h4 class="text-md font-medium text-gray-700 dark:text-gray-200 mb-2">New Values</h4>
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded border border-gray-200 dark:border-gray-600">
                            <pre class="text-xs text-gray-600 dark:text-gray-300 overflow-x-auto">{{ json_encode($activityLog->new_values, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Created Data Card (for creates) -->
        @if($activityLog->action === 'create' && $activityLog->new_values)
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    <svg class="w-6 h-6 inline mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Created Data
                </h2>
                <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded border border-gray-200 dark:border-gray-600">
                    <pre class="text-xs text-gray-600 dark:text-gray-300 overflow-x-auto">{{ json_encode($activityLog->new_values, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
        </div>
        @endif

        <!-- Deleted Data Card (for deletes) -->
        @if($activityLog->action === 'delete' && $activityLog->old_values)
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    <svg class="w-6 h-6 inline mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Deleted Data
                </h2>
                <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded border border-gray-200 dark:border-gray-600">
                    <pre class="text-xs text-gray-600 dark:text-gray-300 overflow-x-auto">{{ json_encode($activityLog->old_values, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
        </div>
        @endif

        <!-- User Agent Card -->
        @if($activityLog->user_agent)
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    <svg class="w-6 h-6 inline mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    User Agent
                </h2>
                <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded border border-gray-200 dark:border-gray-600">
                    <p class="text-sm text-gray-600 dark:text-gray-300 break-all">{{ $activityLog->user_agent }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 