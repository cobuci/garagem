<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportLegacyDataJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public string $filePath) {}

    public function handle(): void
    {
        if (! Storage::exists($this->filePath)) {
            return;
        }

        $sql = Storage::get($this->filePath);

        DB::transaction(function () use ($sql) {
            $this->importCategories($sql);
            $this->importProducts($sql);
        });

        Storage::delete($this->filePath);
    }

    protected function importCategories(string $sql): void
    {
        if (! preg_match('/INSERT INTO `categories` \(`id`, `name`, `icon`, `created_at`, `updated_at`\) VALUES\s*(.*?);/s', $sql, $matches)) {
            return;
        }

        $rows = $this->splitSqlRows($matches[1]);

        foreach ($rows as $index => $row) {
            $data = $this->parseSqlRow($row);

            if (count($data) < 5) {
                continue;
            }

            $id = (int) $data[0];
            $name = $data[1];
            $icon = isset($data[2]) ? $this->mapIcon($data[2]) : null;
            $created_at = (isset($data[3]) && $data[3] !== '0000-00-00 00:00:00') ? Carbon::parse($data[3]) : now();
            $updated_at = (isset($data[4]) && $data[4] !== '0000-00-00 00:00:00') ? Carbon::parse($data[4]) : now();

            Category::updateOrCreate(
                ['id' => $id],
                [
                    'name'       => $name,
                    'icon'       => $icon,
                    'sort_order' => $index + 1,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at,
                ],
            );
        }
    }

    protected function importProducts(string $sql): void
    {
        if (! preg_match_all('/INSERT INTO `products` \(`id`, `name`, `brand`, `weight`, `cost`, `sale`, `amount`, `expiration_date`, `category_id`, `upc`\) VALUES\s*(.*?);/s', $sql, $allMatches)) {
            return;
        }

        foreach ($allMatches[1] as $values) {
            $rows = $this->splitSqlRows($values);

            foreach ($rows as $row) {
                $data = $this->parseSqlRow($row);

                if (count($data) < 10) {
                    continue;
                }

                $category_id = (int) $data[8];

                if (! Category::where('id', $category_id)->exists()) {
                    Category::create([
                        'id'   => $category_id,
                        'name' => "Category {$category_id}",
                    ]);
                }

                Product::updateOrCreate(
                    ['id' => (int) $data[0]],
                    [
                        'category_id'     => $category_id,
                        'name'            => $data[1],
                        'brand'           => $data[2],
                        'weight'          => $data[3],
                        'upc'             => (! isset($data[9]) || $data[9] === 'NULL' || empty($data[9])) ? null : $data[9],
                        'stock_quantity'  => (int) $data[6],
                        'unit_cost'       => (float) $data[4],
                        'sale_price'      => (float) $data[5],
                        'expiration_date' => (! isset($data[7]) || $data[7] === 'NULL' || empty($data[7]) || str_starts_with($data[7], '0000-00-00') || str_starts_with($data[7], '-0001')) ? null : Carbon::parse($data[7]),
                    ],
                );
            }
        }
    }

    protected function mapIcon(string $legacyIcon): string
    {
        if (! preg_match('/fa-([a-z-]+)/', $legacyIcon, $matches)) {
            return $legacyIcon;
        }

        if ($matches[1] === 'solid' && preg_match_all('/fa-([a-z-]+)/', $legacyIcon, $allMatches)) {
            return $allMatches[1][count($allMatches[1]) - 1];
        }

        return $matches[1];
    }

    protected function parseSqlRow(string $row): array
    {
        $columns = [];
        $current = '';
        $inString = false;
        $quoteChar = '';

        for ($i = 0; $i < strlen($row); $i++) {
            $char = $row[$i];

            if ($char === '\\') {
                if ($i + 1 < strlen($row)) {
                    $nextChar = $row[$i + 1];
                    if ($nextChar === "'" || $nextChar === '"' || $nextChar === '\\') {
                        $current .= $nextChar;
                        $i++;
                        continue;
                    }
                }
                $current .= $char;
            } elseif ($char === "'" || $char === '"') {
                if ($inString && $quoteChar === $char) {
                    if ($i + 1 < strlen($row) && $row[$i + 1] === $char) {
                        $current .= $char;
                        $i++;
                    } else {
                        $inString = false;
                    }
                } elseif (! $inString) {
                    $inString = true;
                    $quoteChar = $char;
                } else {
                    $current .= $char;
                }
            } elseif ($char === ',' && ! $inString) {
                $columns[] = $this->cleanValue($current);
                $current = '';
            } else {
                $current .= $char;
            }
        }

        $columns[] = $this->cleanValue($current);

        return $columns;
    }

    protected function cleanValue(string $value): ?string
    {
        $value = trim($value);
        if ($value === 'NULL' || $value === 'null') {
            return null;
        }

        return trim($value, "'\"");
    }

    protected function splitSqlRows(string $values): array
    {
        $rows = [];
        $current = '';
        $inString = false;
        $quoteChar = '';
        $depth = 0;

        for ($i = 0; $i < strlen($values); $i++) {
            $char = $values[$i];

            if ($char === '\\') {
                $current .= $char;
                if ($i + 1 < strlen($values)) {
                    $current .= $values[$i + 1];
                    $i++;
                }
            } elseif ($char === "'" || $char === '"') {
                $current .= $char;
                if ($inString && $quoteChar === $char) {
                    if ($i + 1 < strlen($values) && $values[$i + 1] === $char) {
                        $current .= $values[$i + 1];
                        $i++;
                    } else {
                        $inString = false;
                    }
                } elseif (! $inString) {
                    $inString = true;
                    $quoteChar = $char;
                }
            } elseif ($char === '(' && ! $inString) {
                $depth++;
                if ($depth === 1) {
                    $current = '';
                } else {
                    $current .= $char;
                }
            } elseif ($char === ')' && ! $inString) {
                $depth--;
                if ($depth === 0) {
                    $rows[] = $current;
                    $current = '';
                } else {
                    $current .= $char;
                }
            } else {
                $current .= $char;
            }
        }

        return array_filter(array_map('trim', $rows));
    }
}
