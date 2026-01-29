@extends('admin.layout')

@section('title', 'Push Notifications')
@section('header', 'Push Notifications')

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <!-- Send Notification Form -->
    <div class="lg:col-span-1">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-[#8b0000] to-[#bf4040] px-5 py-4">
                <h2 class="font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Send Notification
                </h2>
            </div>
            <form method="POST" action="{{ route('admin.notifications.store') }}" class="p-5 space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Target</label>
                    <select name="target" id="target" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[#8b0000] focus:ring-1 focus:ring-[#8b0000]/20 focus:outline-none"
                            onchange="toggleUserSelect()">
                        <option value="all">📢 All Students (Broadcast)</option>
                        <option value="individual">👤 Individual Student</option>
                    </select>
                </div>

                <div id="user-select-container" class="hidden">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Select Student</label>
                        <select name="user_id" id="user_id"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[#8b0000] focus:ring-1 focus:ring-[#8b0000]/20 focus:outline-none">
                        <option value="">-- Select Student --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Type</label>
                        <select name="type" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[#8b0000] focus:ring-1 focus:ring-[#8b0000]/20 focus:outline-none">
                        <option value="info">ℹ️ Information</option>
                        <option value="success">✅ Success</option>
                        <option value="warning">⚠️ Warning</option>
                        <option value="urgent">🚨 Urgent</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Title</label>
                          <input type="text" name="title" required maxlength="255"
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[#8b0000] focus:ring-1 focus:ring-[#8b0000]/20 focus:outline-none"
                           placeholder="Notification title...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Message</label>
                    <textarea name="message" required rows="4" maxlength="2000"
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[#8b0000] focus:ring-1 focus:ring-[#8b0000]/20 focus:outline-none resize-none"
                              placeholder="Write your message here..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Expires At (Optional)</label>
                          <input type="datetime-local" name="expires_at"
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[#8b0000] focus:ring-1 focus:ring-[#8b0000]/20 focus:outline-none">
                    <p class="text-xs text-slate-500 mt-1">Leave empty for permanent notification</p>
                </div>

                <div class="rounded-lg bg-blue-50 border border-blue-200 p-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="send_email" value="1" 
                               class="rounded border-slate-300 text-[#8b0000] focus:ring-[#8b0000] w-5 h-5">
                        <div>
                            <span class="font-medium text-slate-700">📧 Also send via Email</span>
                            <p class="text-xs text-slate-500">Students will receive this notification in their inbox</p>
                        </div>
                    </label>
                </div>

                <button type="submit" 
                        class="w-full rounded-lg bg-[#8b0000] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#6b0000] transition-colors shadow-sm">
                    📤 Send Notification
                </button>
            </form>
        </div>
    </div>

    <!-- Notifications History -->
    <div class="lg:col-span-2">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="bg-slate-50 border-b border-slate-200 px-5 py-4">
                <h2 class="font-semibold text-slate-800">Sent Notifications</h2>
            </div>
            
            @if($notifications->isEmpty())
                <div class="p-8 text-center text-slate-500">
                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p>No notifications sent yet</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($notifications as $notification)
                        <div class="p-4 hover:bg-slate-50 transition-colors">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        @if($notification->type === 'info')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">ℹ️ Info</span>
                                        @elseif($notification->type === 'success')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">✅ Success</span>
                                        @elseif($notification->type === 'warning')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700">⚠️ Warning</span>
                                        @elseif($notification->type === 'urgent')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">🚨 Urgent</span>
                                        @endif
                                        
                                        @if($notification->is_broadcast)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-700">📢 Broadcast</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">👤 {{ $notification->targetUser->name ?? 'User' }}</span>
                                        @endif
                                    </div>
                                    
                                    <h3 class="font-semibold text-slate-800 text-sm">{{ $notification->title }}</h3>
                                    <p class="text-sm text-slate-600 mt-1 line-clamp-2">{{ $notification->message }}</p>
                                    
                                    <div class="flex items-center gap-3 mt-2 text-xs text-slate-500">
                                        <span>By {{ $notification->admin->name ?? 'Admin' }}</span>
                                        <span>•</span>
                                        <span>{{ $notification->created_at->diffForHumans() }}</span>
                                        @if($notification->expires_at)
                                            <span>•</span>
                                            <span class="{{ $notification->expires_at->isPast() ? 'text-red-500' : '' }}">
                                                {{ $notification->expires_at->isPast() ? 'Expired' : 'Expires ' . $notification->expires_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <form method="POST" action="{{ route('admin.notifications.destroy', $notification) }}" 
                                      onsubmit="return confirm('Delete this notification?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($notifications->hasPages())
                    <div class="px-5 py-4 border-t border-slate-100">
                        {{ $notifications->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<script>
function toggleUserSelect() {
    const target = document.getElementById('target').value;
    const container = document.getElementById('user-select-container');
    const userSelect = document.getElementById('user_id');
    
    if (target === 'individual') {
        container.classList.remove('hidden');
        userSelect.required = true;
    } else {
        container.classList.add('hidden');
        userSelect.required = false;
        userSelect.value = '';
    }
}
</script>
@endsection
