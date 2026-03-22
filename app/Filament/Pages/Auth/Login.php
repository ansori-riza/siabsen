<?php

namespace App\Filament\Pages\Auth;

use App\Models\Sekolah;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.login';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->autocomplete()
                    ->autofocus(),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required()
                    ->autocomplete('current-password'),

                Checkbox::make('remember')
                    ->label('Ingat saya'),
            ])
            ->statePath('data');
    }

    /**
     * Return empty string since we have custom view
     */
    public function getHeading(): string
    {
        return '';
    }
}
