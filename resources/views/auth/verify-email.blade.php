<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email — GameTradeHub</title>
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
                        0 0 30px rgba(99,102,241,0.1);
        }
        .glow-btn { box-shadow: 0 0 20px rgba(99,102,241,0.4); transition: all 0.3s; }
        .glow-btn:hover { box-shadow: 0 0 40px rgba(99,102,241,0.7); transform: translateY(-1px); }
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 1; }
            100% { transform: scale(1.5); opacity: 0; }
        }
        .pulse-ring {
            animation: pulse-ring 2s ease-out infinite;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-950 bg-grid flex items-center justify-center">

    <div class="fixed top-6 left-6 w-6 h-6 border-t-2 border-l-2 border-indigo-500/40"></div>
    <div class="fixed top-6 right-6 w-6 h-6 border-t-2 border-r-2 border-indigo-500/40"></div>
    <div class="fixed bottom-6 left-6 w-6 h-6 border-b-2 border-l-2 border-indigo-500/40"></div>
    <div class="fixed bottom-6 right-6 w-6 h-6 border-b-2 border-r-2 border-indigo-500/40"></div>

    <div class="w-full max-w-sm mx-4">

        <div class="text-center mb-6">
            <div class="inline-flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center
                            justify-center text-white font-game font-bold text-sm">G</div>
                <span class="font-game text-xl font-bold tracking-wider">
                    <span class="text-indigo-400">GAME</span>
                    <span class="text-white">TRADE</span>
                    <span class="text-indigo-400">HUB</span>
                </span>
            </div>
        </div>

        <div class="glow-box rounded-2xl p-6 text-center relative"
             style="background: rgba(10,10,20,0.9); backdrop-filter: blur(20px);">

            <div class="absolute top-0 left-0 w-4 h-4 border-t border-l border-indigo-500/50 rounded-tl-2xl"></div>
            <div class="absolute top-0 right-0 w-4 h-4 border-t border-r border-indigo-500/50 rounded-tr-2xl"></div>
            <div class="absolute bottom-0 left-0 w-4 h-4 border-b border-l border-indigo-500/50 rounded-bl-2xl"></div>
            <div class="absolute bottom-0 right-0 w-4 h-4 border-b border-r border-indigo-500/50 rounded-br-2xl"></div>

            {{-- Animated email icon --}}
            <div class="relative inline-flex items-center justify-center mb-4">
                <div class="pulse-ring absolute w-16 h-16 rounded-full
                            bg-indigo-500/20 border border-indigo-500/30"></div>
                <div class="w-16 h-16 bg-indigo-600/20 border border-indigo-500/40
                            rounded-full flex items-center justify-center text-3xl">
                    📧
                </div>
            </div>

            <h2 class="font-game text-lg font-bold text-white mb-2 tracking-wider">
                VERIFY EMAIL
            </h2>
            <p class="text-xs text-gray-400 mb-5 leading-relaxed">
                We sent a verification link to your email.
                Click the link to activate your account and start trading.
            </p>

            @if(session('status') == 'verification-link-sent')
            <div class="mb-4 bg-green-500/10 border border-green-500/20
                        text-green-400 text-xs rounded-xl px-3 py-2">
                ✅ New verification link sent!
            </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
                @csrf
                <button type="submit"
                        class="glow-btn w-full bg-indigo-600 hover:bg-indigo-500
                               text-white font-game font-bold tracking-widest
                               text-xs py-3 rounded-xl uppercase">
                    📧 Resend Link
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="text-xs text-gray-500 hover:text-red-400 transition">
                    🚪 Log Out
                </button>
            </form>

        </div>
    </div>
</body>
</html>
