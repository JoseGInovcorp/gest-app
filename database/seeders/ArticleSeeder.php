<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = [
            [
                'referencia' => 'ART001',
                'nome' => 'Laptop Dell Inspiron 15',
                'descricao' => 'Laptop Dell Inspiron 15 3000, Intel Core i5, 8GB RAM, 256GB SSD, Windows 11',
                'preco' => 599.99,
                'iva_percentagem' => 23,
                'observacoes' => 'Ideal para uso profissional e estudantil',
                'estado' => 'ativo',
            ],
            [
                'referencia' => 'ART002',
                'nome' => 'Mouse Wireless Logitech',
                'descricao' => 'Mouse sem fios Logitech M705 Marathon com bateria de 3 anos',
                'preco' => 49.99,
                'iva_percentagem' => 23,
                'observacoes' => 'Ergonómico e durável',
                'estado' => 'ativo',
            ],
            [
                'referencia' => 'ART003',
                'nome' => 'Teclado Mecânico',
                'descricao' => 'Teclado mecânico RGB com switches Cherry MX Blue',
                'preco' => 89.99,
                'iva_percentagem' => 23,
                'observacoes' => 'Para gamers e programadores',
                'estado' => 'ativo',
            ],
            [
                'referencia' => 'ART004',
                'nome' => 'Monitor 24" Full HD',
                'descricao' => 'Monitor LED 24 polegadas, resolução 1920x1080, HDMI e VGA',
                'preco' => 149.99,
                'iva_percentagem' => 23,
                'observacoes' => null,
                'estado' => 'ativo',
            ],
            [
                'referencia' => 'ART005',
                'nome' => 'Serviço de Consultoria IT',
                'descricao' => 'Consultoria em tecnologias de informação por hora',
                'preco' => 75.00,
                'iva_percentagem' => 23,
                'observacoes' => 'Inclui análise de sistemas e recomendações',
                'estado' => 'ativo',
            ],
            [
                'referencia' => 'ART006',
                'nome' => 'Impressora Multifunções',
                'descricao' => 'Impressora HP DeskJet 3760 - Imprime, copia e digitaliza',
                'preco' => 79.99,
                'iva_percentagem' => 23,
                'observacoes' => 'Compacta e WiFi',
                'estado' => 'inativo',
            ],
        ];

        foreach ($articles as $articleData) {
            Article::create($articleData);
        }
    }
}
