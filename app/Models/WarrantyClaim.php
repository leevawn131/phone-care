<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class WarrantyClaim extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'claim_number',
        'warranty_id',
        'handled_by',
        'status',
        'issue_description',
        'attachments',
        'technician_note',
        'resolution_note',
        'received_at',
        'resolved_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'received_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'pending' => 'Chờ xét duyệt',
            'rejected' => 'Từ chối',
            'approved' => 'Chấp nhận',
            'received' => 'Đã nhận được sản phẩm',
            'in_progress' => 'Đang tiến hành bảo hành',
            'completed' => 'Hoàn tất',
        ];
    }

    public static function statusColor(?string $status): string
    {
        return match ($status) {
            'pending' => 'warning',
            'approved', 'received', 'in_progress' => 'info',
            'completed' => 'success',
            'rejected' => 'danger',
            default => 'gray',
        };
    }

    public static function attachmentUrl(string $path): string
    {
        return Storage::url($path);
    }

    public function warranty(): BelongsTo
    {
        return $this->belongsTo(Warranty::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
