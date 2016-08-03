<?php
/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/3
 * Time: 16:15
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Response\JsonResponse;
use App\Services\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function listAction(Request $request)
    {
        $page     = $request->get('page', 1);
        $limit    = 20;
        $search   = $request->get('search', '');
        $services = Service::getList($search, $page, $limit);

        return view(
            'admin.service',
            [
                'services' => $services,
                'query'    => [
                    'search' => $search,
                ],
            ]
        );
    }

    public function saveAction(Request $request)
    {
        $id      = $request->get('id', 0);
        $name    = $request->get('name', '');
        $enabled = $request->get('enabled', false);
        $hosts   = array_filter(explode("\n", $request->get('hosts', '')));
        $service = Service::createOrUpdate($name, $hosts, $enabled, $id);
        $service->load('hosts');

        return JsonResponse::success($service, trans($id > 0 ? 'admin.service.edit_ok' : 'admin.service.add_ok'));
    }
}