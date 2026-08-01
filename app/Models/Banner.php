<?php

namespace App\Models;

use App\Enums\BannerFormat;
use App\Enums\BannerJobStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int             $id
 * @property int             $user_id
 * @property string          $name
 * @property BannerFormat    $format
 * @property array           $design
 * @property ?string         $background_prompt
 * @property ?string         $background_path
 * @property BannerJobStatus $background_status
 * @property BannerJobStatus $export_status
 * @property ?string         $export_png_path
 * @property ?string         $export_pdf_path
 * @property ?Carbon         $created_at
 * @property ?Carbon         $updated_at
 * @property User            $user
 */
class Banner extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'format'            => BannerFormat::class,
            'design'            => 'array',
            'background_status' => BannerJobStatus::class,
            'export_status'     => BannerJobStatus::class,
        ];
    }

    public static function defaultDesign(): array
    {
        return [
            'title'            => 'PROMOÇÕES',
            'subtitle'         => 'Confira nossos preços',
            'footer'           => '',
            'background_color' => '#0f172a',
            'accent_color'     => '#38bdf8',
            'text_color'       => '#ffffff',
            'items'            => [
                ['name' => 'Produto', 'note' => '', 'price' => 'R$ 0,00'],
            ],
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
