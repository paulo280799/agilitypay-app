<?php

namespace App\Http\Controllers;

use App\Repository\TransacoesRepository;
use App\Repository\ContaRepository;
use App\Http\Requests\TransactionRequest;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\DB;
class TransacoesController extends Controller
{
    private $transacoesRepository;
    private $contasRepository;
    private $userRepository;

    public function __construct(TransacoesRepository $transacoesRepository, ContaRepository $contasRepository, UserRepository $userRepository){
        $this->transacoesRepository = $transacoesRepository;
        $this->contasRepository = $contasRepository;
        $this->userRepository = $userRepository;
    }

    public function list()
    {
        $user = auth()->user();
        $conta = $this->contasRepository->find($user->id);
        $transacoes = $this->transacoesRepository->listAll($conta->code);

        foreach($transacoes as $transacao) {
            if($conta->code == $transacao->account_sender) {
                $transacao->type = "out";
                $transacao->sender_name = auth()->user()->name;
            } else {
                $transacao->type = "in";
                $transacao->sender_name = $this->userRepository->findById($transacao->sender_id)->name;
            }
        }

        return response()->json(['success' => true, 'data' => $transacoes], 200);
    }

    public function createTransaction(TransactionRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();

            $user = auth()->user();

            $contaSender =  $this->contasRepository->find($user->id);

            if(!empty($contaSender) && $contaSender->balance = 0 && $contaSender->balance < $data['value']){
                return response()->json(['success' => false, 'message' => 'Saldo insuficiente para a transação'], 401);
            }

            $contaReciever =  $this->contasRepository->findBychave($data['key']);

            if(!$contaReciever){
                return response()->json(['success' => false, 'message' => 'Conta destinatária não encontrada'], 401);
            }

            if($contaReciever->code == $contaSender->code) {
                return response()->json(['success' => false, 'message' => 'Não pode fazer transferência para si mesmo'], 401);
            }

            $transacao = [
                'description' => $data['description'],
                'amount' => $data['value'],
                'account_sender' => $contaSender->code,
                'account_receiver' => $contaReciever->code,
                'sender_id' => auth()->user()->id
            ];

            $this->contasRepository->updateValue($transacao);

            $this->transacoesRepository->create($transacao);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Transferência realizada com sucesso'
            ], 200);
        } catch (\Throwable $th) {
            \Log::info($th);
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

    }
}
