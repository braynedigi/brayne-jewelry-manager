<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Distributor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class UserAuthenticationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'distributor'
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertRedirect('/distributor/dashboard');
        $this->assertAuthenticated();
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'distributor'
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function admin_user_redirects_to_admin_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertRedirect('/admin/dashboard');
    }

    /** @test */
    public function distributor_user_redirects_to_distributor_dashboard()
    {
        $distributor = User::factory()->create(['role' => 'distributor']);

        $response = $this->actingAs($distributor)->get('/dashboard');

        $response->assertRedirect('/distributor/dashboard');
    }

    /** @test */
    public function factory_user_redirects_to_factory_dashboard()
    {
        $factory = User::factory()->create(['role' => 'factory']);

        $response = $this->actingAs($factory)->get('/dashboard');

        $response->assertRedirect('/factory/dashboard');
    }

    /** @test */
    public function distributor_needs_profile_to_access_dashboard()
    {
        $distributor = User::factory()->create(['role' => 'distributor']);

        $response = $this->actingAs($distributor)->get('/distributor/dashboard');

        $response->assertRedirect('/distributor/profile/create');
    }

    /** @test */
    public function distributor_can_create_profile()
    {
        $distributor = User::factory()->create(['role' => 'distributor']);

        $profileData = [
            'company_name' => 'Test Company',
            'phone' => '+1234567890',
            'street' => '123 Test St',
            'city' => 'Test City',
            'province' => 'Test Province',
            'country' => 'Test Country',
            'is_international' => false
        ];

        $response = $this->actingAs($distributor)->post('/distributor/profile', $profileData);

        $response->assertRedirect();
        $this->assertDatabaseHas('distributors', [
            'user_id' => $distributor->id,
            'company_name' => 'Test Company'
        ]);
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    /** @test */
    public function unauthenticated_user_cannot_access_protected_routes()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/distributor/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/factory/dashboard');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function distributor_cannot_access_admin_routes()
    {
        $distributor = User::factory()->create(['role' => 'distributor']);

        $response = $this->actingAs($distributor)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /** @test */
    public function factory_cannot_access_distributor_routes()
    {
        $factory = User::factory()->create(['role' => 'factory']);

        $response = $this->actingAs($factory)->get('/distributor/dashboard');
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_access_all_routes()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/distributor/dashboard');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/factory/dashboard');
        $response->assertStatus(200);
    }
} 