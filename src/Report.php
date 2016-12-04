<?php

namespace KubaWerlos\TypesChecker;

class Report
{
    private $errors = [];

    private $itemsCount = 0;

    public function addErrors(string $class, string $error)
    {
        if (!isset($this->errors[$class])) {
            $this->errors[$class] = [];
        }

        $this->errors[$class][] = $error;
    }

    public function incrementItemsCount()
    {
        ++$this->itemsCount;
    }

    public function isProper(): bool
    {
        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getItemsCount(): int
    {
        return $this->itemsCount;
    }
}
