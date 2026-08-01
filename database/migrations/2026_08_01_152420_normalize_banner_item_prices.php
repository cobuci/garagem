<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (DB::table('banners')->get(['id', 'design', 'export_design']) as $banner) {
            DB::table('banners')->where('id', $banner->id)->update([
                'design'        => $this->normalize($banner->design),
                'export_design' => $this->normalize($banner->export_design),
            ]);
        }
    }

    private function normalize(?string $json): ?string
    {
        if ($json === null) {
            return null;
        }

        $design = json_decode($json, true);

        foreach ($design['items'] ?? [] as $index => $item) {
            $price = trim((string) ($item['price'] ?? ''));

            if (preg_match('/^R\$\s*\d{1,3}(\.\d{3})*(,\d{2})?$/', $price)) {
                $clean = str_replace(['R$', ' ', '.'], '', $price);
                $design['items'][$index]['price'] = str_replace(',', '.', $clean);
            }
        }

        return json_encode($design);
    }
};
