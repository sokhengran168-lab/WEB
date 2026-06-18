@extends('layouts.app')
@section('title', 'My Profile')


@section('content')
<div class="max-w-2xl mx-auto px-4 py-8 space-y-4">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-2">
        <div>
            <h1 class="text-xl font-bold tracking-tight">My Profile</h1>
            <p class="text-gray-500 text-xs mt-0.5">Complete your profile to build trust with buyers</p>
        </div>
        @if(auth()->user()->profile_completed)
            <span class="inline-flex items-center gap-1.5 bg-green-500/10 border border-green-500/20 text-green-400 px-3 py-1 rounded-full text-xs font-semibold">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                Verified
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 bg-yellow-500/10 border border-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-xs font-semibold">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                Incomplete
            </span>
        @endif
    </div>

    <form id="profileForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        {{-- Avatar + Stats --}}
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-5">
            <div class="flex items-center gap-5">

                {{-- Avatar --}}
                <div class="relative group shrink-0">
                    <img
                        src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=fff&background=6366f1' }}"
                        class="w-20 h-20 rounded-full object-cover border-2 border-gray-700 transition duration-200 group-hover:border-indigo-500"
                        id="avatarPreview"
                    >
                    <label for="avatarInput"
                           class="absolute inset-0 flex items-center justify-center bg-black/60 opacity-0 group-hover:opacity-100 rounded-full cursor-pointer transition duration-200">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </label>
                    <input type="file" name="avatar" id="avatarInput" class="hidden" accept="image/*" onchange="previewAvatar(event)">
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-base truncate">{{ $user->name }}</div>
                    <div class="text-gray-500 text-xs">{{ $user->username }}</div>

                    <div class="flex gap-4 mt-3 text-xs">
                        <div class="text-center">
                            <div class="font-bold text-white text-sm">{{ $user->total_sales ?? 0 }}</div>
                            <div class="text-gray-500">Sales</div>
                        </div>
                        <div class="w-px bg-gray-800"></div>
                        <div class="text-center">
                            <div class="font-bold text-white text-sm">{{ $user->reviews_count ?? 0 }}</div>
                            <div class="text-gray-500">Reviews</div>
                        </div>
                    </div>
                </div>

                {{-- Remove avatar --}}
                @if($user->avatar)
                    <button type="button" onclick="removeAvatar()"
                            class="text-xs text-red-400 hover:text-red-300 transition shrink-0">
                        Remove
                    </button>
                @endif
            </div>
            <input type="hidden" name="remove_avatar" id="removeAvatarInput" value="0">
        </div>

        {{-- Personal Information --}}
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-5 space-y-4">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Personal</p>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Display Name <span class="text-indigo-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                    @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Username <span class="text-indigo-400">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm select-none">@</span>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}"
                               class="w-full bg-gray-800 border border-gray-700 rounded-xl pl-7 pr-3 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                    </div>
                    @error('username')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Full Name <span class="text-gray-600 font-normal">(as per ID)</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}"
                           placeholder="Legal name"
                           class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Email</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           class="w-full bg-gray-800/40 border border-gray-800 rounded-xl px-3 py-2.5 text-sm text-gray-600 cursor-not-allowed">
                </div>
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1.5 font-medium">Date of Birth</label>
                <input type="date" name="date_of_birth"
                       value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                       max="{{ now()->subYears(13)->format('Y-m-d') }}"
                       class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                @error('date_of_birth')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Contact --}}
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-5 space-y-4">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Contact</p>
                <p class="text-xs text-gray-600 mt-0.5">Only shown to buyers after a completed purchase</p>
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1.5 font-medium">Phone Number</label>
                <input type="text" name="phone_number"
                       value="{{ old('phone_number', $user->phone_number) }}"
                       placeholder="+1 123 456 789"
                       class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
            </div>
        </div>

        {{-- Messengers --}}
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-5 space-y-3">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Messengers</p>
                <p class="text-xs text-gray-600 mt-0.5">Add at least one so buyers can reach you directly</p>
            </div>

            {{-- Telegram --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1.5 font-medium">Telegram</label>
                <div class="flex items-center bg-gray-800 border border-gray-700 rounded-xl overflow-hidden focus-within:border-sky-500 transition">
                    <span class="px-3 text-gray-500 text-xs whitespace-nowrap border-r border-gray-700 py-2.5 select-none">t.me/</span>
                    <input type="text" name="telegram"
                           value="{{ old('telegram', $user->telegram) }}"
                           placeholder="yourusername"
                           class="flex-1 bg-transparent px-3 py-2.5 text-sm text-white focus:outline-none">
                </div>
            </div>

            {{-- WhatsApp --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1.5 font-medium">WhatsApp</label>
                <div class="flex items-center bg-gray-800 border border-gray-700 rounded-xl overflow-hidden focus-within:border-green-500 transition">
                    <span class="px-3 py-2.5 border-r border-gray-700 select-none">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </span>
                    <input type="text" name="whatsapp"
                           value="{{ old('whatsapp', $user->whatsapp) }}"
                           placeholder="60123456789"
                           class="flex-1 bg-transparent px-3 py-2.5 text-sm text-white focus:outline-none">
                </div>
            </div>

            {{-- Discord --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1.5 font-medium">Discord</label>
                <div class="flex items-center bg-gray-800 border border-gray-700 rounded-xl overflow-hidden focus-within:border-indigo-500 transition">
                    <span class="px-3 py-2.5 border-r border-gray-700 select-none">
                        <svg class="w-4 h-4 text-indigo-400" fill="currentColor" viewBox="0 0 24 24"><path d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg>
                    </span>
                    <input type="text" name="discord"
                           value="{{ old('discord', $user->discord) }}"
                           placeholder="username or username#0000"
                           class="flex-1 bg-transparent px-3 py-2.5 text-sm text-white focus:outline-none">
                </div>
            </div>

            {{-- Line --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1.5 font-medium">Line ID</label>
                <div class="flex items-center bg-gray-800 border border-gray-700 rounded-xl overflow-hidden focus-within:border-green-500 transition">
                    <span class="px-3 py-2.5 border-r border-gray-700 select-none">
                        <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 24 24"><path d="M19.952 12.617c0-4.267-4.279-7.738-9.537-7.738-5.257 0-9.537 3.471-9.537 7.738 0 3.826 3.391 7.028 7.974 7.636.31.067.733.204.84.469.096.241.063.618.031.861l-.136.817c-.042.241-.192.943.825.514 1.017-.429 5.49-3.233 7.492-5.537 1.381-1.519 2.048-3.062 2.048-4.76z"/></svg>
                    </span>
                    <input type="text" name="line_id"
                           value="{{ old('line_id', $user->line_id) }}"
                           placeholder="your_line_id"
                           class="flex-1 bg-transparent px-3 py-2.5 text-sm text-white focus:outline-none">
                </div>
            </div>
        </div>

        {{-- Save --}}
        <div class="flex justify-end">
            <button type="submit" id="saveBtn"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Save Profile
            </button>
        </div>

    </form>

    {{-- Change Password --}}
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-5">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Change Password</p>

        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs text-gray-400 mb-1.5 font-medium">Current Password</label>
                <input id="current_password" type="password" name="current_password"
                       autocomplete="current-password" required
                       class="w-full bg-gray-800 border rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500 transition @error('current_password') border-red-500 @else border-gray-700 @enderror">
                @error('current_password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">New Password</label>
                    <div class="relative">
                        <input id="password" type="password" name="password"
                               autocomplete="new-password" required
                               class="w-full bg-gray-800 border rounded-xl px-3 py-2.5 pr-10 text-sm text-white focus:outline-none focus:border-indigo-500 transition @error('password') border-red-500 @else border-gray-700 @enderror">
                        <button type="button" onclick="togglePassword('password', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Confirm Password</label>
                    <div class="relative">
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               required
                               class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 pr-10 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                        <button type="button" onclick="togglePassword('password_confirmation', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 border border-gray-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Change Password
                </button>
            </div>
        </form>
    </div>

    {{-- Danger Zone --}}
    <div class="bg-red-950/30 border border-red-900/40 rounded-2xl p-5" x-data="{ show: false }">
        <p class="text-xs font-bold text-red-500/70 uppercase tracking-widest mb-3">Danger Zone</p>
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm font-semibold text-red-400">Delete Account</div>
                <div class="text-xs text-gray-600 mt-0.5">Permanently removes your account and all listings</div>
            </div>
            <button type="button" @click="show = !show"
                    class="text-xs text-red-400 border border-red-500/30 hover:bg-red-500/10 px-4 py-2 rounded-xl font-semibold transition">
                Delete
            </button>
        </div>

        <div x-show="show" x-transition class="mt-4 pt-4 border-t border-red-900/40">
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                <input type="password" name="password"
                       placeholder="Enter password to confirm"
                       class="w-full bg-gray-900 border border-red-500/30 rounded-xl px-3 py-2.5 text-sm text-white mb-3 focus:outline-none focus:border-red-500 transition">
                <button type="submit"
                        onclick="this.innerText='Deleting...'; this.disabled=true;"
                        class="w-full bg-red-600 hover:bg-red-500 text-white py-2.5 rounded-xl text-sm font-bold transition">
                    Yes, permanently delete my account
                </button>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.getElementById('profileForm').addEventListener('submit', function () {
        const btn = document.getElementById('saveBtn');
        btn.disabled = true;
        btn.innerHTML = `<svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Saving...`;
    });

    function previewAvatar(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = () => document.getElementById('avatarPreview').src = reader.result;
        reader.readAsDataURL(file);
        document.getElementById('removeAvatarInput').value = 0;
    }

    function removeAvatar() {
        const defaultAvatar = @json(
            "https://ui-avatars.com/api/?name=" . urlencode($user->name) . "&color=fff&background=6366f1"
        );
        document.getElementById('avatarPreview').src = defaultAvatar;
        document.getElementById('avatarInput').value = '';
        document.getElementById('removeAvatarInput').value = 1;
    }

    function togglePassword(id, btn) {
        const input = document.getElementById(id);
        const hidden = input.type === 'password';
        input.type = hidden ? 'text' : 'password';
        // swap the eye icon path
        const paths = btn.querySelectorAll('path');
        if (hidden) {
            paths[0].setAttribute('d', 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21');
            paths[1]?.remove();
        } else {
            paths[0].setAttribute('d', 'M15 12a3 3 0 11-6 0 3 3 0 016 0z');
            if (!paths[1]) {
                const newPath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                newPath.setAttribute('stroke-linecap', 'round');
                newPath.setAttribute('stroke-linejoin', 'round');
                newPath.setAttribute('d', 'M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z');
                btn.querySelector('svg').appendChild(newPath);
            }
        }
    }
</script>
@endpush
