<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Password — GameTradeHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Rajdhani', sans-serif; }
        .font-game { font-family: 'Orbitron', monospace; }
        .bg-grid {
            background-image:
                linear-gradient(rgba(99,102,241,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99,102,241,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .glow-box {
            box-shadow: 0 0 0 1px rgba(99,102,241,0.3),
                        0 0 30px rgba(99,102,241,0.1),
                        inset 0 0 30px rgba(0,0,0,0.3);
        }
        .glow-btn { box-shadow: 0 0 20px rgba(99,102,241,0.4); transition: all 0.3s; }
        .glow-btn:hover { box-shadow: 0 0 40px rgba(99,102,241,0.7); transform: translateY(-1px); }
        .input-game {
            background: rgba(15,15,30,0.8);
            border: 1px solid rgba(99,102,241,0.3);
            color: #e2e8f0; transition: all 0.3s;
        }
        .input-game:focus {
            outline: none;
            border-color: rgba(99,102,241,0.8);
            box-shadow: 0 0 15px rgba(99,102,241,0.2);
        }
        .input-game::placeholder { color: rgba(148,163,184,0.4); }
    </style>
</head>
<body class="min-h-screen bg-gray-950 bg-grid flex items-center justify-center">

    <div class="fixed top-6 left-6 w-6 h-6 border-t-2 border-l-2 border-indigo-500/40"></div>
    <div class="fixed top-6 right-6 w-6 h-6 border-t-2 border-r-2 border-indigo-500/40"></div>
    <div class="fixed bottom-6 left-6 w-6 h-6 border-b-2 border-l-2 border-indigo-500/40"></div>
    <div class="fixed bottom-6 right-6 w-6 h-6 border-b-2 border-r-2 border-indigo-500/40"></div>

    <div class="w-full max-w-sm mx-4">

        <div class="text-center mb-6">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center
                            justify-center text-white font-game font-bold text-sm">G</div>
                <span class="font-game text-xl font-bold tracking-wider">
                    <span class="text-indigo-400">GAME</span>
                    <span class="text-white">TRADE</span>
                    <span class="text-indigo-400">HUB</span>
                </span>
            </a>
            <div class="text-xs text-indigo-400/60 tracking-[0.3em] uppercase">
                🔐 Set New Password
            </div>
        </div>

        <div class="glow-box rounded-2xl p-6 relative"
             style="background: rgba(10,10,20,0.9); backdrop-filter: blur(20px);">

            <div class="absolute top-0 left-0 w-4 h-4 border-t border-l border-indigo-500/50 rounded-tl-2xl"></div>
            <div class="absolute top-0 right-0 w-4 h-4 border-t border-r border-indigo-500/50 rounded-tr-2xl"></div>
            <div class="absolute bottom-0 left-0 w-4 h-4 border-b border-l border-indigo-500/50 rounded-bl-2xl"></div>
            <div class="absolute bottom-0 right-0 w-4 h-4 border-b border-r border-indigo-500/50 rounded-br-2xl"></div>

            <h2 class="font-game text-lg font-bold text-white mb-1 tracking-wider">
                NEW PASSWORD
            </h2>
            <p class="text-xs text-gray-500 mb-5">
                Choose a strong password for your account.
            </p>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label class="block text-xs font-semibold text-gray-400
                                  tracking-widest uppercase mb-1.5">Email</label>
                    <input type="email" name="email"
                           value="{{ old('email', $request->email) }}"
                           required
                           class="input-game w-full rounded-xl px-4 py-2.5 text-sm">
                    @error('email')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-400
                                  tracking-widest uppercase mb-1.5">New Password</label>
                    <input type="password" name="password"
                           required placeholder="Min 8 characters"
                           class="input-game w-full rounded-xl px-4 py-2.5 text-sm">
                    @error('password')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-400
                                  tracking-widest uppercase mb-1.5">Confirm Password</label>
                    <input type="password" name="password_confirmation"
                           required placeholder="Repeat password"
                           class="input-game w-full rounded-xl px-4 py-2.5 text-sm">
                </div>

                <button type="submit"
                        class="glow-btn w-full bg-indigo-600 hover:bg-indigo-500
                               text-white font-game font-bold tracking-widest
                               text-xs py-3 rounded-xl uppercase">
                    🔐 Update Password
                </button>

            </form>
        </div>
    </div>
</body>
</html>
