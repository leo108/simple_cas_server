<?php

/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/3
 * Time: 10:29
 */
use Illuminate\Foundation\Testing\DatabaseMigrations;

class HomeControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testChangePwd()
    {
        $user = $this->initDemoUser();
        $this->actingAs($user)->route('POST', 'change_pwd', [], ['old' => 'wrong pwd', 'new' => 'whatever']);
        $this->seeJson(['code' => -1]);
        $this->actingAs($user)->route('POST', 'change_pwd', [], ['old' => 'secret', 'new' => 'new pwd']);
        $this->seeJson(['code' => 0]);
    }

    public function testDisabledUser()
    {
        $user          = $this->initDemoUser();
        $user->enabled = false;
        $user->save();
        $this->actingAs($user)->route('GET', 'home');
        $this->assertRedirectedToRoute('cas_login_page');
    }
}
