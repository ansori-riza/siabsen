<x-filament-panels::page.simple class="siabsen-login">
    @php
        $sekolah = \App\Models\Sekolah::query()->where('is_active', true)->first() ?? \App\Models\Sekolah::query()->first();
        $logoUrl = $sekolah?->logo ? asset('storage/' . $sekolah->logo) : null;
        $themeColor = $sekolah?->theme_color ?? '#1971C2';
    @endphp

    {{-- Custom Login Wrapper --}}
    <div class="siabsen-login-wrapper">
        {{-- Logo & Brand Section --}}
        <div class="login-header">
            <div class="logo-container" style="--theme-color: {{ $themeColor }};">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $sekolah->nama }}" class="logo-image">
                @else
                    <svg class="logo-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2v4m0 12v4M2 12h4m12 0h4" opacity="0.5"/>
                    </svg>
                @endif
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

            {{-- Filament Form --}}
            <x-filament-panels::form wire:submit="authenticate">
                {{ $this->form }}

                <x-filament::button
                    type="submit"
                    form="form"
                    color="primary"
                    class="submit-btn"
                >
                    <span class="btn-content">
                        <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Masuk
                    </span>
                </x-filament::button>
            </x-filament-panels::form>
        </div>

        {{-- Footer --}}
        <div class="login-footer">
            <p>SiAbsen &copy; {{ date('Y') }} - Sistem Absensi Sekolah</p>
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
            width: 100% !important;
        }
        
        .fi-simple-main-ctn {
            background: transparent !important;
            display: block !important;
            padding: 0 !important;
        }
        
        .fi-header-heading {
            display: none !important;
        }

        /* Page background */
        .fi-simple-layout {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #06b6d4 100%) !important;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
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
            overflow: hidden;
        }

        .logo-container:hover {
            transform: scale(1.05);
        }

        .logo-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 8px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            color: var(--theme-color, #2563eb);
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

        /* Form Styling */
        .fi-form {
            padding: 1.5rem 1.75rem;
            gap: 1rem !important;
        }

        /* Style Filament form components */
        .fi-fo-text-input input {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 0.875rem 1rem;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
        }

        .fi-fo-text-input input:focus {
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            transform: translateY(-1px);
        }

        .fi-fo-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .fi-fo-checkbox input {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            border: 1px solid #d1d5db;
        }

        /* Submit Button */
        .submit-btn {
            width: 100%;
            padding: 0.875rem 1.5rem !important;
            font-size: 0.9375rem !important;
            font-weight: 600 !important;
            border-radius: 12px !important;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            border: none !important;
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4) !important;
            transition: all 0.2s ease !important;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(37, 99, 235, 0.5) !important;
        }

        .btn-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-icon {
            width: 20px;
            height: 20px;
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

        /* Responsive */
        @media (max-width: 640px) {
            .siabsen-login-wrapper {
                padding: 1rem;
            }

            .brand-title {
                font-size: 2rem;
            }

            .fi-form {
                padding-left: 1.25rem;
                padding-right: 1.25rem;
            }

            .card-header {
                padding-left: 1.25rem;
                padding-right: 1.25rem;
            }
        }

        /* Error messages */
        .fi-fo-field-wrp-error-msg {
            font-size: 0.75rem;
            color: #ef4444;
            margin-top: 0.25rem;
        }

        .fi-fo-field-wrp[data-invalid] input {
            border-color: #ef4444;
            background: #fef2f2;
        }

        /* Password toggle button */
        .password-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
            z-index: 10;
        }

        .password-toggle-btn:hover {
            color: #6b7280;
        }

        .password-toggle-btn svg {
            width: 20px;
            height: 20px;
        }

        .password-input-wrapper {
            position: relative;
        }

        .password-input-wrapper input {
            padding-right: 3rem !important;
        }
    </style>
    @endpush

    {{-- Password Toggle Script --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Find password input and add toggle button
            const passwordInputs = document.querySelectorAll('input[type="password"][name="password"], input[type="password"]');
            
            passwordInputs.forEach(function(passwordInput) {
                // Create wrapper if not already wrapped
                if (!passwordInput.parentElement.classList.contains('password-input-wrapper')) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'password-input-wrapper';
                    passwordInput.parentNode.insertBefore(wrapper, passwordInput);
                    wrapper.appendChild(passwordInput);
                }
                
                const wrapper = passwordInput.parentElement;
                
                // Create toggle button
                const toggleBtn = document.createElement('button');
                toggleBtn.type = 'button';
                toggleBtn.className = 'password-toggle-btn';
                toggleBtn.innerHTML = `
                    <svg class="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg class="eye-off-icon" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.432 7.432l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                `;
                toggleBtn.setAttribute('aria-label', 'Toggle password visibility');
                
                wrapper.appendChild(toggleBtn);
                
                // Toggle functionality
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const eyeIcon = toggleBtn.querySelector('.eye-icon');
                    const eyeOffIcon = toggleBtn.querySelector('.eye-off-icon');
                    
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        eyeIcon.style.display = 'none';
                        eyeOffIcon.style.display = 'block';
                    } else {
                        passwordInput.type = 'password';
                        eyeIcon.style.display = 'block';
                        eyeOffIcon.style.display = 'none';
                    }
                });
            });
        });
    </script>
    @endpush
</x-filament-panels::page.simple>
