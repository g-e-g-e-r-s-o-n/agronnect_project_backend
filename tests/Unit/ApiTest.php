<?php

namespace Tests\Unit;

//use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Support\Str;

class ApiTest extends TestCase
{
    /**
     * Test user registration.
     */
    public function testUserRegister()
    {
        $response = $this->json('POST', '/api/register', [
            'name' => 'Test User',
            'email' => Str::random(10) . '@gmail.com',
            'password' => '12345678',
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'status',
            'message',
            'user',
            'authorisation' => [
                'token',
                'type',
            ]
        ]);
    }

    /**
     * Test user login.
     */
    public function testUserLogin()
    {
        $response = $this->json('POST', '/api/login', [
            'email' => 'user@gmail.com',
            'password' => 'pass'
        ]);

        $response->assertStatus(422)->assertJsonStructure([
            'status',
        ]);
    }
}
