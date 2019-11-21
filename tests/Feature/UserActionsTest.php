<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserActionsTest extends TestCase
{
    use DatabaseTransactions;


    protected $userMock;

    /** Get token from the first user (the pojo user for testing)
     * @return string
     */
    protected function getToken(): string
    {
        return JWTAuth::fromUser(User::first());
    }

    /**
     * Test index
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
     * Test Error Index
     *
     * @return void
     */
    public function testErrorIndex()
    {
        $response = $this->get('/api/user', ['Authorization' => "Bearer test"]);

        $response->assertStatus(400);

    }

    /**
     * Test update
     *
     * @return void
     */
    public function testUpdate()
    {

        $token = $this->getToken();

        $response = $this->put('/api/user', [
            'email' => 'test23@test.com',
            'password' => 'aAD1234.@',
            'password_confirmation' => 'aAD1234.@',
            'password_old' => 'foo'
        ],
            ['Authorization' => "Bearer $token"]
        );

        $response->assertStatus(200);
    }

    /**
     * Test Error if the user put the old password
     *
     * @return void
     */
    public function testErrorOldPasswordUpdate()
    {

        $token = $this->getToken();

        $response = $this->put('/api/user', [
            'email' => 'test23@test.com',
            'password' => 'aAD1234.@',
            'password_confirmation' => 'aAD1234.@',
            'password_old' => 'test'
        ],
            ['Authorization' => "Bearer $token"]
        );

        $response->assertStatus(403);
    }

    /**
     * Test Error Update
     *
     * @return void
     */
    public function testErrorUpdate()
    {

        $response = $this->put('/api/user', [
                'password_old' => 'foo'
            ]
        );

        $response->assertStatus(400);
    }

    /**
     * Test Destroy
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
     * Test Error Destroy
     *
     * @return void
     */
    public function testErrorDestroy()
    {

        $response = $this->delete('/api/user');

        $response->assertStatus(400);
    }

    /**
     * Test picture
     *
     * @return void
     */
    public function testPicture()
    {
        $token = $this->getToken();

        $response = $this->get('/api/user/picture',
            ['Authorization' => "Bearer $token"]
        );

        $response->assertStatus(200);
    }


    /**
     * Test Error on picture.
     *
     * @return void
     */
    public function testErrorPicture()
    {

        $response = $this->get('/api/user/picture');

        $response->assertStatus(400);
    }

    /**
     * Test avatar site.
     *
     * @return void
     */
    public function testAvatarSite()
    {
        $token = $this->getToken();

        //then read the avatar
        $response = $this->get('/api/user/avatar',
            ['Authorization' => "Bearer $token"]
        );
        $response->assertStatus(200);
    }


}
