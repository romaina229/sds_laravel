<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceProContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FinanceProContentAdminController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => FinanceProContent::latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        $content = FinanceProContent::create($data);
        $this->clearCache();

        return response()->json(['success' => true, 'data' => $content], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $content = FinanceProContent::findOrFail($id);
        $data = $this->validated($request, $id);

        if (!empty($data['title']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $content->update($data);
        $this->clearCache();

        return response()->json(['success' => true, 'data' => $content->fresh()]);
    }

    public function publish(int $id): JsonResponse
    {
        $content = FinanceProContent::findOrFail($id);

        FinanceProContent::where('id', '!=', $content->id)->update([
            'published' => false,
        ]);

        $content->update([
            'published' => true,
            'published_at' => now(),
        ]);

        $this->clearCache();

        return response()->json(['success' => true, 'data' => $content->fresh()]);
    }

    public function unpublish(int $id): JsonResponse
    {
        $content = FinanceProContent::findOrFail($id);
        $content->update(['published' => false]);
        $this->clearCache();

        return response()->json(['success' => true, 'data' => $content->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $content = FinanceProContent::findOrFail($id);
        $content->delete();
        $this->clearCache();

        return response()->json(['success' => true, 'message' => 'Contenu Finance Pro supprimé.']);
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|max:255|unique:finance_pro_contents,slug,' . $id,
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'benefits' => 'nullable|array',
            'faq' => 'nullable|array',
            'hero_image' => 'nullable|string|max:2048',
            'demo_url' => 'nullable|url|max:2048',
            'price_fcfa' => 'nullable|numeric|min:0',
            'price_euro' => 'nullable|numeric|min:0',
            'price_period' => 'nullable|string|max:100',
            'published' => 'sometimes|boolean',
        ]);
    }

    private function clearCache(): void
    {
        Cache::forget('finance_pro_content');
    }
}
