<?php

namespace App\Observers;

use App\Models\SupplierOrder;
use App\Models\BankAccount;
use App\Models\BankTransaction;

class SupplierOrderObserver
{
    /**
     * Handle the SupplierOrder "updated" event.
     */
    public function updated(SupplierOrder $supplierOrder): void
    {
        // Verificar se o status mudou para 'closed' (encomenda fechada/paga)
        if ($supplierOrder->isDirty('status') && $supplierOrder->status === 'closed') {
            // Buscar conta bancária principal (Conta Corrente Principal)
            $bankAccount = BankAccount::where('nome', 'Conta Corrente Principal')
                ->orWhere('tipo', 'corrente')
                ->first();

            if ($bankAccount) {
                // Criar movimento bancário (débito - saída de dinheiro)
                BankTransaction::create([
                    'bank_account_id' => $bankAccount->id,
                    'data_movimento' => now(),
                    'descricao' => "Pagamento Encomenda {$supplierOrder->number} - {$supplierOrder->supplier->name}",
                    'tipo' => 'debito',
                    'valor' => $supplierOrder->total_value,
                    'referencia' => $supplierOrder->number,
                    'categoria' => 'pagamento',
                    'observacoes' => "Fornecedor: {$supplierOrder->supplier->name}",
                ]);
            }
        }
    }
}
