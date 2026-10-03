<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\Login;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_kasir_can_login_to_filament_panel(): void
    {
        Livewire::test(Login::class)
            ->set('data.email', 'kasir')
            ->set('data.password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(Filament::getUrl());

        $this->assertAuthenticated();
        $this->assertEquals('cashier', auth()->user()->role);
    }

    public function test_admin1_can_login_to_filament_panel(): void
    {
        Livewire::test(Login::class)
            ->set('data.email', 'admin1')
            ->set('data.password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(Filament::getUrl());

        $this->assertAuthenticated();
        $this->assertEquals('sales_admin', auth()->user()->role);
    }

    public function test_gm_can_login_to_filament_panel(): void
    {
        Livewire::test(Login::class)
            ->set('data.email', 'gm')
            ->set('data.password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(Filament::getUrl());

        $this->assertAuthenticated();
        $this->assertEquals('gm', auth()->user()->role);
    }

    public function test_driver_logging_in_filament_is_redirected_to_driver_dashboard(): void
    {
        Livewire::test(Login::class)
            ->set('data.email', 'TGL1.2')
            ->set('data.password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(route('driver.dashboard'));

        $this->assertAuthenticated();
        $this->assertEquals('driver', auth()->user()->role);
    }

    public function test_inactive_user_cannot_access_filament_panel(): void
    {
        User::where('username', 'kasir')->update(['is_active' => false]);

        Livewire::test(Login::class)
            ->set('data.email', 'kasir')
            ->set('data.password', 'password')
            ->call('authenticate')
            ->assertHasErrors(['data.email']);

        $this->assertGuest();
    }
}
