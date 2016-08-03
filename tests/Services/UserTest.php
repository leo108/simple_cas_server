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
        $user = User::createOrUpdate('demo', 'Demo Name', 'secret', 'demo@demo.com');
        $this->assertGreaterThan(0, $user->id);
        $this->assertEquals('demo', $user->name);
        $this->assertEquals('Demo Name', $user->real_name);
        $this->assertEquals('demo@demo.com', $user->email);
        $this->assertTrue($user->enabled);
        $this->assertFalse($user->admin);
        $this->assertTrue(Hash::check('secret', $user->password));
        $this->assertEquals($user->id, User::getUserById($user->id)->id);
        $this->assertEquals($user->id, User::getUserByName($user->name)->id);

        //test update
        $new = User::createOrUpdate('new name', 'New Real Name', 'new pwd', 'new@demo.com', true, false, $user->id);
        $this->assertEquals($user->id, $new->id);
        //will not change name
        $this->assertEquals('demo', $new->name);
        $this->assertEquals('New Real Name', $new->real_name);
        $this->assertEquals('new@demo.com', $new->email);
        $this->assertFalse($new->enabled);
        $this->assertTrue($new->admin);
        $this->assertTrue(Hash::check('new pwd', $new->password));

        $admin = User::createOrUpdate('admin', 'Admin Name', 'secret', 'admin@demo.com', true);
        $this->assertTrue($admin->admin);

        try {
            User::createOrUpdate('demo', 'Demo Name', 'secret', 'demo@demo.com');
        } catch (\RuntimeException $e) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testResetPassword()
    {
        $user = User::createOrUpdate('demo', 'Demo Name', 'secret', 'demo@demo.com');
        $this->assertTrue(Hash::check('secret', $user->password));

        $new = User::resetPassword($user->id, 'new pwd');
        $this->assertTrue(Hash::check('new pwd', $new->password));
    }

    public function testGetList()
    {
        User::createOrUpdate('demo', 'Demo Name', 'secret', 'demo@demo.com', false, false);
        User::createOrUpdate('admin', 'Admin Name', 'secret', 'admin@demo.com', true);
        $this->assertCount(2, User::getList('', null, null, 1, 10));
        $this->assertCount(2, User::getList('demo', null, null, 1, 10));
        $this->assertCount(1, User::getList('', true, null, 1, 10));
        $this->assertCount(1, User::getList('', null, true, 1, 10));

        $list = User::getList('admin', null, null, 1, 10);
        $this->assertCount(1, $list);
        $this->assertEquals('admin', $list[0]->name);

        $list = User::getList('Demo Name', null, null, 1, 10);
        $this->assertCount(1, $list);
        $this->assertEquals('demo', $list[0]->name);
    }
}
