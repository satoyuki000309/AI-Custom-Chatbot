@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Q&A Management</h1>
            <p class="text-gray-600 dark:text-gray-400">Manage your chatbot's knowledge base by adding, editing, or deleting Q&A pairs.</p>
        </div>

        <!-- Add New Q&A Button -->
        <div class="mb-6">
            <button onclick="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Q&A
            </button>
        </div>

        <!-- Q&A List -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Question</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Answer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($qnas ?? [] as $qna)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $qna->id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ Str::limit($qna->question, 50) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ Str::limit($qna->answer, 80) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="openEditModal({{ $qna->id }}, '{{ addslashes($qna->question) }}', '{{ addslashes($qna->answer) }}')" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="deleteQnA({{ $qna->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No Q&A pairs found. Add your first one!
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($qnas) && $qnas->hasPages())
                    <div class="mt-6">
                        {{ $qnas->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="qnaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 id="modalTitle" class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Add New Q&A</h3>
            <form id="qnaForm" method="POST">
                @csrf
                <input type="hidden" id="qnaId" name="qna_id">
                <input type="hidden" id="isEdit" name="_method" value="POST">
                
                <div class="mb-4">
                    <label for="question" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question</label>
                    <textarea id="question" name="question" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100" required></textarea>
                </div>
                
                <div class="mb-6">
                    <label for="answer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Answer</label>
                    <textarea id="answer" name="answer" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100" required></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Q&A';
    document.getElementById('qnaForm').action = '{{ route("qna.store") }}';
    document.getElementById('qnaForm').method = 'POST';
    document.getElementById('isEdit').value = 'POST';
    document.getElementById('question').value = '';
    document.getElementById('answer').value = '';
    document.getElementById('qnaModal').classList.remove('hidden');
}

function openEditModal(id, question, answer) {
    document.getElementById('modalTitle').textContent = 'Edit Q&A';
    document.getElementById('qnaForm').action = `/admin/qna/${id}`;
    document.getElementById('qnaForm').method = 'POST';
    document.getElementById('isEdit').value = 'PUT';
    document.getElementById('qnaId').value = id;
    document.getElementById('question').value = question;
    document.getElementById('answer').value = answer;
    document.getElementById('qnaModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('qnaModal').classList.add('hidden');
}

function deleteQnA(id) {
    if (confirm('Are you sure you want to delete this Q&A pair?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/qna/${id}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        
        form.appendChild(methodInput);
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modal when clicking outside
document.getElementById('qnaModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection
