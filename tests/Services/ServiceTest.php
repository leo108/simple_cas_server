<?php

/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/2
 * Time: 10:56
 */

use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\Services\Service;

class ServiceTest extends TestCase
{
    use  DatabaseMigrations;

    public function testCreateAndGet()
    {
        $service = Service::createOrUpdate(
            'test',
            [
                'test.com',
                'demo.com',
            ]
        );
        $this->assertEquals($service->name, 'test');
        $this->assertCount(2, $service->hosts);
        $this->assertGreaterThan(0, $service->id);

        $this->assertEquals($service->id, Service::getServiceByUrl('http://test.com')->id);
        $this->assertEquals($service->id, Service::getServiceByUrl('http://demo.com')->id);
        $this->assertNull(Service::getServiceByUrl('http://none.com'));
        $this->assertFalse(Service::isUrlValid('http://none.com'));
    }

    public function testEnable()
    {
        $service = Service::createOrUpdate(
            'test',
            [
                'test.com',
                'demo.com',
            ]
        );
        $this->assertTrue(Service::isUrlValid('http://test.com'));

        Service::createOrUpdate(
            'test',
            [
                'test.com',
                'demo.com',
            ],
            false,
            $service->id
        );
        $this->assertFalse(Service::isUrlValid('http://test.com'));
    }

    public function testGetList()
    {
        Service::createOrUpdate(
            'key1',
            [
                'key2.com',
                'key3.com',
            ]
        );
        Service::createOrUpdate(
            'key2',
            [
                'key4.com',
                'key5.net',
            ]
        );
        $this->assertCount(2, Service::getList('', 1, 10));
        $this->assertCount(2, Service::getList('key2', 1, 10));
        //only match host
        $list = Service::getList('key3', 1, 10);
        $this->assertCount(1, $list);
        $this->assertEquals($list[0]['name'], 'key1');

        //only match name
        $list = Service::getList('key1', 1, 10);
        $this->assertCount(1, $list);
        $this->assertEquals($list[0]['name'], 'key1');
    }

    public function testException1()
    {
        Service::createOrUpdate(
            'test',
            [
                'test.com',
                'demo.com',
            ]
        );
        try {
            Service::createOrUpdate('test', []);
        } catch (\RuntimeException $e) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testException2()
    {
        Service::createOrUpdate(
            'test',
            [
                'test.com',
                'demo.com',
            ]
        );
        try {
            Service::createOrUpdate('test2', ['test.com']);
        } catch (\RuntimeException $e) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
}
