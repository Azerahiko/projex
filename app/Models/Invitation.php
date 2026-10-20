<?php

namespace App\Models;

use Database\Factories\InvitationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $email
 * @property string $role
 * @property string $token
 * @property string $status
 * @property int $invited_by
 * @property Carbon $expires_at
 * @property Carbon|null $accepted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Database\Factories\InvitationFactory factory($count = null, $state = [])
 */
class Invitation extends Model
{
    /** @use HasFactory<InvitationFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_REVOKED = 'revoked';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'role',
        'token',
        'status',
        'invited_by',
        'expires_at',
        'accepted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    /**
     * Get the admin who sent the invitation.
     *
     * @return BelongsTo<User, $this>
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Determine if the invitation is still usable.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING && ! $this->expires_at->isPast();
    }

    /**
     * Determine if the invitation has expired.
     */
    public function isExpired(): bool
    {
        return $this->status === self::STATUS_PENDING && $this->expires_at->isPast();
    }

    /**
     * Get the human readable status label.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_ACCEPTED => 'Diterima',
            self::STATUS_EXPIRED => 'Kedaluwarsa',
            self::STATUS_REVOKED => 'Dibatalkan',
            default => 'Menunggu',
        };
    }
}
