<?php

/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/2
 * Time: 10:13
 */

use App\Services\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    public function testCreateAndGet()
    {
        $user = User::create('demo', 'Demo Name', 'secret', 'demo@demo.com');
        $this->assertGreaterThan(0, $user->id);
        $this->assertEquals('demo', $user->name);
        $this->assertEquals('Demo Name', $user->real_name);
        $this->assertEquals('demo@demo.com', $user->email);
        $this->assertTrue($user->enabled);
        $this->assertFalse($user->admin);
        $this->assertTrue($this->app->make('hash')->check('secret', $user->password));
        $this->assertEquals($user->id, User::getUserById($user->id)->id);
        $this->assertEquals($user->id, User::getUserByName($user->name)->id);

        $admin = User::create('admin', 'Admin Name', 'secret', 'admin@demo.com', true);
        $this->assertTrue($admin->admin);

        try {
            User::create('demo', 'Demo Name', 'secret', 'demo@demo.com');
        } catch (\RuntimeException $e) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
}
