<?php

namespace Tests\Unit;

use App\Repositories\UserRepository;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{

    private $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();

        $this->userRepository = new UserRepository();

    }
    protected function tearDown(): void
    {
        parent::tearDown();
        //DB::rollBack();

    }


    /**
     * Test save
     *
     * @return void
     */
    public function testSave()
    {

        $this->assertNotNull($this->userRepository->save("test@test.com", "test", "test"));
    }

    /**
     * Test  Error save
     *
     * @return void
     */
    public function testErrorSave()
    {


        $this->assertNull($this->userRepository->save("", "testpass", "test"));

    }

    /**
     * Test update
     *
     * @return void
     */
    public function testUpdate()
    {

        $user = new User([
            'email' => 'test@email.com',
            'username' => 'Username',
            'password' => '123456'
        ]);
        $user->save();
        $this->assertNotNull($this->userRepository->update($user->id, array("username" => "testUsername")));
    }

    /**
     * Test error update
     *
     * @return void
     */
    public function testErrorUpdate()
    {


        $this->assertNull($this->userRepository->update(-1, array("username" => "testUsername")));
    }

    /**
     * Test update
     *
     * @return void
     */
    public function testDestroy()
    {

        $user = new User([
            'email' => 'test@email.com',
            'username' => 'Username',
            'password' => '123456'
        ]);
        $user->save();
        $this->assertTrue($this->userRepository->delete($user->id));
    }

    /**
     * Test update
     *
     * @return void
     */
    public function testErrorDestroy()
    {


        $this->assertFalse($this->userRepository->delete(-1));
    }

    /**
     * Test update
     *
     * @return void
     */
    public function testCreatePicture()
    {

        $user = new User([
            'email' => 'test@email.com',
            'username' => 'Username',
            'password' => '123456'
        ]);
        $user->save();
        $this->assertIsString($this->userRepository->createPicture($user->id));
    }

    /**
     * Test update
     *
     * @return void
     */
    public function testErrorCreatePicture()
    {

        $this->expectException(\TypeError::class);
        $this->userRepository->createPicture(null);
    }
}
