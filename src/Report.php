<?php

namespace KubaWerlos\TypesChecker;

class Report
{
    private $errors = [];

    public function addErrors(string $class, string $error)
    {
        if (!isset($this->errors[$class])) {
            $this->errors[$class] = [];
        }

        $this->errors[$class][] = $error;
    }

    public function isProper(): bool
    {
        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
