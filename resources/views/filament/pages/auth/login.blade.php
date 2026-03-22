<x-filament-panels::page.simple class="siabsen-login">
    @php
        $sekolah = \App\Models\Sekolah::query()->where('is_active', true)->first() ?? \App\Models\Sekolah::query()->first();
    @endphp

    {{-- Custom Login Wrapper --}}
    <div class="siabsen-login-wrapper">
        {{-- Logo & Brand Section --}}
        <div class="login-header">
            <div class="logo-container">
                <svg class="logo-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2v4m0 12v4M2 12h4m12 0h4" opacity="0.5"/>
                </svg>
            </div>
            
            <h1 class="brand-title">SiAbsen</h1>
            <div class="brand-subtitle">
                <span class="line"></span>
                <span>Sistem Absensi Sekolah</span>
                <span class="line"></span>
            </div>
        </div>

        {{-- School Name Badge --}}
        @if($sekolah)
        <div class="school-badge-container">
            <span class="school-badge">
                <svg class="school-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                {{ $sekolah->nama }}
            </span>
        </div>
        @endif

        {{-- Login Card --}}
        <div class="login-card">
            <div class="card-header">
                <h2>Selamat Datang</h2>
                <p>Masuk untuk mengelola absensi sekolah</p>
            </div>

            <form wire:submit="authenticate" class="login-form space-y-4">
                {{ csrf_field() }}
                
                {{-- Email Field --}}
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                        <input 
                            type="email" 
                            id="email"
                            name="email"
                            wire:model="data.email"
                            required
                            autofocus
                            autocomplete="email"
                            class="form-input"
                            placeholder="admin@siabsen.test"
                        >
                    </div>
                    @error('data.email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password Field --}}
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <input 
                            type="password" 
                            id="password"
                            name="password"
                            wire:model="data.password"
                            required
                            autocomplete="current-password"
                            class="form-input"
                            placeholder="password"
                        >
                    </div>
                    @error('data.password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="form-options">
                    <label class="remember-me">
                        <input 
                            type="checkbox" 
                            id="remember"
                            name="remember"
                            wire:model="data.remember"
                            class="checkbox-input"
                        >
                        <span class="checkbox-label">Ingat saya</span>
                    </label>
                </div>

                {{-- Submit Button --}}
                <button 
                    type="submit"
                    class="submit-button"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="authenticate">
                        <svg class="button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Masuk
                    </span>
                    <span wire:loading wire:target="authenticate">
                        <svg class="loading-spinner" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>

                {{-- Error Messages --}}
                @if($errors->has('data.email') || $errors->has('data.password'))
                    <div class="login-error">
                        <svg class="error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Email atau password salah</span>
                    </div>
                @endif
            </form>

            {{-- Card Footer --}}
            <div class="card-footer">
                <p>
                    Default login: <strong>admin@siabsen.test</strong> / <strong>password</strong>
                </p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="login-footer">
            <p>SiAbsen &copy; {{ date('Y') }} - Sistem Absensi Sekolah</p>
            <p class="powered-by">Powered by Laravel & Filament</p>
        </div>
    </div>

    {{-- Styles --}}
    @push('styles')
    <style>
        /* Reset default Filament styles */
        .fi-simple-main {
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
            max-width: none !important;
        }
        
        .fi-simple-main-ctn {
            background: transparent !important;
            display: block !important;
        }
        
        .fi-header-heading {
            display: none !important;
        }

        /* Page background */
        .fi-simple-layout {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #06b6d4 100%) !important;
            min-height: 100vh;
        }

        /* Wrapper */
        .siabsen-login-wrapper {
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
            padding: 2rem 1rem;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Header */
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-container {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .logo-container:hover {
            transform: scale(1.05);
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            color: #2563eb;
        }

        .brand-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            margin: 0;
            letter-spacing: -0.5px;
        }

        .brand-subtitle {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 0.5rem;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .brand-subtitle .line {
            width: 24px;
            height: 1px;
            background: rgba(255, 255, 255, 0.4);
        }

        /* School Badge */
        .school-badge-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .school-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 9999px;
            color: white;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .school-icon {
            width: 16px;
            height: 16px;
        }

        /* Login Card */
        .login-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        .card-header {
            padding: 1.75rem 1.75rem 0;
            text-align: center;
        }

        .card-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        .card-header p {
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            margin-bottom: 0;
        }

        .login-form {
            padding: 1.5rem 1.75rem;
        }

        /* Form Elements */
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.375rem;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: #9ca3af;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.9375rem;
            color: #111827;
            transition: all 0.2s ease;
        }

        .form-input::placeholder {
            color: #9ca3af;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            transform: translateY(-1px);
        }

        .error-message {
            font-size: 0.75rem;
            color: #ef4444;
        }

        /* Options */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .checkbox-input {
            width: 16px;
            height: 16px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            cursor: pointer;
        }

        .checkbox-label {
            font-size: 0.875rem;
            color: #4b5563;
        }

        /* Submit Button */
        .submit-button {
            width: 100%;
            padding: 0.875rem 1.5rem;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            font-weight: 600;
            font-size: 0.9375rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
        }

        .submit-button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(37, 99, 235, 0.5);
        }

        .submit-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .button-icon {
            width: 20px;
            height: 20px;
        }

        .loading-spinner {
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Error Message */
        .login-error {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            color: #dc2626;
            font-size: 0.875rem;
        }

        .error-icon {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        /* Card Footer */
        .card-footer {
            padding: 1rem 1.75rem;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }

        .card-footer p {
            font-size: 0.75rem;
            color: #9ca3af;
            margin: 0;
        }

        .card-footer strong {
            color: #6b7280;
            font-weight: 500;
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .login-footer p {
            font-size: 0.875rem;
            margin: 0;
        }

        .login-footer .powered-by {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 0.25rem;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .siabsen-login-wrapper {
                padding: 1rem;
            }

            .brand-title {
                font-size: 2rem;
            }

            .login-form,
            .card-header,
            .card-footer {
                padding-left: 1.25rem;
                padding-right: 1.25rem;
            }
        }
    </style>
    @endpush
</x-filament-panels::page.simple>
