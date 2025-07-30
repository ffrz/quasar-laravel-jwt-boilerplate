<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class CheckAcl
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $role = $user->role; // Asumsikan kolom 'role' ada di tabel users
        $action = $this->getActionName($request);
        $controller = $this->getControllerName($request);
        $permissions = config("acl.roles.$role.$controller", []);

        if (!in_array($action, $permissions)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return $next($request);
    }

    protected function getControllerName(Request $request): ?string
    {
        $route = $request->route();
        if (!$route) return null;

        $action = $route->getAction();

        if (isset($action['controller'])) {
            // Output: App\Http\Controllers\UserController@index
            [$controllerClass, $method] = explode('@', $action['controller']);
            return $controllerClass; // FQCN
        }

        return null;
    }

    protected function getActionName(Request $request): string
    {
        $route = $request->route();
        if (!$route) return '';

        $action = $route->getAction();

        if (isset($action['controller']) && strpos($action['controller'], '@') !== false) {
            return explode('@', $action['controller'])[1]; // e.g. 'index'
        }

        return 'Closure';
    }
}
