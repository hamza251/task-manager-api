<?php

declare(strict_types=1);

namespace App\Domain\Task;

use App\Domain\DomainException\DomainRecordNotFoundException;

class TaskNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The requested task does not exist.';
}