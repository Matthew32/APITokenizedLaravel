<?php

namespace Tests\Unit;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Mocked version of the model.
     */
    protected $userMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->userMock = Mockery::mock('User');
        $this->userMock->id = 1;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }


    /**
     * Test save
     *
     * @return void
     */
    public function testSave()
    {
        $this->userMock->shouldReceive('save')->with("test@test.com", "test", "test")->andReturn('User');

        $userRepository = App::make(UserRepository::class, array($this->userMock));

        $this->assertNotNull($userRepository->save("test2@test.com", "test", "test"));
    }

    /**
     * Test  Error save
     *
     * @return void
     */
    public function testErrorSave()
    {

        $this->userMock->shouldReceive('save')->with("", "test", "test")->andReturn('null');

        $userRepository = App::make(UserRepository::class, array($this->userMock));

        $this->assertNull($userRepository->save("", "testpass", "test"));

    }

    /**
     * Test update
     *
     * @return void
     */
    public function testUpdate()
    {

        $updateTest = array("username" => "testUsername");
        $this->userMock->shouldReceive('update')->with($this->userMock->id, $updateTest)->andReturn('User');

        $userRepository = App::make(UserRepository::class, array($this->userMock));

        $this->assertNotNull($userRepository->update($this->userMock->id, $updateTest));
    }

    /**
     * Test error update
     *
     * @return void
     */
    public function testErrorUpdate()
    {


        $updateTest = array("username" => "testUsername");
        $this->userMock->shouldReceive('update')->with(-1, $updateTest)->andReturn('null');

        $userRepository = App::make(UserRepository::class, array($this->userMock));

        $this->assertNull($userRepository->update(-1, $updateTest));
    }

    /**
     * Test delete
     *
     * @return void
     */
    public function testDelete()
    {

        $this->userMock->shouldReceive('delete')->with($this->userMock->id)->andReturn('bool');

        $userRepository = App::make(UserRepository::class, array($this->userMock));

        $this->assertTrue($userRepository->delete($this->userMock->id));
    }

    /**
     * Test error delete
     *
     * @return void
     */
    public function testErrorDelete()
    {


        $this->userMock->shouldReceive('delete')->with(-1)->andReturn('bool');

        $userRepository = App::make(UserRepository::class, array($this->userMock));

        $this->assertFalse($userRepository->delete(-1));
    }

    /**
     * Test create picture
     *
     * @return void
     */
    public function testCreatePicture()
    {


        $userRepository = App::make(UserRepository::class, array($this->userMock));

        $this->assertEquals("1", $userRepository->createPicture($this->userMock->id));
    }

    /**
     * Test  error create picture
     *
     * @return void
     */
    public function testErrorCreatePicture()
    {

        $this->expectException(\TypeError::class);


        $userRepository = App::make(UserRepository::class, array($this->userMock));

        $this->assertNull($userRepository->createPicture(null));
    }
}
