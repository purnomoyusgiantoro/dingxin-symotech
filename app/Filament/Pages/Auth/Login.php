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
        return 'Dingxin Symotech Portal';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return new HtmlString(
            '<div class="text-xs text-gray-500 dark:text-gray-400 space-y-2 mt-1">' .
            '<div>Portal Operasional Distribusi, Kasir, & Manajemen</div>' .
            '<div class="p-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-[11px] leading-relaxed border border-gray-200 dark:border-gray-700 text-left">' .
            '<div class="font-semibold text-gray-700 dark:text-gray-200 mb-1">Daftar Akun Login (Password: <code>password</code>):</div>' .
            '<div class="grid grid-cols-2 gap-1 text-[11px]">' .
            '<div>&bull; Kasir: <strong class="text-primary-600 dark:text-primary-400">kasir</strong></div>' .
            '<div>&bull; Admin: <strong class="text-primary-600 dark:text-primary-400">admin</strong> / <strong>admin1</strong></div>' .
            '<div>&bull; GM: <strong class="text-primary-600 dark:text-primary-400">gm</strong></div>' .
            '<div>&bull; Sopir: <strong class="text-primary-600 dark:text-primary-400">TGL1.2</strong> / <strong>BRS1.2</strong></div>' .
            '</div>' .
            '<div class="mt-2 pt-1.5 border-t border-gray-200 dark:border-gray-700 text-gray-400 text-[10px]">' .
            'Sopir armada dapat juga login via <a href="/login" class="text-primary-600 dark:text-primary-400 underline font-medium">Portal Sopir Mobile</a>' .
            '</div>' .
            '</div>' .
            '</div>'
        );
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
