<?php

namespace App\Filament\Resources\WarrantyClaims\Schemas;

use App\Filament\Resources\WarrantyClaims\WarrantyClaimResource;
use App\Filament\Resources\Warranties\WarrantyResource;
use App\Models\WarrantyClaim;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class WarrantyClaimInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Claim Overview')
                    ->schema([
                        TextEntry::make('claim_number'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (?string $state): string => WarrantyClaimResource::getStatusColor($state)),
                        TextEntry::make('warranty.warranty_code')
                            ->label('Warranty Code'),
                        TextEntry::make('warranty.orderItem.product_name')
                            ->label('Product')
                            ->columnSpanFull(),
                        TextEntry::make('warranty.order.recipient_name')
                            ->label('Customer'),
                        TextEntry::make('warranty.order.recipient_phone')
                            ->label('Phone'),
                        TextEntry::make('warranty.status')
                            ->label('Warranty Status')
                            ->badge()
                            ->color(fn (?string $state): string => WarrantyResource::getStatusColor($state)),
                        TextEntry::make('issue_description')
                            ->columnSpanFull(),
                        TextEntry::make('technician_note')
                            ->columnSpanFull(),
                        TextEntry::make('resolution_note')
                            ->columnSpanFull(),
                        TextEntry::make('received_at')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('resolved_at')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
                Section::make('Attachments')
                    ->schema([
                        TextEntry::make('attachments')
                            ->label('Attachment Preview')
                            ->state(fn (?WarrantyClaim $record): HtmlString => self::renderAttachmentPreview($record?->attachments ?? []))
                            ->html()
                            ->columnSpanFull(),
                    ]),
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
