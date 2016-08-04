<?php

/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/2
 * Time: 11:06
 */
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Exceptions\CAS\CasException;

class SecurityControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testInvalidLogin()
    {
        //login with none exists user
        $this->_login('not_exists_user', 'random')->see(trans('auth.failed'));

        //login with invalid credential
        $user = $this->initDemoUser();
        $this->_login($user->name, 'wrong_password')->see(trans('auth.failed'));
    }

    public function testBaseLoginLogout()
    {
        //normally login
        $user = $this->initDemoUser();
        $this->_login($user->name, 'secret')->see($user->name);

        //test session cookie
        $cookies = $this->response->headers->getCookies(ResponseHeaderBag::COOKIES_ARRAY);
        $this->assertContains($user->name, $this->route('GET', 'home', [], [], $cookies)->getContent());
        //logout
        $this->assertNotContains($user->name, $this->route('GET', 'cas_logout', [], [], $cookies)->getContent());
    }

    public function testDisabledUser()
    {
        $user          = $this->initDemoUser();
        $user->enabled = false;
        $user->save();
        $this->_login($user->name, 'secret')->see(trans('auth.failed'));
    }

    public function testLoginWithRemember()
    {
        $user = $this->initDemoUser();
        $this->_login($user->name, 'secret', true)->see($user->name);

        $cookies = $this->response->headers->getCookies(ResponseHeaderBag::COOKIES_ARRAY);
        unset($cookies['laravel_session']);
        $this->assertContains($user->name, $this->route('GET', 'home', [], [], $cookies)->getContent());
    }

    public function testLoginWithService()
    {
        $user       = $this->initDemoUser();
        $service    = $this->initService();
        $serviceUrl = 'http://'.$service->hosts()->first()->host;

        $url = $this->app['url']->route('cas_login_page', ['service' => $serviceUrl]);
        $this->visit($url)->dontSee((new CasException(CasException::INVALID_SERVICE))->getCasMsg());

        $this->actingAs($user)->route('GET', 'cas_login_page', ['service' => $serviceUrl]);

        $location = $this->response->headers->get('location');
        $this->assertContains($serviceUrl, $location);
        $this->assertContains('ticket=', $location);
    }

    public function testRequestLoginWithInvalidServiceUrl()
    {
        $url = $this->app['url']->route('cas_login_page', ['service' => 'http://none-exists.com']);
        $this->visit($url)->see((new CasException(CasException::INVALID_SERVICE))->getCasMsg());

        $user = $this->initDemoUser();
        $this->visit($url)->type($user->name, 'name')->type('secret', 'password')->press(trans('common.submit'))
            ->see((new CasException(CasException::INVALID_SERVICE))->getCasMsg());
    }

    public function testLoginWithWarn()
    {
        $user       = $this->initDemoUser();
        $service    = $this->initService();
        $serviceUrl = 'http://'.$service->hosts()->first()->host;

        $url = $this->app['url']->route('cas_login_page', ['service' => $serviceUrl, 'warn' => 'true']);
        $this->actingAs($user)->visit($url)->see(trans('message.cas_redirect_warn', ['url' => $serviceUrl]));
        $jumpUrl = $this->filterByNameOrId('btn_ok', 'a')->link()->getUri();
        $this->call('GET', $jumpUrl);
        $location = $this->response->headers->get('location');
        $this->assertContains($serviceUrl, $location);
        $this->assertContains('ticket=', $location);
    }

    /**
     * @param string $name
     * @param string $password
     * @param bool   $remember
     * @param array  $params
     * @return static
     */
    protected function _login($name, $password, $remember = false, $params = array())
    {
        $url  = $this->app['url']->route('cas_login_page', $params);
        $form = $this->visit($url)->type($name, 'name')->type($password, 'password');
        if ($remember) {
            $form->check('remember');
        }

        return $form->press(trans('common.submit'));
    }
}
