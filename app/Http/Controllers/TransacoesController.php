<?php

namespace App\Http\Controllers;

use App\Repository\TransacoesRepository;
use App\Repository\ContaRepository;
use App\Http\Requests\TransactionRequest;
use Illuminate\Support\Facades\DB;
class TransacoesController extends Controller
{
    private $transacoesRepository;
    private $contasRepository;

    public function __construct(TransacoesRepository $transacoesRepository, ContaRepository $contasRepository){
        $this->transacoesRepository = $transacoesRepository;
        $this->contasRepository = $contasRepository;
    }

    public function list()
    {
        $transacoes = $this->transacoesRepository->listAll();
        return response()->json(['success' => true, 'data' => $transacoes], 200);
    }

    public function createTransaction(TransactionRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();

            $user = auth()->user();

            $contaSender =  $this->contasRepository->find($user->id);

            if(!empty($contaSender) && $contaSender->balance = 0 && $contaSender->balance < $data['valor']){
                return response()->json(['success' => false, 'message' => 'Tá lizo?'], 401);
            }

            $contaReciever =  $this->contasRepository->findBychave($data['chave']);

            if(!$contaReciever){
                return response()->json(['success' => false, 'message' => 'Conta destinataria não encontrada'], 401);
            }

            $transacao = [
                'description' => $data['descricao'],
                'amount' => $data['valor'],
                'account_sender' => $contaSender->code,
                'account_receiver' => $contaReciever->code,
            ];

            $this->contasRepository->updateValue($transacao);

            $this->transacoesRepository->create($transacao);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' =>'transferencia realizada com sucesso'
            ], 200);
        } catch (\Throwable $th) {
            \Log::info($th);
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }

    }
}
