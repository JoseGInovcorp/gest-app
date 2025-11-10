<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateArticlesWithNewFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gamas = ['Premium', 'Standard', 'Básico'];

        Article::all()->each(function ($article, $index) use ($gamas) {
            $article->update([
                'tipo' => $index % 3 === 0 ? 'servico' : 'produto',
                'gama' => $gamas[$index % 3],
                'stock_quantidade' => $article->tipo === 'produto' ? rand(0, 100) : 0,
                'data_criacao' => now()->subDays(rand(1, 365))->toDateString(),
            ]);
        });

        $this->command->info('Artigos atualizados com sucesso!');
    }
}
