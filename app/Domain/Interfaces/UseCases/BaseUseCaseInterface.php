<?php

namespace App\Domain\Interfaces\UseCases;

interface BaseUseCaseInterface
{
    public function execute(array $data = []): mixed;
}