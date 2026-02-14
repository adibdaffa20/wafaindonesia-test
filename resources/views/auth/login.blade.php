<x-guest-layout>
    <div class="login-wrapper">

        <!-- Card -->
        <div class="login-card">

            <div class="login-header">
                <h1 class="login-title">Dashboard Leads</h1>
                <p class="login-subtitle">Selamat datang kembali! Silakan masuk ke akun Anda.</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="login-form">
                @csrf

                <!-- Username -->
                <div class="field">
                    <x-input-label for="username" :value="__('Username')" />
                    <div class="input-wrapper">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M10 10C12.7614 10 15 7.76142 15 5C15 2.23858 12.7614 0 10 0C7.23858 0 5 2.23858 5 5C5 7.76142 7.23858 10 10 10Z" fill="currentColor" opacity="0.5"/>
                            <path d="M10 12C4.477 12 0 14.015 0 16.5V20H20V16.5C20 14.015 15.523 12 10 12Z" fill="currentColor" opacity="0.5"/>
                        </svg>
                        <x-text-input id="username"
                            class="input"
                            type="text"
                            name="username"
                            :value="old('username')"
                            required autofocus autocomplete="username"
                            placeholder="Masukkan username" />
                    </div>
                    <x-input-error :messages="$errors->get('username')" class="mt-2 text-sm text-red-500" />
                </div>

                <!-- Password -->
                <div class="field">
                    <x-input-label for="password" :value="__('Password')" />
                    <div class="input-wrapper">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M15 8H14V5C14 2.791 12.209 1 10 1C7.791 1 6 2.791 6 5V8H5C3.897 8 3 8.897 3 10V17C3 18.103 3.897 19 5 19H15C16.103 19 17 18.103 17 17V10C17 8.897 16.103 8 15 8ZM8 5C8 3.897 8.897 3 10 3C11.103 3 12 3.897 12 5V8H8V5Z" fill="currentColor" opacity="0.5"/>
                        </svg>
                        <x-text-input id="password"
                            class="input"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="Masukkan password" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-500" />
                </div>

                <!-- Remember & Forgot -->
                <div class="remember-forgot">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" class="checkbox">
                        <span>Ingat saya</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">
                            Lupa password?
                        </a>
                    @endif
                </div>

                <!-- Captcha -->
                <div class="field">
                    <label for="captcha">Verifikasi</label>

                    <div class="captcha-wrapper">
                        <span class="captcha-img">{!! captcha_img('flat') !!}</span>

                        <button type="button" class="captcha-refresh" onclick="refreshCaptcha()">
                            ⟳
                        </button>
                    </div>

                    <input type="text"
                        name="captcha"
                        class="input"
                        placeholder="Masukkan kode di atas"
                        required>

                    @error('captcha')
                        <div class="text-red-500">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Button -->
                <button type="submit" class="login-btn">
                    <span>Masuk</span>
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M7.5 15L12.5 10L7.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </form>

        </div>

    </div>

    <script>
        function refreshCaptcha(){
            fetch('/refresh-captcha')
                .then(res => res.json())
                .then(data => {
                    document.querySelector('.captcha-img').innerHTML = data.captcha;
                });
        }
        </script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Background */
    .login-wrapper {
        min-height: 100vh;
        width: 100%;
        display: grid;
        place-items: center;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        padding: 20px;
        position: fixed;
        top: 0;
        left: 0;

        /* Multiple background layers */
        background:
            linear-gradient(
                135deg,
                rgba(107,27,78,0.85) 0%,
                rgba(142,42,106,0.85) 35%,
                rgba(194,24,91,0.85) 70%,
                rgba(233,30,99,0.85) 100%
            ),
            url('/assets/wafa.png');

        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }


        /* Card */
        .login-card {
            width: 100%;
            max-width: 460px;
            padding: 48px 40px;
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 
                0 40px 100px rgba(0, 0, 0, 0.35),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
        }

        /* Header */
        .login-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .login-title {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #8E2A6A, #E91E63);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0 0 8px 0;
            animation: fadeInDown 0.6s ease-out;
        }

        .login-subtitle {
            font-size: 14px;
            color: rgba(15, 23, 42, 0.6);
            line-height: 1.5;
            animation: fadeInDown 0.6s ease-out 0.1s both;
        }

        /* Form */
        .login-form {
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        /* Fields */
        .field {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            color: rgba(15, 23, 42, 0.4);
            pointer-events: none;
            z-index: 1;
        }

        .input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border-radius: 16px;
            border: 2px solid rgba(15, 23, 42, 0.08);
            font-size: 15px;
            outline: none;
            transition: all 0.2s ease;
            background: rgba(248, 250, 252, 0.5);
            color: #1e293b;
        }

        .input::placeholder {
            color: rgba(15, 23, 42, 0.4);
        }

        .input:focus {
            border-color: #E91E63;
            background: #ffffff;
            box-shadow: 
                0 0 0 4px rgba(233, 30, 99, 0.1),
                0 4px 12px rgba(233, 30, 99, 0.15);
        }

        .input:focus + .input-icon {
            color: #E91E63;
        }

        /* Remember & Forgot */
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: rgba(15, 23, 42, 0.7);
            cursor: pointer;
            user-select: none;
        }

        /* CAPTCHA */
        .captcha-wrapper{
            display:flex;
            align-items:center;
            gap:10px;
            margin-bottom:12px;
        }

        .captcha-img img{
        height: 38px;
        border-radius: 12px;
        border: 2px solid rgba(15,23,42,0.08);
    }

        .captcha-refresh{
            padding: 6px 10px;
            font-size: 13px;
        }


        .captcha-refresh:hover{
            transform:translateY(-2px);
            box-shadow:0 6px 18px rgba(233,30,99,0.3);
        }

        .checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #E91E63;
        }

        .forgot-link {
            font-size: 14px;
            color: #E91E63;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .forgot-link:hover {
            color: #8E2A6A;
            text-decoration: underline;
        }

        /* Button */
        .login-btn {
            width: 100%;
            padding: 15px;
            border-radius: 16px;
            border: none;
            font-weight: 700;
            font-size: 15px;
            color: #fff;
            cursor: pointer;
            background: linear-gradient(135deg, #8E2A6A 0%, #E91E63 100%);
            box-shadow: 
                0 4px 15px rgba(142, 42, 106, 0.25),
                0 8px 25px rgba(233, 30, 99, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 
                0 8px 25px rgba(142, 42, 106, 0.35),
                0 12px 35px rgba(233, 30, 99, 0.3);
        }

        .login-btn:active {
            transform: translateY(-1px);
        }

        .login-btn svg {
            transition: transform 0.3s ease;
        }

        .login-btn:hover svg {
            transform: translateX(4px);
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 36px 28px;
            }

            .login-title {
                font-size: 24px;
            }

            .login-subtitle {
                font-size: 13px;
            }

            .remember-forgot {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }
        }

        /* Error messages styling */
        .text-red-500 {
            color: #ef4444;
            font-size: 13px;
            margin-top: 6px;
        }
    </style>
</x-guest-layout>