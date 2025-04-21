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
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    $app->get('/db-test', function (Request $request, Response $response) {
        try {
            $pdo = $this->get(PDO::class); // Get PDO from container
            $stmt = $pdo->query('SELECT version()');
            $version = $stmt->fetchColumn();

            $stmt = $pdo->query('SELECT COUNT(*) FROM tasks'); // Check tasks table
            $count = $stmt->fetchColumn();

            $payload = json_encode(['db_version' => $version, 'task_count' => $count]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\PDOException $e) {
            $response->getBody()->write(json_encode(['error' => 'DB Connection Failed: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });

     // --- Task API Routes ---
     $app->group('/tasks', function (Group $group) {
        $group->get('', ListTasksAction::class);          // GET /tasks
        $group->post('', CreateTaskAction::class);         // POST /tasks
        $group->get('/{id:[0-9]+}', ViewTaskAction::class); // GET /tasks/{id} (numeric id)
        $group->put('/{id:[0-9]+}', UpdateTaskAction::class); // PUT /tasks/{id}
        $group->delete('/{id:[0-9]+}', DeleteTaskAction::class); // DELETE /tasks/{id}
    });
};
