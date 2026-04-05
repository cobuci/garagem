<?php

namespace App\Models;

use Database\Factories\ChangelogFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int    $id
 * @property string $version
 * @property string $title
 * @property Carbon $released_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, ChangelogItem> $items
 */
class Changelog extends Model
{
    /** @use HasFactory<ChangelogFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'released_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(ChangelogItem::class)->orderBy('sort_order');
    }

    public function seenByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'changelog_user')
            ->withPivot('seen_at');
    }

    /** @param Builder<Changelog> $query */
    public function scopeUnseenBy(Builder $query, User $user): void
    {
        $query->whereDoesntHave('seenByUsers', fn (Builder $q) => $q->where('users.id', $user->id));
    }
}
