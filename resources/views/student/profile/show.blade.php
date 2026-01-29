@extends('layouts.student')

@section('title', 'Profile')
@section('header', 'Your Profile')
@section('subheader', 'Manage your account settings')

@section('content')
    <div class="grid gap-6 md:grid-cols-3">
        {{-- Avatar Section --}}
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm p-6">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">Profile Picture</h2>
            
            <div class="flex flex-col items-center space-y-4">
                {{-- Current Avatar --}}
                <div class="relative">
                    @if($user->avatar_path)
                        <img 
                            id="current-avatar" 
                            src="{{ Storage::url($user->avatar_path) }}" 
                            alt="{{ $user->name }}" 
                            class="h-32 w-32 rounded-full object-cover border-4 border-slate-200"
                        />
                    @else
                        <div 
                            id="current-avatar-placeholder" 
                            class="h-32 w-32 rounded-full bg-indigo-100 flex items-center justify-center text-4xl font-semibold text-indigo-600 border-4 border-slate-200"
                        >
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                {{-- Upload Form --}}
                <form 
                    id="avatar-form" 
                    action="{{ route('student.profile.avatar') }}" 
                    method="POST" 
                    enctype="multipart/form-data"
                    class="w-full"
                >
                    @csrf
                    
                    {{-- Dropzone --}}
                    <div 
                        id="avatar-dropzone" 
                        class="border-2 border-dashed border-slate-300 rounded-lg p-4 text-center cursor-pointer hover:border-indigo-400 hover:bg-slate-50 transition-colors"
                    >
                        <input 
                            type="file" 
                            name="avatar" 
                            id="avatar-input" 
                            accept="image/jpeg,image/png,image/jpg"
                            class="hidden"
                        />
                        <div class="text-sm text-slate-600">
                            <span class="font-medium text-indigo-600">Click to upload</span> or drag and drop
                        </div>
                        <div class="text-xs text-slate-500 mt-1">PNG or JPG (max 2MB)</div>
                    </div>

                    {{-- Preview --}}
                    <div id="avatar-preview-container" class="hidden mt-4">
                        <img id="avatar-preview" src="" alt="Preview" class="h-24 w-24 rounded-full object-cover mx-auto border-2 border-indigo-300" />
                        <p class="text-xs text-center text-slate-500 mt-2">Preview</p>
                    </div>

                    {{-- Error Message --}}
                    <div id="avatar-error" class="hidden mt-2 text-sm text-red-600 text-center"></div>

                    {{-- Submit Button --}}
                    <button 
                        type="submit" 
                        id="avatar-submit"
                        class="hidden mt-4 w-full rounded bg-indigo-600 px-4 py-2 text-white text-sm font-medium hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Upload Avatar
                    </button>
                </form>

                {{-- Remove Avatar --}}
                @if($user->avatar_path)
                    <form action="{{ route('student.profile.avatar.remove') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800 hover:underline">
                            Remove avatar
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Profile Info Section --}}
        <div class="md:col-span-2 rounded-lg border border-slate-200 bg-white shadow-sm p-6">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">Profile Information</h2>
            
            <form action="{{ route('student.profile.update') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name', $user->name) }}"
                        class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        required
                    />
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        value="{{ $user->email }}"
                        class="w-full rounded border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-500 cursor-not-allowed"
                        disabled
                    />
                    <p class="mt-1 text-xs text-slate-500">Email is managed by Google and cannot be changed.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Member Since</label>
                    <p class="text-sm text-slate-600">{{ $user->created_at->format('F j, Y') }}</p>
                </div>

                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="rounded bg-indigo-600 px-4 py-2 text-white text-sm font-medium hover:bg-indigo-700"
                    >
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
    const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/jpg'];

    const dropzone = document.getElementById('avatar-dropzone');
    const input = document.getElementById('avatar-input');
    const preview = document.getElementById('avatar-preview');
    const previewContainer = document.getElementById('avatar-preview-container');
    const errorEl = document.getElementById('avatar-error');
    const submitBtn = document.getElementById('avatar-submit');

    // Click dropzone to trigger file input
    dropzone.addEventListener('click', () => input.click());

    // File input change
    input.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) handleFile(file);
    });

    // Drag and drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => {
            dropzone.classList.add('border-indigo-500', 'bg-indigo-50');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => {
            dropzone.classList.remove('border-indigo-500', 'bg-indigo-50');
        });
    });

    dropzone.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            input.files = files;
            handleFile(files[0]);
        }
    });

    function handleFile(file) {
        // Reset
        errorEl.classList.add('hidden');
        previewContainer.classList.add('hidden');
        submitBtn.classList.add('hidden');

        // Validate type
        if (!ALLOWED_TYPES.includes(file.type)) {
            showError('Please upload a JPG or PNG image.');
            input.value = '';
            return;
        }

        // Validate size
        if (file.size > MAX_FILE_SIZE) {
            showError('File size must be less than 2MB.');
            input.value = '';
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            previewContainer.classList.remove('hidden');
            submitBtn.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    function showError(message) {
        errorEl.textContent = message;
        errorEl.classList.remove('hidden');
    }
});
</script>
@endpush
