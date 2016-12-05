<?php

namespace KubaWerlos\TypesChecker;

class Report
{
    private $errors = [];

    private $itemsCount = 0;

    public function addErrors(\ReflectionClass $class, string $error)
    {
        $key = $this->getKey($class);

        if (!isset($this->errors[$key])) {
            $this->errors[$key] = [];
        }

        $this->errors[$key][] = $error;
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

    private function getKey(\ReflectionClass $class): string
    {
        switch (true) {
            case $class->isInterface():
                $type = 'Interface';
                break;
            case $class->isTrait():
                $type = 'Trait';
                break;
            default:
                $type = 'Class';
                break;
        }

        return $type.' '.$class->getName();
    }
}
