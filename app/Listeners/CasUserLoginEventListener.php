<?php

namespace App\Listeners;

use App\Events\CasUserLoginEvent;
use App\Services\Ticket;
use Illuminate\Http\Response;
use App\Exceptions\CAS\CasException;

class CasUserLoginEventListener
{
    /**
     * Handle the event.
     *
     * @param  CasUserLoginEvent $event
     * @return Response
     */
    public function handle(CasUserLoginEvent $event)
    {
        $serviceUrl = $event->getRequest()->get('service', '');
        if (!empty($serviceUrl)) {
            $query = parse_url($serviceUrl, PHP_URL_QUERY);
            try {
                $ticket = Ticket::applyTicket($event->getUser(), $serviceUrl);
            } catch (CasException $e) {
                return redirect()->route('home')->withErrors(['global' => $e->getCasMsg()]);
            }
            $finalUrl = $serviceUrl.($query ? '&' : '?').'ticket='.$ticket->ticket;

            return redirect($finalUrl);
        }

        return redirect()->route('home');
    }
}
