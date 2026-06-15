<?php

namespace App\Filament\Resources\WarrantyClaims\Schemas;

use App\Filament\Resources\WarrantyClaims\WarrantyClaimResource;
use App\Models\WarrantyClaim;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class WarrantyClaimForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Claim Review')
                    ->schema([
                        Placeholder::make('claim_number')
                            ->label('Claim Number')
                            ->content(fn (?WarrantyClaim $record): string => $record?->claim_number ?? 'Will be generated automatically'),
                        Placeholder::make('warranty_code')
                            ->label('Warranty Code')
                            ->content(fn (?WarrantyClaim $record): string => $record?->warranty?->warranty_code ?? 'N/A'),
                        Placeholder::make('product')
                            ->label('Product')
                            ->content(fn (?WarrantyClaim $record): string => $record?->warranty?->orderItem?->product_name ?? 'N/A')
                            ->columnSpanFull(),
                        Placeholder::make('customer')
                            ->label('Customer')
                            ->content(fn (?WarrantyClaim $record): string => $record?->warranty?->order?->recipient_name ?? 'N/A'),
                        Placeholder::make('phone')
                            ->label('Phone')
                            ->content(fn (?WarrantyClaim $record): string => $record?->warranty?->order?->recipient_phone ?? 'N/A'),
                        Select::make('status')
                            ->label('Trạng thái xử lý')
                            ->options(WarrantyClaimResource::getStatusOptions())
                            ->native(false)
                            ->required(),
                        Textarea::make('issue_description')
                            ->label('Mô tả lỗi từ khách hàng')
                            ->rows(5)
                            ->disabled()
                            ->columnSpanFull(),
                        Textarea::make('technician_note')
                            ->label('Ghi chú kỹ thuật')
                            ->rows(4)
                            ->columnSpanFull(),
                        Textarea::make('resolution_note')
                            ->label('Lý do từ chối / Kết luận xử lý')
                            ->helperText('Nếu chọn trạng thái Từ chối, hãy nhập rõ lý do để khách hàng nhìn thấy ở trang bảo hành.')
                            ->rows(4)
                            ->columnSpanFull(),
                        Placeholder::make('attachments')
                            ->label('Attachments')
                            ->content(fn (?WarrantyClaim $record): HtmlString => self::renderAttachmentPreview($record?->attachments ?? []))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    private static function renderAttachmentPreview(array $attachments): HtmlString
    {
        $normalized = collect($attachments)
            ->map(function ($attachment): ?array {
                if (is_string($attachment)) {
                    return [
                        'path' => $attachment,
                        'name' => basename($attachment),
                        'mime' => null,
                    ];
                }

                if (! is_array($attachment) || empty($attachment['path'])) {
                    return null;
                }

                return [
                    'path' => (string) $attachment['path'],
                    'name' => (string) ($attachment['name'] ?? basename((string) $attachment['path'])),
                    'mime' => isset($attachment['mime']) ? (string) $attachment['mime'] : null,
                ];
            })
            ->filter()
            ->values();

        if ($normalized->isEmpty()) {
            return new HtmlString('<span class="text-gray-500">No attachments uploaded.</span>');
        }

        $items = $normalized->map(function (array $attachment): string {
            $url = e(WarrantyClaim::attachmentUrl($attachment['path']));
            $name = e($attachment['name']);
            $mime = $attachment['mime'];
            $ext = Str::lower(pathinfo($attachment['path'], PATHINFO_EXTENSION));
            $isImage = ($mime && Str::startsWith($mime, 'image/')) || in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
            $isVideo = ($mime && Str::startsWith($mime, 'video/')) || in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'mkv'], true);

            if ($isImage) {
                return sprintf(
                    '<div style="border:1px solid #e5e7eb;border-radius:10px;padding:10px;"><div style="font-weight:600;margin-bottom:8px;">%s</div><a href="%s" target="_blank" rel="noopener noreferrer"><img src="%s" alt="%s" style="max-width:220px;max-height:220px;border-radius:8px;object-fit:cover;border:1px solid #e5e7eb;"></a></div>',
                    $name,
                    $url,
                    $url,
                    $name,
                );
            }

            if ($isVideo) {
                return sprintf(
                    '<div style="border:1px solid #e5e7eb;border-radius:10px;padding:10px;"><div style="font-weight:600;margin-bottom:8px;">%s</div><video controls preload="metadata" style="max-width:320px;border-radius:8px;border:1px solid #e5e7eb;"><source src="%s"></video><div style="margin-top:8px;"><a href="%s" target="_blank" rel="noopener noreferrer">Open video in new tab</a></div></div>',
                    $name,
                    $url,
                    $url,
                );
            }

            return sprintf(
                '<div style="border:1px solid #e5e7eb;border-radius:10px;padding:10px;"><div style="font-weight:600;margin-bottom:6px;">%s</div><a href="%s" target="_blank" rel="noopener noreferrer">Open attachment</a></div>',
                $name,
                $url,
            );
        })->implode('');

        return new HtmlString('<div style="display:flex;flex-wrap:wrap;gap:12px;">'.$items.'</div>');
    }
}
