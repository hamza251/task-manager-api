<?php

declare(strict_types=1);

namespace App\Application\Actions\Task;

use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator;
use Respect\Validation\Exceptions\NestedValidationException;
use Slim\Exception\HttpBadRequestException;

class DeleteTaskAction extends TaskAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $data = (array)$this->getFormData();
        $taskId = (int) $this->resolveArg('id');

        $task = $this->taskRepository->deleteTaskById($taskId);

        $this->logger->info("Task of id `{$taskId}` was deleted.");

        return $this->respondWithData($task);
    }
}
