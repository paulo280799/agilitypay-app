<?php

namespace App\Repository;
use App\Models\Conta;
class ContaRepository{
    private $conta;

    public function __construct(Conta $conta){
        $this->conta = $conta;
    }

    public function find(string $code): Conta {
        return  $this->conta::where('user_id',$code)->first();
    }

    public function findBychave(string $code) {
        return  $this->conta::select('contas.id','contas.balance','contas.code')->join('users', 'contas.user_id', '=', 'users.id')->where('users.cpf',$code)->first();
    }

    public function create(int $idUser): void {

        $conta = [
            'user_id'=> $idUser,
            'balance' => 1000.24,
            'code'=> $this->generateCode(),
        ];
        $this->conta::create($conta);
    }

    public function updateValue(array $data): void {
        $this->conta::where('code', $data['account_sender'])
        ->decrement('balance',$data['amount']);

        $this->conta::where('code', $data['account_receiver'])
        ->increment('balance',$data['amount']);
    }

    private function generateCode(): string {
        $firstPart = mt_rand(1000, 9999);
        $secondPart = mt_rand(0, 9);
        return "{$firstPart}-{$secondPart}";
    }
}
?>
