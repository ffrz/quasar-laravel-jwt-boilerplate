<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckAcl
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $role = $user->role;

        if ($role === User::Role_Admin) {
            return $next($request);
        }

        $action = $this->getActionName($request);
        $controllerFQCN = $this->getControllerName($request);

        // Ambil alias dari FQCN
        $controllerAlias = config("acl.controller_alias.$controllerFQCN");

        if (!$controllerAlias) {
            return response()->json(['error' => 'Unknown controller alias'], 500);
        }

        $permissions = config("acl.roles.$role.$controllerAlias", []);

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
            [$controllerClass, $method] = explode('@', $action['controller']);
            return $controllerClass;
        }

        return null;
    }

    protected function getActionName(Request $request): string
    {
        $route = $request->route();
        if (!$route) return '';

        $action = $route->getAction();

        if (isset($action['controller']) && strpos($action['controller'], '@') !== false) {
            return explode('@', $action['controller'])[1];
        }

        return 'Closure';
    }
}
