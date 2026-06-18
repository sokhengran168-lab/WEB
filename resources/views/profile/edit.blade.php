@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">My Profile</h1>
            <p class="text-gray-400 text-sm mt-1">
                Complete your profile to build trust with buyers
            </p>
        </div>
        @if(auth()->user()->profile_completed)
        <span class="bg-green-500/10 border border-green-500/25 text-green-400
                     px-3 py-1.5 rounded-full text-xs font-bold">
            Profile Complete
        </span>
        @else
        <span class="bg-yellow-500/10 border border-yellow-500/25 text-yellow-400
                     px-3 py-1.5 rounded-full text-xs font-bold">
            Profile Incomplete
        </span>
        @endif
    </div>

    <form id="profileForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 mb-5">
            <div class="flex items-center gap-6">

<!-- Avatar -->
<div class="relative group">
    <img
        src="{{ $user->avatar ? $user->avatar : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
        class="w-24 h-24 rounded-full object-cover border-2 border-gray-700
               transition duration-300 hover:scale-105"
        id="avatarPreview"
    >

    <!-- Hover overlay -->
    <label for="avatarInput"
           class="absolute inset-0 flex items-center justify-center
                  bg-black/50 opacity-0 group-hover:opacity-100
                  rounded-full cursor-pointer text-xs text-white font-semibold">
        <i class="fa-solid fa-camera mr-1"></i>
        Change
    </label>

    <input type="file" name="avatar" id="avatarInput" class="hidden"
           onchange="previewAvatar(event)">
</div>

                <!-- Profile Info -->
                <div class="flex-1">
                    <div class="flex items-center gap-3">
                        <h2 class="text-lg font-bold">
                            {{ $user->name }}
                        </h2>

                        @if($user->profile_completed)
                            <span class="text-xs bg-green-500/20 text-green-400 px-2 py-1 rounded-full">
                                <i class="fa-solid fa-circle-check text-[10px] mr-1"></i>
                                Verified
                            </span>
                        @endif
                    </div>

                    <p class="text-sm text-gray-400">
                        @ {{ $user->username }}
                    </p>

                    <p class="text-xs text-gray-500 mt-1">
                        {{ $user->country ?? 'No country set' }}
                    </p>

                    <!-- Quick stats -->
                    <div class="flex gap-4 mt-3 text-xs text-gray-400">
                        <div>
                            <span class="font-bold text-white">{{ $user->total_sales ?? 0 }}</span> Sales
                        </div>
                        <div>
                            <span class="font-bold text-white">{{ $user->reviews_count ?? 0 }}</span> Reviews
                        </div>
                    </div>

                    <!-- Remove button -->
                    @if($user->avatar)
                        <button type="button"
                                onclick="removeAvatar()"
                                class="text-red-400 text-xs mt-2 hover:underline">
                            Remove photo
                        </button>
                    @endif
                </div>
            </div>

            <!-- Hidden -->
            <input type="hidden" name="remove_avatar" id="removeAvatarInput" value="0">
        </div>

        {{-- Personal Information --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 mb-4">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">
                Personal Information
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">
                        Display Name *
                    </label>
                    <input type="text" name="name"
                           value="{{ old('name', $user->name) }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-xl
                                  px-3 py-2.5 text-sm text-white
                                  focus:outline-none focus:border-indigo-500">
                    @error('name')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">
                        Username *
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2
                                     text-gray-500 text-sm">@</span>
                        <input type="text" name="username"
                               value="{{ old('username', $user->username) }}"
                               class="w-full bg-gray-800 border border-gray-700 rounded-xl
                                      pl-7 pr-3 py-2.5 text-sm text-white
                                      focus:outline-none focus:border-indigo-500">
                    </div>
                    @error('username')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">
                        Full Name
                        <span class="text-gray-600 font-normal">(as per ID)</span>
                    </label>
                    <input type="text" name="full_name"
                           value="{{ old('full_name', $user->full_name) }}"
                           placeholder="Your legal full name"
                           class="w-full bg-gray-800 border border-gray-700 rounded-xl
                                  px-3 py-2.5 text-sm text-white
                                  focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">
                        Email Address
                    </label>
                    <input type="email" value="{{ $user->email }}" disabled
                           class="w-full bg-gray-800/50 border border-gray-700 rounded-xl
                                  px-3 py-2.5 text-sm text-gray-500 cursor-not-allowed">
                    <p class="text-xs text-gray-600 mt-1">Email cannot be changed</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">
                        Date of Birth
                    </label>
                    <input type="date" name="date_of_birth"
                           value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                           max="{{ now()->subYears(13)->format('Y-m-d') }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-xl
                                  px-3 py-2.5 text-sm text-white
                                  focus:outline-none focus:border-indigo-500">
                    @error('date_of_birth')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">
                        Country
                    </label>
                    <x-country-select
                        name="country"
                        :selected="old('country', $user->country ?? '')" />
                </div>
            </div>
        </div>

        {{-- Contact Information --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 mb-4">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                Contact Information
            </div>
            <p class="text-xs text-gray-500 mb-4">
                Phone number is only shown to buyers after a completed purchase.
            </p>

            <div>
                <label class="block text-xs font-semibold text-gray-400 mb-1.5">
                    Phone Number
                </label>
                <div class="flex gap-2">
                    <x-country-select
                        name="phone_country_code"
                        :selected="old('phone_country_code', $user->phone_country_code ?? '')" />
                    <input type="text" name="phone_number"
                           value="{{ old('phone_number', $user->phone_number) }}"
                           placeholder="123456789"
                           class="flex-1 bg-gray-800 border border-gray-700 rounded-xl
                                  px-3 py-2.5 text-sm text-white
                                  focus:outline-none focus:border-indigo-500">
                </div>
            </div>
        </div>

        {{-- Instant Messenger --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 mb-4">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                Instant Messenger
            </div>
            <p class="text-xs text-gray-500 mb-4">
                Add at least one so buyers can contact you directly.
            </p>

            <div class="flex flex-col gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">
                        Telegram
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2
                                     text-gray-500 text-sm">t.me/</span>
                        <input type="text" name="telegram"
                               value="{{ old('telegram', $user->telegram) }}"
                               placeholder="yourusername"
                               class="w-full bg-gray-800 border border-gray-700 rounded-xl
                                      pl-14 pr-3 py-2 text-sm text-white
                                      focus:outline-none focus:border-sky-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">
                        WhatsApp
                    </label>
                    <input type="text" name="whatsapp"
                           value="{{ old('whatsapp', $user->whatsapp) }}"
                           placeholder="60123456789"
                           class="w-full bg-gray-800 border border-gray-700 rounded-xl
                                  px-3 py-2 text-sm text-white
                                  focus:outline-none focus:border-green-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">
                        Discord
                    </label>
                    <input type="text" name="discord"
                           value="{{ old('discord', $user->discord) }}"
                           placeholder="username or username#0000"
                           class="w-full bg-gray-800 border border-gray-700 rounded-xl
                                  px-3 py-2 text-sm text-white
                                  focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">
                        Line ID
                    </label>
                    <input type="text" name="line_id"
                           value="{{ old('line_id', $user->line_id) }}"
                           placeholder="your_line_id"
                           class="w-full bg-gray-800 border border-gray-700 rounded-xl
                                  px-3 py-2 text-sm text-white
                                  focus:outline-none focus:border-green-600">
                </div>
            </div>
        </div>

        <div class="flex justify-end mb-4">
            <button type="submit"
                class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white
                px-6 py-2.5 rounded-xl font-semibold text-sm transition shadow"
                id="saveBtn">

                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span>Save Profile</span>
            </button>
        </div>


    </form>

    {{-- Change Password --}}
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-sm">

        <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-5">
            Change Password
        </h2>

        <form method="POST" action="{{ route('profile.password') }}" class="space-y-5">
    @csrf
    @method('PATCH')

    {{-- Current Password --}}
    <div>
        <label for="current_password" class="block text-xs font-semibold text-gray-400 mb-1.5">
            Current Password
        </label>

        <div class="relative">
            <input
                id="current_password"
                type="password"
                name="current_password"
                autocomplete="current-password"
                required
                class="w-full bg-gray-800 border rounded-xl px-3 py-2.5 pr-10 text-sm text-white
                       focus:outline-none focus:ring-2 focus:ring-indigo-500 transition
                       @error('current_password') border-red-500 @else border-gray-700 @enderror">

            <span class="absolute right-3 top-2.5 text-gray-500 text-xs">🔒</span>
        </div>

        @error('current_password')
            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Password Fields --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- New Password --}}
        <div>
            <label for="password" class="block text-xs font-semibold text-gray-400 mb-1.5">
                New Password
            </label>

            <div class="relative">
                <input
                    id="password"
                    type="password"
                    name="password"
                    autocomplete="new-password"
                    required
                    class="w-full bg-gray-800 border rounded-xl px-3 py-2.5 pr-10 text-sm text-white
                           focus:outline-none focus:ring-2 focus:ring-indigo-500 transition
                           @error('password') border-red-500 @else border-gray-700 @enderror">

                <button type="button"
                        onclick="togglePassword('password', this)"
                        class="absolute right-3 top-2.5 text-gray-400 hover:text-white text-xs">
                    👁
                </button>
            </div>

            {{-- Optional strength indicator --}}
            <div id="passwordStrength" class="text-xs mt-1 text-gray-500"></div>

            @error('password')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-gray-400 mb-1.5">
                Confirm Password
            </label>

            <div class="relative">
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    class="w-full bg-gray-800 border border-gray-700 rounded-xl
                           px-3 py-2.5 pr-10 text-sm text-white
                           focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">

                <button type="button"
                        onclick="togglePassword('password_confirmation', this)"
                        class="absolute right-3 top-2.5 text-gray-400 hover:text-white text-xs">
                    👁
                </button>
            </div>
        </div>

    </div>

    {{-- Submit --}}
    <div class="flex justify-end">
        <button type="submit"
            class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500
                   text-white px-5 py-2.5 rounded-xl text-sm font-semibold
                   transition shadow-md hover:shadow-lg"
            id="passwordBtn">

            🔄 <span>Change Password</span>
        </button>
    </div>
</form>
    </div>


    {{-- Danger Zone --}}
    <div class="bg-red-500/5 border border-red-500/20 rounded-xl p-5"
         x-data="{ show: false }">
        <div class="text-xs font-bold text-red-400 uppercase tracking-wider mb-3">
            Danger Zone
        </div>
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm font-semibold">Delete Account</div>
                <div class="text-xs text-gray-500">
                    Permanently delete your account and all data
                </div>
            </div>
            <button type="button" @click="show = !show"
                    class="bg-red-600/20 hover:bg-red-600/40 text-red-400
                           border border-red-500/30 px-4 py-2
                           rounded-xl text-sm font-bold transition">
                Delete Account
            </button>
        </div>
        <div x-show="show"
             x-transition
             class="mt-4 pt-4 border-t border-red-500/20">
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                <input type="password" name="password"
                       placeholder="Enter your password to confirm"
                       class="w-full bg-gray-800 border border-red-500/30 rounded-xl
                              px-3 py-2 text-sm text-white mb-3
                              focus:outline-none focus:border-red-500">
                <button type="submit"
                        onclick="this.innerText='Saving...'; this.disabled=true;"
                        class="w-full bg-red-600 hover:bg-red-500 text-white
                               py-2 rounded-xl text-sm font-bold transition">
                    Yes, Delete My Account Permanently
                </button>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('profileForm');
        if (form) {
            form.addEventListener('submit', function () {
                const btn = document.getElementById('saveBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = 'Saving...';
                }
            });
        }
    });

    function previewAvatar(event) {
        const reader = new FileReader();
        reader.onload = function () {
            document.getElementById('avatarPreview').src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);

        document.getElementById('removeAvatarInput').value = 0;
    }

    function togglePassword(id, btn) {
        const input = document.getElementById(id);
        const isHidden = input.type === "password";
        input.type = isHidden ? "text" : "password";
        btn.innerText = isHidden ? "🙈" : "👁";
    }




    function removeAvatar() {
        const defaultAvatar = @json(
            "https://ui-avatars.com/api/?name=" . urlencode($user->name) . "&color=fff&background=6366f1"
        );

        document.getElementById('avatarPreview').src = defaultAvatar;
        document.getElementById('avatarInput').value = "";
        document.getElementById('removeAvatarInput').value = 1;
    }

    document.getElementById('profileForm')
    .addEventListener('submit', function () {
        const btn = document.getElementById('saveBtn');
        btn.disabled = true;
        btn.innerHTML = 'Saving...';
    });
</script>
@endpush
