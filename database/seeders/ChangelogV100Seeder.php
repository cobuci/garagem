<?php

namespace Database\Seeders;

use App\Models\Changelog;
use App\Models\ChangelogItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ChangelogV100Seeder extends Seeder
{
    public function run(): void
    {
        Changelog::where('version', '1.0.0')->delete();

        $changelog = Changelog::create([
            'version'     => '1.0.0',
            'title'       => 'Novas Métricas, Abas em Relatórios e Melhorias',
            'released_at' => Carbon::now(),
        ]);

        $items = [
            [
                'title'       => 'Abas no Menu de Relatórios',
                'description' => 'O menu de relatórios agora conta com navegação por abas, facilitando o acesso às diferentes análises disponíveis.',
            ],
            [
                'title'       => 'Pico de Vendas por Hora',
                'description' => 'Nova métrica que exibe os horários de maior volume de vendas, ajudando a identificar os períodos mais movimentados.',
            ],
            [
                'title'       => 'Evolução do Ticket Médio',
                'description' => 'Acompanhe a evolução do ticket médio ao longo do tempo e identifique tendências no valor das vendas.',
            ],
            [
                'title'       => 'Produtos Mais Lucrativos',
                'description' => 'Visualize quais produtos geram maior lucratividade para o seu negócio.',
            ],
            [
                'title'       => 'Lucratividade por Categoria',
                'description' => 'Análise de lucratividade agrupada por categoria de produtos.',
            ],
            [
                'title'       => 'Clientes com Risco de Churn',
                'description' => 'Identifique clientes que apresentam sinais de abandono para agir de forma proativa na retenção.',
            ],
            [
                'title'       => 'Dias de Estoque vs. Giro',
                'description' => 'Relação entre os dias de cobertura de estoque e o giro dos produtos, auxiliando na gestão de inventário.',
            ],
            [
                'title'       => 'Concluir Todas as Vendas do Cliente',
                'description' => 'Agora é possível marcar todas as vendas de um cliente como concluídas de uma só vez, diretamente na página do cliente.',
            ],
            [
                'title'       => 'Correções Gerais do Sistema',
                'description' => 'Diversas correções e melhorias de estabilidade foram aplicadas nesta versão.',
            ],
        ];

        foreach ($items as $index => $item) {
            ChangelogItem::create([
                'changelog_id' => $changelog->id,
                'title'        => $item['title'],
                'description'  => $item['description'],
                'sort_order'   => $index,
            ]);
        }
    }
}
