<?php

declare(strict_types=1);

use App\Application\Actions\Task\CreateTaskAction;
use App\Application\Actions\Task\DeleteTaskAction;
use App\Application\Actions\Task\ListTasksAction;
use App\Application\Actions\Task\UpdateTaskAction;
use App\Application\Actions\Task\ViewTaskAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
   
     // --- Task API Routes ---
     $app->group('/tasks', function (Group $group) {
        $group->get('', ListTasksAction::class);          // GET /tasks
        $group->post('', CreateTaskAction::class);         // POST /tasks
        $group->get('/{id:[0-9]+}', ViewTaskAction::class); // GET /tasks/{id} (numeric id)
        $group->put('/{id:[0-9]+}', UpdateTaskAction::class); // PUT /tasks/{id}
        $group->delete('/{id:[0-9]+}', DeleteTaskAction::class); // DELETE /tasks/{id}
    });
};
