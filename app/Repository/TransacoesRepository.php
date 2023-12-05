<?php

namespace App\Repository;
use App\Models\Transacoes;
use Illuminate\Database\Eloquent\Collection;

class TransacoesRepository{
    private $transacoes;

    public function __construct(Transacoes $transacoes){
        $this->transacoes = $transacoes;
    }

    public function listAll(string $code) : Collection {
        return $this->transacoes
            ->where('account_sender', $code)
            ->orWhere('account_receiver', $code)
            ->get();
    }

    public function create(array $transacao): void {

        $this->transacoes::create($transacao);
    }
}
?>
