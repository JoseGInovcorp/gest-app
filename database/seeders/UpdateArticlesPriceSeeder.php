<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

class UpdateArticlesPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Atualizar todos os artigos para calcular preco_com_iva
        Article::all()->each(function ($article) {
            $article->save(); // O boot event calculará o preco_com_iva automaticamente
        });

        $this->command->info('Artigos atualizados com sucesso! Total: ' . Article::count());
    }
}
