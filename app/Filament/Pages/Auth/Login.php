<?php

namespace App\Filament\Pages\Auth;

use App\Models\Driver;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function hasLogo(): bool
    {
        return false;
    }

    public function getHeading(): string | Htmlable
    {
        return 'Dingxin Symotech';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Silakan masukkan kredensial untuk mengakses sistem';
    }

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Masuk ke Dashboard');
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Username, Kode Sopir, atau Email')
            ->placeholder('Contoh: kasir / admin / gm / TGL1.2')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $login = trim((string) ($data['email'] ?? ''));

        // Convenience alias: ketik "sopir" otomatis diarahkan ke akun sample TGL1.2
        if (strtolower($login) === 'sopir') {
            $login = 'TGL1.2';
        }

        // Case-insensitive lookup (username atau email)
        $user = User::whereRaw('LOWER(username) = ?', [strtolower($login)])
            ->orWhereRaw('LOWER(email) = ?', [strtolower($login)])
            ->first();

        // Jika tidak ditemukan, lookup via kode sopir
        if (!$user) {
            $driver = Driver::whereRaw('LOWER(driver_code) = ?', [strtolower($login)])->first();
            if ($driver) {
                $user = $driver->user;
            }
        }

        return [
            'username' => $user ? $user->username : $login,
            'password' => $data['password'] ?? '',
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(15);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        // Cek status keaktifan user
        if (!$user->is_active) {
            Filament::auth()->logout();
            throw ValidationException::withMessages([
                'data.email' => 'Akun Anda berstatus non-aktif. Silakan hubungi administrator.',
            ]);
        }

        // Jika user adalah sopir, arahkan ke dashboard sopir
        if ($user->role === 'driver') {
            session()->regenerate();
            $this->redirect(route('driver.dashboard'));
            return null;
        }

        // Cek akses panel untuk sales_admin, cashier, gm
        if (
            ($user instanceof \Filament\Models\Contracts\FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();
            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.email' => 'Username/kode sopir atau kata sandi yang Anda masukkan salah.',
        ]);
    }
}
