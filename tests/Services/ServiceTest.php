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
        $service = Service::create(
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

    public function testException1()
    {
        Service::create(
            'test',
            [
                'test.com',
                'demo.com',
            ]
        );
        try {
            Service::create('test', []);
        } catch (\RuntimeException $e) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testException2()
    {
        Service::create(
            'test',
            [
                'test.com',
                'demo.com',
            ]
        );
        try {
            Service::create('test2', ['test.com']);
        } catch (\RuntimeException $e) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
}
