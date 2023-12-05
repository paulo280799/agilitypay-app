<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\ContaRepository;

class ContaController extends Controller
{
    private $contaRepository;

    public function __construct(ContaRepository $contaRepository){
        $this->contaRepository = $contaRepository;
    }
    public function dadosConta(Request $request){
        try {
            $user = auth()->user();

            $conta =  $this->contaRepository->find($user->id);

            $dados = [
                'user' => $user->name,
                'balance' => $conta->balance,
            ];

            return response()->json(['success' => true, 'data' => $dados], 200);
        } catch (\Throwable $th) {
            \Log::info($th);
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

    }

}
