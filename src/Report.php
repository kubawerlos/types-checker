<?php

namespace KubaWerlos\TypesChecker;

class Report
{
    private $errors = [];

    private $itemsCount = 0;

    public function addErrors(\ReflectionMethod $method, string $error)
    {
        $key = $this->getKey($method->getDeclaringClass());

        if (!isset($this->errors[$key][$method->getName()])) {
            $this->errors[$key][$method->getName()] = [];
        }

        $this->errors[$key][$method->getName()][] = $error;
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

        return sprintf('%s %s', $type, $class->getName());
    }
}
