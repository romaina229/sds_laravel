<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinanceProContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class FinanceProController extends Controller
{
    public function show(): JsonResponse
    {
        $content = Cache::remember('finance_pro_public', now()->addMinutes(30), function () {
            return FinanceProContent::published()
                ->latest('published_at')
                ->latest('id')
                ->first();
        });

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Finance Pro est momentanément indisponible.',
            ], 404);
        }

        return response()->json(['success' => true, 'data' => $content]);
    }
}
