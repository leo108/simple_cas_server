<?php
/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/1
 * Time: 15:06
 */

namespace App\Services;

use App\Models\Service as Model;
use App\Models\ServiceHost as ServiceHostModel;

class Service
{
    /**
     * @param $url
     * @return \App\Models\Service|null
     */
    public static function getServiceByUrl($url)
    {
        $host = parse_url($url, PHP_URL_HOST);

        $record = ServiceHostModel::where('host', $host)->first();
        if (!$record) {
            return null;
        }

        return $record->service;
    }

    /**
     * @param $url
     * @return bool
     */
    public static function isUrlValid($url)
    {
        return self::getServiceByUrl($url) !== null;
    }

    public static function create($name, $hostArr)
    {
        if (Model::where('name', $name)->count() > 0) {
            throw new \RuntimeException('Service name duplicated'); //todo change exception class
        }

        $service = Model::create(['name' => $name]);
        foreach ($hostArr as $host) {
            if (ServiceHostModel::where('host', $host)->count() > 0) {
                //todo change exception class
                throw new \RuntimeException(sprintf('Service host %s is occupied', $host));
            }
            ServiceHostModel::create(['host' => $host, 'service_id' => $service->id]);
        }

        return $service;
    }
}
