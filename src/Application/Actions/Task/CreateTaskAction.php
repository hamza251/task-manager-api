<?php // src/Application/Action/Task/CreateTaskAction.php
declare(strict_types=1);

namespace App\Application\Actions\Task;

use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator;
use Respect\Validation\Exceptions\NestedValidationException;
use Slim\Exception\HttpBadRequestException; // For validation errors

class CreateTaskAction extends TaskAction
{

    /** {@inheritdoc} */
    protected function action(): Response
    {
        $data = (array)$this->getFormData();

        // request validation
        $this->validateTaskStoreRequest($data);

        $title = $data['title'];
        $description = $data['description'] ?? null;
        $completed = $data['completed'] ?? false;

        $newTask = $this->taskRepository->createTask($title, $description, $completed);

        $this->logger->info("Task `{$newTask->getTitle()}` [{$newTask->getId()}] was created.");

        // Return 201 Created status code with the new task data
        return $this->respondWithData($newTask, 201);
    }

    /**
     * validate task 
     * 
     * @return void
     * @throws HttpBadRequestException
     */
    private function validateTaskStoreRequest($tasksRequestRawData)
    {
        $validator = Validator::key('title', Validator::stringType()->notEmpty()->length(3, null))
        ->key('description', Validator::optional(Validator::stringType())) // Optional string
        ->key('completed', Validator::optional(Validator::boolVal()), false); // Optional bool, default false if missing

        try {
        
            $validator->assert($tasksRequestRawData);
        
        } catch (NestedValidationException $exception) {
            
            $errors = $exception->getMessages();
            $this->logger->warning("Task creation validation failed", $errors);

            throw new HttpBadRequestException($this->request, json_encode(['errors' => $errors]));
        }
    }
}