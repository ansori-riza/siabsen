<?php

namespace App\Filament\Pages\Auth;

use App\Models\Sekolah;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.login';

    public function getHeading(): string | HtmlString
    {
        $sekolah = Sekolah::query()->where('is_active', true)->first() ?? Sekolah::query()->first();
        $namaSekolah = $sekolah?->nama ?? config('app.name', 'SiAbsen');

        return new HtmlString('
            <div class="flex flex-col items-center gap-2">
                <div class="text-2xl font-bold text-primary-600">' . e($namaSekolah) . '</div>
                <div class="text-sm text-gray-500">Sistem Absensi Sekolah</div>
            </div>
        ');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->autocomplete()
                    ->autofocus()
                    ->placeholder('Masukkan email Anda')
                    ->extraInputAttributes(['class' => 'transition-all duration-200 focus:ring-2 focus:ring-primary-500']),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required()
                    ->autocomplete('current-password')
                    ->placeholder('Masukkan password Anda')
                    ->extraInputAttributes(['class' => 'transition-all duration-200 focus:ring-2 focus:ring-primary-500']),

                Checkbox::make('remember')
                    ->label('Ingat saya'),
            ])
            ->statePath('data');
    }

    protected function getAuthenticateFormAction(): \Filament\Forms\Components\Component
    {
        return \Filament\Forms\Components\Actions\Action::make('authenticate')
            ->label('Masuk')
            ->submit('authenticate')
            ->extraAttributes(['class' => 'w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-[1.02]']);
    }
}
