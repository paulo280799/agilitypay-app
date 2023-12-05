<?php

namespace App\Repository;
use App\Models\Transacoes;

class TransacoesRepository{
    private $transacoes;

    public function __construct(Transacoes $transacoes){
        $this->transacoes = $transacoes;
    }

    public function listAll() : object{
        return $this->transacoes->all();
    }
    public function create(array $transacao): void {

        $this->transacoes::create($transacao);
    }
}
?>
