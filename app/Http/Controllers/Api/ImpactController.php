<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ImpactCalculatorService;

class ImpactController extends Controller
{
    public function __construct(
        private readonly ImpactCalculatorService $impact,
    ) {}

    public function show()
    {
        return response()->json([
            'data' => $this->impact->getPublicStats(),
        ]);
    }
}
