<?php

namespace Tests\Feature;

use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_successfully()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Marcos Maio',
            'email' => 'marcospaulomaio2607@gmail.com',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'marcospaulomaio2607@gmail.com',
        ]);
    }

    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'marcospaulomaio2607@gmail.com',
            'password' => Hash::make('senha123'),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'marcospaulomaio2607@gmail.com',
            'password' => 'senha123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
            ]);
    }

    /** @test */
    public function authenticated_user_can_create_comment()
    {
        $user = \App\Models\User::factory()->create();

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/comments', [
            'content' => 'test comment.',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'content',
                'user_id',
                'created_at',
                'updated_at',
            ]);

        $this->assertDatabaseHas('comments', [
            'content' => 'test comment.',
            'user_id' => $user->id,
        ]);
    }
}
