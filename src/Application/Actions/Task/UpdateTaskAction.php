<?php

declare(strict_types=1);

namespace App\Application\Actions\Task;

use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator;
use Respect\Validation\Exceptions\NestedValidationException;
use Slim\Exception\HttpBadRequestException;

class UpdateTaskAction extends TaskAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $data = (array)$this->getFormData();
        $taskId = (int) $this->resolveArg('id');

        // request validation
        $this->validateTaskUpdateRequest($data);

        $title = $data['title'];
        $description = $data['description'] ?? null;
        $completed = $data['completed'] ?? false;

        $task = $this->taskRepository->updateTask($taskId, $title, $description, $completed);

        $this->logger->info("Task of id `{$taskId}` was viewed.");

        return $this->respondWithData($task);
    }


    /**
     * validate task 
     * 
     * @return void
     * @throws HttpBadRequestException
     */
    private function validateTaskUpdateRequest($tasksRequestRawData)
    {
        $validator = Validator::key('title', Validator::stringType()->notEmpty()->length(3, null))
        ->key('description', Validator::optional(Validator::stringType())) // Optional string
        ->key('completed', Validator::optional(Validator::boolVal()), false); // Optional bool, default false if missing

        try {
        
            $validator->assert($tasksRequestRawData);
        
        } catch (NestedValidationException $exception) {
            
            $errors = $exception->getMessages();
            $this->logger->warning("Task update validation failed", $errors);

            throw new HttpBadRequestException($this->request, json_encode(['errors' => $errors]));
        }
    }
}
