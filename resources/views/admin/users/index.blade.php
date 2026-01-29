@extends('admin.layout')

@section('title', 'User Management')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-800">User Management</h1>
    </div>

    @if(session('status'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or email..."
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
            </div>
            <select name="role" class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">All Roles</option>
                <option value="student" {{ $role === 'student' ? 'selected' : '' }}>Students</option>
                <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admins</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Search
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">User</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Role</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 uppercase">Borrows</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 uppercase">Bookings</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Joined</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($users as $user)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($user->avatar_path)
                                    <img src="{{ asset('storage/' . $user->avatar_path) }}" class="w-8 h-8 rounded-full object-cover" />
                                @else
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-blue-600 font-medium text-sm">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-slate-800">{{ $user->name }}</p>
                                    <p class="text-sm text-slate-500">{{ $user->email }}</p>
                                </div>
                                @if($user->is_banned)
                                    <span class="px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded-full">Banned</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-slate-600">{{ $user->borrow_requests_count }}</td>
                        <td class="px-4 py-3 text-center text-sm text-slate-600">{{ $user->bookings_count }}</td>
                        <td class="px-4 py-3 text-sm text-slate-500">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View
                                </a>
                                @if($user->role !== 'admin')
                                    <form method="POST" action="{{ route('admin.users.toggleBan', $user) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-sm font-medium {{ $user->is_banned ? 'text-green-600 hover:text-green-800' : 'text-red-600 hover:text-red-800' }}">
                                            {{ $user->is_banned ? 'Unban' : 'Ban' }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-200">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
