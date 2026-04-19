<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function chatbot(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $message = trim((string) $validated['message']);
        $userId = $request->user()?->id;
        $sessionId = $request->session()->getId();

        ChatMessage::query()->create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'message' => $message,
            'is_bot' => false,
        ]);

        $catalogProducts = Product::query()
            ->with(['variants' => fn ($query) => $query->where('is_active', true)->orderBy('id')])
            ->where('is_active', true)
            ->latest('id')
            ->take(12)
            ->get();

        $productText = $catalogProducts
            ->map(function (Product $product): string {
                $price = number_format($product->getDisplayPrice());

                return "- {$product->name} - {$price} VND";
            })
            ->implode("\n");

        $prompt = "Bạn là trợ lý bán phụ kiện điện thoại của PhoneCare.\n"
            . "Luôn trả lời ngắn gọn, thân thiện bằng tiếng Việt có dấu.\n"
            . "Nếu khách cần tư vấn sản phẩm thì gợi ý theo nhu cầu thực tế.\n"
            . "Danh sách sản phẩm tham khảo:\n{$productText}\n"
            . "Khách hỏi: {$message}";

        $reply = 'Xin lỗi, hệ thống AI đang bận. Bạn vui lòng thử lại sau.';

        try {
            $res = Http::timeout(25)->post('http://localhost:11434/api/generate', [
                'model' => 'mistral',
                'prompt' => $prompt,
                'stream' => false,
            ]);

            $data = $res->json();
            $modelReply = trim((string) ($data['response'] ?? ''));
            if ($modelReply !== '') {
                $reply = $modelReply;
            }
        } catch (\Throwable) {
            // Keep the fallback reply when the local AI service is unavailable.
        }

        $suggestedProducts = $this->resolveSuggestedProducts($message);

        ChatMessage::query()->create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'message' => $reply,
            'is_bot' => true,
        ]);

        return response()->json([
            'reply' => $reply,
            'products' => $suggestedProducts,
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $messages = ChatMessage::query()
            ->when(
                $request->user(),
                fn ($query) => $query->where('user_id', $request->user()->id),
                fn ($query) => $query->whereNull('user_id')->where('session_id', $request->session()->getId())
            )
            ->orderByDesc('id')
            ->limit(50)
            ->get(['id', 'message', 'is_bot'])
            ->reverse()
            ->values();

        return response()->json([
            'messages' => $messages,
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function resolveSuggestedProducts(string $message): array
    {
        $tokens = collect(preg_split('/\s+/u', Str::lower($message)) ?: [])
            ->map(fn (string $token): string => trim($token))
            ->filter(fn (string $token): bool => mb_strlen($token) >= 3)
            ->take(8)
            ->values();

        $query = Product::query()
            ->with([
                'brand',
                'images',
                'variants' => fn ($variantQuery) => $variantQuery->where('is_active', true)->orderBy('id'),
            ])
            ->where('is_active', true);

        if ($tokens->isNotEmpty()) {
            $query->where(function ($builder) use ($tokens): void {
                foreach ($tokens as $token) {
                    $builder->orWhere('name', 'like', "%{$token}%")
                        ->orWhere('short_description', 'like', "%{$token}%");
                }
            });
        }

        $products = $query->latest('id')->take(3)->get();

        if ($products->isEmpty()) {
            $products = Product::query()
                ->with([
                    'brand',
                    'images',
                    'variants' => fn ($variantQuery) => $variantQuery->where('is_active', true)->orderBy('id'),
                ])
                ->where('is_active', true)
                ->latest('id')
                ->take(3)
                ->get();
        }

        return $products->map(fn (Product $product): array => [
            'name' => $product->name,
            'url' => route('products.show', $product->slug),
            'image' => $product->getPrimaryImageUrl(),
            'price' => number_format($product->getDisplayPrice()) . ' VND',
            'brand' => $product->brand?->name,
        ])->values()->all();
    }
}
