<?php

namespace Tests\Feature;


use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthenticationTest  extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
        $user = new User([
            'email' => 'test@email.com',
            'username' => 'Username',
            'password' => '123456'
        ]);

        $user->save();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        DB::rollBack();

    }


    /** @test */
    public function it_will_register_a_user()
    {
        $response = $this->post('api/auth/register', [
            'email' => 'test23@test.com',
            'username' => 'usertest',
            'password' => 'aAD1234.@',
            'password_confirmation' => 'aAD1234.@'
        ]);


        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
    }

    /** @test */
    public function it_will_log_a_user_in()
    {
        $response = $this->post('api/auth/login', [
            'email' => 'test@email.com',
            'password' => '123456'
        ]);

        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
    }

    /** @test */
    public function it_will_not_log_an_invalid_user_in()
    {
        $response = $this->post('api/auth/login', [
            'email' => 'test@email.com',
            'password' => 'notlegitpassword'
        ]);

        $response->assertJsonStructure([
            'error',
        ]);
    }
}
