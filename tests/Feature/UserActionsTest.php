<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserActionsTest extends TestCase
{

    protected function getToken($user = null): string
    {

        if (!isset($user))
            $user = factory(User::class)->create(['password' => bcrypt('foo')]);

        return JWTAuth::fromUser($user);

    }

    /**
     * A  test get User info.
     *
     * @return void
     */
    public function testIndex()
    {
        $token = $this->getToken();
        $response = $this->get('/api/user', ['Authorization' => "Bearer $token"]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'username',
            'email',
            'email_verified_at',
            'updated_at',
            'created_at'
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testErrorIndex()
    {
        $token = $this->getToken();
        $response = $this->get('/api/user', ['Authorization' => "Bearer "]);

        $response->assertStatus(400);

    }

    /**
     * A  test update.
     *
     * @return void
     */
    public function testUpdate()
    {
        DB::beginTransaction();
        $user = new User([
            'email' => 'test@email.com',
            'username' => 'Username',
            'password' => '123456'
        ]);

        $user->save();

        $token = $this->getToken($user);

        $response = $this->put('/api/user', [
            'email' => 'test23@test.com',
            // 'username' => 'usertest',
            'password' => 'aAD1234.@',
            'password_confirmation' => 'aAD1234.@',
            'password_old' => '123456'
        ],
            ['Authorization' => "Bearer $token"]
        );

        $response->assertStatus(200);
    }

    /**
     * A  test Error update old password.
     *
     * @return void
     */
    public function testErrorOldPasswordUpdate()
    {

        $token = $this->getToken(factory(User::class)->create(['password' => bcrypt('foo')]));

        $response = $this->put('/api/user', [
            'email' => 'test23@test.com',
            // 'username' => 'usertest',
            'password' => 'aAD1234.@',
            'password_confirmation' => 'aAD1234.@',
            'password_old' => 'foo'
        ],
            ['Authorization' => "Bearer $token"]
        );

        $response->assertStatus(403);
    }

    /**
     * A  test Error update old password.
     *
     * @return void
     */
    public function testErrorUpdate()
    {

        $token = $this->getToken(factory(User::class)->create(['password' => bcrypt('foo')]));

        $response = $this->put('/api/user', [
                'password_old' => 'foo'
            ]
        );

        $response->assertStatus(400);
    }

    /**
     * A  test destroy.
     *
     * @return void
     */
    public function testDestroy()
    {
        $token = $this->getToken();

        $response = $this->delete('/api/user', [

        ],
            ['Authorization' => "Bearer $token"]
        );

        $response->assertStatus(200);
    }

    /**
     * A  test destroy.
     *
     * @return void
     */
    public function testErrorDestroy()
    {
        $token = $this->getToken();

        $response = $this->delete('/api/user', [
            ]
        );

        $response->assertStatus(400);
    }
}
