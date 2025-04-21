<?php

declare(strict_types=1);

namespace App\Application\Actions\Task;

use Psr\Http\Message\ResponseInterface as Response;

class ViewTaskAction extends TaskAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $taskId = (int) $this->resolveArg('id');
        
        $task = $this->taskRepository->findTaskById($taskId);

        $this->logger->info("task of id `{$taskId}` was viewed.");

        return $this->respondWithData($task);
    }
}
