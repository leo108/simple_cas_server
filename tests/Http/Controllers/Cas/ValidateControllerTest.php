<?php

/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/2
 * Time: 13:53
 */

use App\Services\Ticket;
use App\Exceptions\CAS\CasException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Response;

class ValidateControllerTest extends TestCase
{
    use DatabaseMigrations;
    protected $user;
    protected $service;

    protected function setUp()
    {
        parent::setUp();
        $this->user    = $this->initDemoUser();
        $this->service = $this->initService();
    }

    protected function getServiceUrl()
    {
        return 'http://'.$this->service->hosts()->first()->host;
    }

    protected function _createTicket()
    {
        return Ticket::applyTicket(
            $this->user,
            $this->getServiceUrl()
        );
    }

    public function testV1Validate()
    {
        //normal
        $ticket = $this->_createTicket();
        $url    = $this->app['url']->route(
            'cas_v1validate',
            ['ticket' => $ticket->ticket, 'service' => $ticket->service_url]
        );

        $this->visit($url)->see('yes');

        //reuse a ticket
        $this->visit($url)->see('no');


        //request with a none-exists ticket
        $url = $this->app['url']->route(
            'cas_v1validate',
            ['ticket' => 'randomstring', 'service' => $ticket->service_url]
        );
        $this->visit($url)->see('no');


        //invalid service url
        $ticket = $this->_createTicket();
        $url    = $this->app['url']->route(
            'cas_v1validate',
            ['ticket' => $ticket->ticket, 'service' => 'http://badserviceurl']
        );
        $this->visit($url)->see('no');

        //empty ticket or service
        $url = $this->app['url']->route(
            'cas_v1validate',
            ['service' => $ticket->service_url]
        );
        $this->visit($url)->see('no');

        $ticket = $this->_createTicket();
        $url    = $this->app['url']->route(
            'cas_v1validate',
            ['ticket' => $ticket->ticket]
        );
        $this->visit($url)->see('no');
    }

    public function testV23Validate()
    {
        $format = ['JSON', 'XML'];
        $router = ['cas_v2validate', 'cas_v3validate'];

        foreach ($format as $f) {
            foreach ($router as $r) {
                //normal
                $expect = $this->genNormalResp($r);
                $ticket = $this->_createTicket();
                $this->doTest(
                    $expect,
                    $r,
                    'http://'.$this->service->hosts()->first()->host,
                    $ticket->ticket,
                    $f
                );

                //reuse ticket
                $expect = [
                    'code' => CasException::INVALID_TICKET,
                ];

                $this->doTest(
                    $expect,
                    $r,
                    'http://'.$this->service->hosts()->first()->host,
                    $ticket->ticket,
                    $f
                );

                //empty ticket or service
                $expect = [
                    'code' => CasException::INVALID_REQUEST,
                ];

                $this->doTest(
                    $expect,
                    $r,
                    '',
                    'justnotempty',
                    $f
                );

                $expect = [
                    'code' => CasException::INVALID_REQUEST,
                ];

                $this->doTest(
                    $expect,
                    $r,
                    'justnotempty',
                    '',
                    $f
                );

                //invalid service url
                $expect = [
                    'code' => CasException::INVALID_SERVICE,
                ];
                $ticket = $this->_createTicket();
                $this->doTest(
                    $expect,
                    $r,
                    'http://badserviceurl',
                    $ticket->ticket,
                    $f
                );
            }
        }
    }

    public function doTest($expect, $router, $service, $ticket, $format)
    {
        $data = array_filter(
            compact('service', 'ticket', 'format'),
            function ($val) {
                return !is_null($val);
            }
        );

        $response = $this->route('GET', $router, $data);
        if (isset($expect['code'])) {
            $this->assertEquals($expect['code'], $this->getErrorCode($response, $format));
        }

        if (isset($expect['equals'])) {
            foreach ($expect['equals'] as $k => $v) {
                $this->assertEquals($v, $this->getResponseValue($response, $format, $k));
            }
        }

        if (isset($expect['empty'])) {
            foreach ($expect['empty'] as $v) {
                $this->assertEmpty($this->getResponseValue($response, $format, $v));
            }
        }

        if (isset($expect['notEmpty'])) {
            foreach ($expect['notEmpty'] as $v) {
                $this->assertNotEmpty($this->getResponseValue($response, $format, $v));
            }
        }
    }

    protected function genNormalResp($router)
    {
        $expect = [
            'equals' => [
                'serviceResponse.authenticationSuccess.user' => $this->user->name,
            ],
        ];
        if (preg_match('~v3~', $router)) {
            $expect['notEmpty']                                                            = ['serviceResponse.authenticationSuccess.attributes'];
            $expect['equals']['serviceResponse.authenticationSuccess.attributes.email']    = $this->user->email;
            $expect['equals']['serviceResponse.authenticationSuccess.attributes.realName'] = $this->user->real_name;
        }

        return $expect;
    }

    protected function getErrorCode(Response $response, $format)
    {
        if (is_null($format)) {
            $format = 'XML';
        }
        if (strtoupper($format) == 'XML') {
            $crawler = new \Symfony\Component\DomCrawler\Crawler();
            $crawler->addXmlContent($response->getContent());
            $final = $crawler->filterXPath('cas:serviceResponse/cas:authenticationFailure');
            if (count($final) == 0) {
                return null;
            }

            return $final->attr('code');
        } else {
            $decode = \json_decode($response->getContent(), true);

            return isset($decode['serviceResponse']['authenticationFailure']['code']) ?
                $decode['serviceResponse']['authenticationFailure']['code'] : null;
        }
    }

    protected function getResponseValue(Response $response, $format, $key)
    {
        if (is_null($format)) {
            $format = 'XML';
        }
        $keyArr = explode('.', $key);
        if (strtoupper($format) == 'XML') {
            $content = '<xml>'.$response->getContent().'</xml>';
            $content = preg_replace('~<cas:~', '<', $content);
            $content = preg_replace('~</cas:~', '</', $content);
            $decode  = json_decode(json_encode(simplexml_load_string($content)), true);
        } else {
            $decode = \json_decode($response->getContent(), true);
        }
        $tmp = $decode;
        foreach ($keyArr as $k) {
            if (!isset($tmp[$k])) {
                return null;
            }
            $tmp = $tmp[$k];
        }

        return $tmp;
    }
}
