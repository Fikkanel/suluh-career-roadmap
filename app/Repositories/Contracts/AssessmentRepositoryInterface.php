<?php

namespace App\Repositories\Contracts;

interface AssessmentRepositoryInterface
{
    public function getActiveQuestions(): array;
    public function saveResult(int $userId, array $scores, array $topCareerIds): mixed;
    public function getLatestResult(int $userId): mixed;
}
