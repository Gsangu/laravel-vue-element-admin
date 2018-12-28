<?php

namespace App\Http\Controllers;

use Cache;
use App\Traits\ApiResponse;
use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @OA\Info(
 *      title=" API文档",
 *      description=" API文档说明",
 *      version="1.0.0",
 *      @OA\Contact(
 *          name="Gsan",
 *          email="gsann@foxmail.com"
 *      )
 * )
 * @OA\Server(
 *      url="/api",
 *      description="api地址"
 * )
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="header",
 *     securityScheme="oauth2",
 *     name="Authorization"
 * )
 */
class Controller extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ApiResponse, Helpers;

  protected function checkRolePermission($role)
  {
      $id = auth('api')->user()->id;
      $user = Cache::get("user-$id");
      if($user['roles']) {
        $data = $user['roles']['data'];
        foreach ($data as $datum) {
          if($role == $datum) {
            return true;
          }
        }
      }
      $this->response->array(['err' => '无权限']);
  }
}
