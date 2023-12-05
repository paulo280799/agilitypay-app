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
                'valor' => $conta->balance,
            ];

            return response()->json(['success' => true, 'dados' => $dados], 200);
        } catch (\Throwable $th) {
            \Log::info($th);
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }

    }
}
