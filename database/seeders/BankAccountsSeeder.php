<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BankAccount;

class BankAccountsSeeder extends Seeder
{
    /**
     * Seed bank accounts for the company
     */
    public function run(): void
    {
        echo "💰 Criando contas bancárias da empresa...\n\n";

        // Conta 1: Conta Corrente Principal - Caixa Geral de Depósitos
        $conta1 = BankAccount::create([
            'nome' => 'Conta Corrente Principal',
            'banco' => 'Caixa Geral de Depósitos',
            'iban' => 'PT50003506510001234567890',
            'swift_bic' => 'CGDIPTPL',
            'saldo_inicial' => 25000.00,
            'saldo_atual' => 25000.00,
            'moeda' => 'EUR',
            'tipo' => 'corrente',
            'estado' => 'ativa',
            'observacoes' => 'Conta principal para operações diárias da empresa. Utilizada para pagamentos a fornecedores e recebimentos de clientes.',
        ]);

        echo "✅ Criada: {$conta1->nome} ({$conta1->banco})\n";
        echo "   IBAN: {$conta1->iban}\n";
        echo "   Saldo: " . number_format($conta1->saldo_atual, 2, ',', '.') . " EUR\n\n";

        // Conta 2: Conta Poupança - Millennium BCP
        $conta2 = BankAccount::create([
            'nome' => 'Conta Poupança Empresarial',
            'banco' => 'Millennium BCP',
            'iban' => 'PT50003300000012345678905',
            'swift_bic' => 'BCOMPTPL',
            'saldo_inicial' => 50000.00,
            'saldo_atual' => 50000.00,
            'moeda' => 'EUR',
            'tipo' => 'poupanca',
            'estado' => 'ativa',
            'observacoes' => 'Conta para reservas e poupança da empresa. Utilizada para investimentos e fundo de emergência.',
        ]);

        echo "✅ Criada: {$conta2->nome} ({$conta2->banco})\n";
        echo "   IBAN: {$conta2->iban}\n";
        echo "   Saldo: " . number_format($conta2->saldo_atual, 2, ',', '.') . " EUR\n\n";

        echo "📊 Total em contas bancárias: " . number_format($conta1->saldo_atual + $conta2->saldo_atual, 2, ',', '.') . " EUR\n";
        echo "✅ Seeders executados com sucesso!\n";
    }
}
