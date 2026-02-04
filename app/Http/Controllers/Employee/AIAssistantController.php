<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\AI\HRAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AIAssistantController extends Controller
{
    /**
     * Traiter un message du chatbot RH.
     */
    public function chat(Request $request, HRAssistantService $assistant): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
            'history' => ['sometimes', 'array', 'max:10'],
            'history.*.role' => ['required_with:history', 'string', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:500'],
        ]);

        $response = $assistant->ask(
            $request->user(),
            $validated['message'],
            $validated['history'] ?? []
        );

        return response()->json([
            'response' => $response,
        ]);
    }
}
