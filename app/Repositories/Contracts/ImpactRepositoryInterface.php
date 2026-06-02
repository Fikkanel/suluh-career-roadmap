<?php

namespace App\Repositories\Contracts;

interface ImpactRepositoryInterface
{
    public function getAggregateStats(): array;
    public function recordSurvey(int $userId, string $type, array $answers): mixed;
}
