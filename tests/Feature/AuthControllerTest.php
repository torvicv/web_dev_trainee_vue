<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_register_route_return_user_registered(): void {
        $user = [
            'name' => 'Brian',
            'email' => 'Brian@gmail.com',
            'password' => 'password'
        ];

        $response = $this->postJson('/api/register', $user)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                '*' => [
                    'name',
                    'email'                ]
            ]);
        $this->assertEquals($user['email'], $response->original['user']->email);
        $this->assertEquals($user['name'], $response->original['user']->name);
    }

    public function test_login_route_return_user_and_token(): void {
        $user = [
            'name' => 'Brian',
            'email' => 'Brian@gmail.com',
            'password' => 'password'
        ];

        $response = $this->postJson('/api/register', $user)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                '*' => [
                    'name',
                    'email'                ]
            ]);

        $user = [
            'email' => 'Brian@gmail.com',
            'password' => 'password'
        ];

        $response = $this->postJson('/api/login', $user)
            ->assertStatus(Response::HTTP_OK);

        $this->assertEquals($user['email'], $response->original['user']->email);
        $this->assertNotEmpty($response->original['token']);
    }

    public function test_logout_route_return_text_logged_out(): void {
        $user = [
            'name' => 'Brian',
            'email' => 'Brian@gmail.com',
            'password' => 'password'
        ];

        $response = $this->postJson('/api/register', $user)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                '*' => [
                    'name',
                    'email'                ]
            ]);

        $user = [
            'email' => 'Brian@gmail.com',
            'password' => 'password'
        ];

        $response = $this->postJson('/api/login', $user)
            ->assertStatus(Response::HTTP_OK);

        $this->assertEquals($user['email'], $response->original['user']->email);
        $this->assertNotEmpty($response->original['token']);
        $token = $response->original['token'];

        $response = $this->postJson('/api/logout',
        [],
        ['authorization' => 'Bearer '.$token])
            ->assertStatus(Response::HTTP_OK);

        $this->assertEquals('Logged out', $response->original['message']);

        $this->assertNotEmpty($response->original['message']);
    }
}
