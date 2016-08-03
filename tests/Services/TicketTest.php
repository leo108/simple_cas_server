<?php

/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/2
 * Time: 10:41
 */
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Services\Ticket;
use App\Services\User;
use App\Services\Service;
use App\Exceptions\CAS\CasException;

class TicketTest extends TestCase
{
    use DatabaseMigrations;
    protected $user;
    protected $service;

    protected function setUp()
    {
        parent::setUp();
        $this->user    = User::createOrUpdate('demo', 'Demo Name', 'secret', 'demo@demo.com');
        $this->service = Service::create(
            'test',
            [
                'test.com',
                'demo.com',
            ]
        );
    }

    public function testApply()
    {
        $ticket = Ticket::applyTicket($this->user, 'http://test.com');
        $this->assertGreaterThan(0, $ticket->id);
        $this->assertEquals($ticket->user->id, $this->user->id);
        $this->assertEquals($ticket->service->id, $this->service->id);
        $this->assertEquals($ticket->service_url, 'http://test.com');
        $this->assertStringStartsWith('ST-', $ticket->ticket);
        $this->assertEquals(
            strlen($ticket->ticket),
            config('cas.ticket_len')
        );
        $this->assertFalse($ticket->isExpired());
        $this->assertEquals($ticket->id, Ticket::getByTicket($ticket->ticket)->id);
        sleep(config('cas.ticket_expire') + 1);
        $this->assertTrue($ticket->isExpired());
        $this->assertFalse(Ticket::getByTicket($ticket->ticket));
        $this->assertEquals($ticket->id, Ticket::getByTicket($ticket->ticket, false)->id);

        $this->assertFalse(Ticket::getByTicket('none-exists'));

        $ticket    = Ticket::applyTicket($this->user, 'http://test.com');
        $ticketStr = $ticket->ticket;
        Ticket::invalidTicket($ticket);
        $this->assertFalse(Ticket::getByTicket($ticketStr));
    }

    public function testException()
    {
        try {
            Ticket::applyTicket($this->user, 'http://none');
        } catch (CasException $e) {
            $this->assertEquals($e->getCasErrorCode(), CasException::INVALID_SERVICE);

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
}
