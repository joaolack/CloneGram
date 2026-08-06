<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Ada Lovelace',
            'username' => 'adalovelace',
            'email' => 'ada@bookshelf.test',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('user.email', 'ada@bookshelf.test')
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonStructure(['token']);

        $this->assertDatabaseHas('users', [
            'username' => 'adalovelace',
            'email' => 'ada@bookshelf.test',
        ]);
    }

    public function test_registration_validates_password_rules(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Weak Password',
            'username' => 'weakpassword',
            'email' => 'weak@bookshelf.test',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }

    public function test_user_can_login_and_fetch_profile(): void
    {
        $user = User::factory()->create([
            'username' => 'loginuser',
            'email' => 'login@bookshelf.test',
            'password' => Hash::make('secret123'),
        ]);

        $login = $this->postJson('/api/login', [
            'email' => 'login@bookshelf.test',
            'password' => 'secret123',
        ]);

        $token = $login->json('token');

        $login
            ->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonStructure(['token']);

        $this->withToken($token)
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('data.email', 'login@bookshelf.test');
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'username' => 'invalidlogin',
            'email' => 'invalid-login@bookshelf.test',
            'password' => Hash::make('secret123'),
        ]);

        $this->postJson('/api/login', [
            'email' => 'invalid-login@bookshelf.test',
            'password' => 'wrong1234',
        ])->assertUnauthorized();
    }

    public function test_logout_revokes_current_token(): void
    {
        $user = User::factory()->create([
            'username' => 'logoutuser',
        ]);
        $token = $user->createToken('api-token')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/logout')
            ->assertNoContent();

        $this->app['auth']->forgetGuards();

        $this->withToken($token)
            ->getJson('/api/me')
            ->assertUnauthorized();
    }

    public function test_logout_all_revokes_all_tokens(): void
    {
        $user = User::factory()->create([
            'username' => 'logoutalluser',
        ]);
        $firstToken = $user->createToken('api-token')->plainTextToken;
        $secondToken = $user->createToken('api-token')->plainTextToken;

        $this->withToken($firstToken)
            ->postJson('/api/logout-all')
            ->assertNoContent();

        $this->app['auth']->forgetGuards();

        $this->withToken($secondToken)
            ->getJson('/api/me')
            ->assertUnauthorized();
    }
}
