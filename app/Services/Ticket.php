<?php
/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/1
 * Time: 15:23
 */

namespace App\Services;

use App\Exceptions\CAS\CasException;
use App\Models\Ticket as Model;
use App\User as UserModel;
use Carbon\Carbon;

class Ticket
{
    /**
     * @param UserModel $user
     * @param string    $serviceUrl
     * @throws CasException
     * @return \App\Models\Ticket
     */
    public static function applyTicket(UserModel $user, $serviceUrl)
    {
        $service = Service::getServiceByUrl($serviceUrl);
        if (!$service) {
            throw new CasException(CasException::INVALID_SERVICE);
        }
        $ticket = self::getAvailableTicket(config('cas.ticket_len', 32));
        if (!$ticket) {
            throw new CasException(CasException::INTERNAL_ERROR, 'apply ticket failed');
        }
        $record = Model::create(
            [
                'ticket'      => $ticket,
                'expire_at'   => new Carbon(sprintf('+%dsec', config('cas.ticket_expire', 300))),
                'created_at'  => new Carbon(),
                'service_url' => $serviceUrl,
                'user_id'     => $user->id,
                'service_id'  => $service->id,
            ]
        );

        return $record;
    }

    /**
     * @param      $ticket
     * @param bool $checkExpired
     * @return bool|\App\Models\Ticket
     */
    public static function getByTicket($ticket, $checkExpired = true)
    {
        $record = Model::where('ticket', $ticket)->first();
        if (!$record) {
            return false;
        }

        return ($checkExpired && $record->isExpired()) ? false : $record;
    }

    /**
     * @param Model $ticket
     * @return bool|null
     */
    public static function invalidTicket(Model $ticket)
    {
        return $ticket->delete();
    }

    /**
     * @param $totalLength
     * @return bool|string
     */
    protected static function getAvailableTicket($totalLength)
    {
        $prefix = 'ST-';
        $ticket = false;
        $flag   = false;
        for ($i = 0; $i < 10; $i++) {
            $str    = bin2hex(random_bytes($totalLength));
            $ticket = $prefix.substr($str, 0, $totalLength - strlen($prefix));
            if (!self::getByTicket($ticket, false)) {
                $flag = true;
                break;
            }
        }

        if (!$flag) {
            return false;
        }

        return $ticket;
    }
}
