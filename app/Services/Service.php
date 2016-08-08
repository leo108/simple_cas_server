<?php
/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/1
 * Time: 15:06
 */

namespace App\Services;

use App\Exceptions\UserException;
use App\Models\Service as Model;
use App\Models\ServiceHost as ServiceHostModel;
use Carbon\Carbon;

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
        $service = self::getServiceByUrl($url);

        return $service !== null && $service->enabled;
    }

    /**
     * @param $name
     * @param $hostArr
     * @param $enabled
     * @param $id
     * @return \App\Models\Service
     */
    public static function createOrUpdate($name, $hostArr, $enabled = true, $id = 0)
    {
        \DB::beginTransaction();
        if ($id == 0) {
            if (Model::where('name', $name)->count() > 0) {
                throw new UserException(trans('message.service.name_duplicated'));
            }

            $service = Model::create(
                [
                    'name'       => $name,
                    'enabled'    => boolval($enabled),
                    'created_at' => (new Carbon())->toDateTimeString(),
                ]
            );
        } else {
            $service          = Model::find($id);
            $service->enabled = boolval($enabled);
            $service->save();
            ServiceHostModel::where('service_id', $id)->delete();
        }

        foreach ($hostArr as $host) {
            $host = trim($host);
            if (ServiceHostModel::where('host', $host)->count() > 0) {
                throw new UserException(trans('message.service.host_occupied', ['host' => $host]));
            }
            ServiceHostModel::create(['host' => $host, 'service_id' => $service->id]);
        }
        \DB::commit();

        return $service;
    }

    public static function getList($search, $page, $limit)
    {
        /* @var \Illuminate\Database\Query\Builder $query */
        $like = '%'.$search.'%';
        if (!empty($search)) {
            $query = Model::whereHas(
                'hosts',
                function ($query) use ($like) {
                    $query->where('host', 'like', $like);
                }
            )->orWhere('name', 'like', $like)->with('hosts');
        } else {
            $query = Model::with('hosts');
        }

        return $query->orderBy('id', 'desc')->paginate($limit, ['*'], 'page', $page);
    }

    public static function dashboard()
    {
        return [
            'total'   => Model::count(),
            'enabled' => Model::where('enabled', true)->count(),
        ];
    }
}
