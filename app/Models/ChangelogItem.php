<?php

namespace App\Models;

use Database\Factories\ChangelogItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int     $id
 * @property int     $changelog_id
 * @property string  $title
 * @property string  $description
 * @property ?string $image_path
 * @property int     $sort_order
 */
class ChangelogItem extends Model
{
    /** @use HasFactory<ChangelogItemFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function changelog(): BelongsTo
    {
        return $this->belongsTo(Changelog::class);
    }
}
