<?php

namespace Database\Seeders;

use App\Models\Changelog;
use App\Models\ChangelogItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ChangelogV110Seeder extends Seeder
{
    public function run(): void
    {
        Changelog::where('version', '1.1.0')->delete();

        $changelog = Changelog::create([
            'version'     => '1.1.0',
            'title'       => 'Novo Dashboard e Métricas de Vendas',
            'released_at' => Carbon::now(),
        ]);

        $items = [
            [
                'title'       => 'Visão Geral de Vendas no Gráfico',
                'description' => 'O gráfico principal agora exibe o Total de Vendas (Pagas + Não Pagas) como série principal, com destaque visual para as Vendas Pagas em linha tracejada violeta. Isso permite uma visualização clara do potencial total de receita vs. o que já foi recebido.',
                'image_path'  => 'changelog/v1.1.0/sales-overview.png',
            ],
            [
                'title'       => 'Média de Vendas Diária',
                'description' => 'Adicionada uma nova métrica no painel de performance que calcula a média de vendas por dia do mês atual, auxiliando na projeção de fechamento mensal.',
                'image_path'  => 'changelog/v1.1.0/daily-average.png',
            ],
            [
                'title'       => 'Novos Cards de Resumo (Hoje, Ontem e Mês)',
                'description' => 'Os cards de performance foram atualizados para mostrar o Total de Vendas de forma mais clara, incluindo um resumo detalhado do que já foi pago e o que ainda está pendente.',
                'image_path'  => 'changelog/v1.1.0/performance-cards.png',
            ],
        ];

        foreach ($items as $index => $item) {
            ChangelogItem::create([
                'changelog_id' => $changelog->id,
                'title'        => $item['title'],
                'description'  => $item['description'],
                'image_path'   => $item['image_path'] ?? null,
                'sort_order'   => $index,
            ]);
        }
    }
}
