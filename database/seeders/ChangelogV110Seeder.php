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
                'title'       => 'Layout Unificado do Dashboard',
                'description' => 'Reorganizamos os componentes do dashboard para eliminar espaços vazios e garantir que todas as informações importantes estejam visíveis de forma compacta e organizada, especialmente em telas maiores.',
                'image_path'  => 'changelog/v1.1.0/dashboard-layout.png',
            ],
            [
                'title'       => 'Melhorias na Legenda e Cores',
                'description' => 'A legenda do gráfico foi atualizada para incluir o Total e as Vendas Pagas separadamente, com ícones padronizados para garantir consistência visual em todo o sistema.',
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
