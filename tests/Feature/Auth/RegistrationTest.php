<?php

namespace Tests\Feature\Auth;

use App\Models\Company;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'company_name' => 'Precifica Fácil LTDA',
            'company_phone' => '11999999999',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $company = Company::where('email', 'test@example.com')->first();

        $this->assertNotNull($company);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'company_id' => $company->id,
            'role' => 'owner',
            'is_owner' => true,
        ]);
        $this->assertDatabaseHas('settings', [
            'company_id' => $company->id,
        ]);
        $this->assertInstanceOf(Setting::class, $company->fresh()->setting);
    }
}
