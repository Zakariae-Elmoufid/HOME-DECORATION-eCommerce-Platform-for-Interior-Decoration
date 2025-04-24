<?php

namespace App\Middlewares;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
Session::start();



class PermissionMiddleware  {

    private string $requiredPermission;
    private  $response;

    public function __construct($permission)
    {
        $this->requiredPermission = $permission;
        $this->response = new Response();
    }

    public function handle(Request $request) {
        $user = Session::get('id')?? null;
        $permissions = Session::get('permissions')?? null;

        if (!$user || !isset($permissions) || !in_array($this->requiredPermission, $permissions )) {
             
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                 $this->response->jsonEncode(['error_permission' => 'Access denied']);
            }else{
                return $this->response->renderError('Access denied');
            }
        }

        return true;
    }
}
