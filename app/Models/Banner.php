<?php

namespace App\Models;

use App\Enums\BannerFont;
use App\Enums\BannerFormat;
use App\Enums\BannerIntensity;
use App\Enums\BannerJobStatus;
use App\Enums\BannerMood;
use App\Enums\BannerTheme;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int              $id
 * @property int              $user_id
 * @property string           $name
 * @property BannerFormat     $format
 * @property array            $design
 * @property ?string          $background_prompt
 * @property ?BannerTheme     $background_theme
 * @property ?BannerMood      $background_mood
 * @property ?BannerIntensity $background_intensity
 * @property ?string          $background_path
 * @property BannerJobStatus  $background_status
 * @property BannerJobStatus  $export_status
 * @property ?string          $export_png_path
 * @property ?string          $export_pdf_path
 * @property ?array           $export_design
 * @property ?Carbon          $created_at
 * @property ?Carbon          $updated_at
 * @property User             $user
 */
class Banner extends Model
{
    public const LOGO_PATH = 'images/brand/logo-46-garagem.png';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'format'               => BannerFormat::class,
            'design'               => 'array',
            'background_theme'     => BannerTheme::class,
            'background_mood'      => BannerMood::class,
            'background_intensity' => BannerIntensity::class,
            'background_status'    => BannerJobStatus::class,
            'export_status'        => BannerJobStatus::class,
            'export_design'        => 'array',
        ];
    }

    public static function defaultDesign(): array
    {
        return [
            'title'            => 'PROMOÇÕES',
            'subtitle'         => 'Confira nossos preços',
            'footer'           => '',
            'title_font'       => BannerFont::InstrumentSans->value,
            'text_font'        => BannerFont::InstrumentSans->value,
            'background_color' => '#0f172a',
            'accent_color'     => '#38bdf8',
            'text_color'       => '#ffffff',
            'show_logo'        => true,
            'logo_position'    => 'top',
            'logo_align'       => 'center',
            'logo_size'        => 'medium',
            'items'            => [
                ['name' => 'Produto', 'note' => '', 'price' => 'R$ 0,00'],
            ],
            'texts' => [],
        ];
    }

    public static function brandPalettes(): array
    {
        return [
            'brand_dark' => [
                'background_color' => '#0b1526',
                'accent_color'     => '#38b6f8',
                'text_color'       => '#ffffff',
            ],
            'brand_light' => [
                'background_color' => '#f8fafc',
                'accent_color'     => '#2da7ef',
                'text_color'       => '#0f172a',
            ],
        ];
    }

    public static function presets(): array
    {
        return [
            'barbecue' => [
                'title'            => 'ESPETINHOS',
                'subtitle'         => 'Direto da brasa',
                'footer'           => 'Peça já o seu!',
                'background_color' => '#1a0f0a',
                'accent_color'     => '#f97316',
                'text_color'       => '#fff7ed',
                'items'            => [
                    ['name' => 'Bovino', 'note' => '', 'price' => 'R$ 0,00'],
                    ['name' => 'Frango', 'note' => '', 'price' => 'R$ 0,00'],
                    ['name' => 'Linguiça', 'note' => '', 'price' => 'R$ 0,00'],
                    ['name' => 'Kafta', 'note' => '', 'price' => 'R$ 0,00'],
                    ['name' => 'Medalhão', 'note' => '5 uni', 'price' => 'R$ 0,00'],
                ],
            ],
            'drinks' => [
                'title'            => 'BEBIDAS GELADAS',
                'subtitle'         => 'Para refrescar seu dia',
                'footer'           => '',
                'background_color' => '#082f49',
                'accent_color'     => '#38bdf8',
                'text_color'       => '#f0f9ff',
                'items'            => [
                    ['name' => 'Cerveja lata', 'note' => '', 'price' => 'R$ 0,00'],
                    ['name' => 'Long neck', 'note' => '', 'price' => 'R$ 0,00'],
                    ['name' => 'Refrigerante', 'note' => '', 'price' => 'R$ 0,00'],
                    ['name' => 'Água', 'note' => '', 'price' => 'R$ 0,00'],
                ],
            ],
            'snacks' => [
                'title'            => 'PETISCOS',
                'subtitle'         => 'Bora beliscar',
                'footer'           => '',
                'background_color' => '#1c1917',
                'accent_color'     => '#fbbf24',
                'text_color'       => '#fefce8',
                'items'            => [
                    ['name' => 'Batata frita', 'note' => '', 'price' => 'R$ 0,00'],
                    ['name' => 'Frango a passarinho', 'note' => '', 'price' => 'R$ 0,00'],
                    ['name' => 'Torresmo', 'note' => '', 'price' => 'R$ 0,00'],
                ],
            ],
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
