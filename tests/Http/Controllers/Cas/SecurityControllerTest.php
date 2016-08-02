<?php

/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/2
 * Time: 11:06
 */
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Exceptions\CAS\CasException;

class SecurityControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testRequestLoginWithInvalidServiceUrl()
    {
        $url = $this->app['url']->route('cas_login_page', ['service' => 'http://none-exists.com']);
        $this->visit($url)->see((new CasException(CasException::INVALID_SERVICE))->getCasMsg());

        $user = $this->initDemoUser();
        $this->visit($url)->type($user->name, 'name')->type('secret', 'password')->press(trans('common.submit'))
            ->see((new CasException(CasException::INVALID_SERVICE))->getCasMsg());
    }

//
//    public function testInvalidLogin()
//    {
//        //login with none exists user
//        $client = $this->_login(self::getNoneExistsUser(), 'random');
//        $this->assertTrue(
//            $client->getResponse()->isRedirect(
//                $this->getRouter($client)->generate('cas_login', array(), Router::ABSOLUTE_URL)
//            )
//        );
//        $crawler = $client->followRedirect();
//        $this->assertContains(
//            $this->getTrans($client)->trans('security.login.invalid_credential'),
//            $crawler->filter('.panel-heading .alert')->text()
//        );
//
//        //login with invalid credential
//        $client = $this->_login(self::getDemoUser(), 'wrong_password');
//        $this->assertTrue(
//            $client->getResponse()->isRedirect(
//                $this->getRouter($client)->generate('cas_login', array(), Router::ABSOLUTE_URL)
//            )
//        );
//        $crawler = $client->followRedirect();
//        $this->assertContains(
//            $this->getTrans($client)->trans('security.login.invalid_credential'),
//            $crawler->filter('.panel-heading .alert')->text()
//        );
//    }

//    /**
//     * @param User   $user
//     * @param string $password
//     * @param bool   $remember
//     * @param array  $params
//     * @return Client
//     */
//    protected function _login(User $user, $password, $remember = false, $params = array())
//    {
//
//
//        $client  = static::createClient();
//        $crawler = $client->request('GET', $this->getRouter($client)->generate('cas_login', $params));
//        $this->assertContains('Central Authentication Service', $crawler->filter('.panel-title')->text());
//        $form = $crawler->filter('form')->form(
//            [
//                'username' => $user->getUsername(),
//                'password' => $password,
//            ]
//        );
//        if ($remember) {
//            $form['rememberMe']->tick();
//        }
//        $client->submit($form);
//
//        return $client;
//    }
}
