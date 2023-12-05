<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Repository\UserRepository;
use App\Repository\ContaRepository;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    private $userRepository;
    private $contaRepository;

    public function __construct(UserRepository $userRepository, ContaRepository $contaRepository){
        $this->userRepository = $userRepository;
        $this->contaRepository = $contaRepository;
    }

    public function login(LoginRequest $request):JsonResponse
    {
        $data = $request->validated();
        if (auth()->attempt($data)) {
            $user = auth()->user();
            $token = $user->createToken(json_encode([
                'user' => $user->name,
                'email' => $user->email,
            ]))->plainTextToken;

            return response()->json(['success' => true, 'data' => ['user' => $user, 'token' => $token]], 200);
        }

        return response()->json(['success' => false, 'error' => 'Credenciais inválidas'], 401);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $dadosUser = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'cpf' => $data['cpf'],
            ];

            $user = $this->userRepository->create($dadosUser);
            $this->contaRepository->create($user->id);

            $token = $user->createToken(json_encode([
                'user' => $user->name,
                'email' => $user->email,
            ]))->plainTextToken;

            DB::commit();
            return response()->json(['success' => true, 'data' => ['user' => $user, 'token' => $token]], 200);

        } catch (\Throwable $th) {
            \Log::info($th);
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(): JsonResponse
    {
        $user = auth()->user();
        if ($user) {
            $user->tokens()->delete();

            return response()->json(['success' => true, 'message' => 'Logout realizado com sucesso'], 200);
        }

        return response()->json(['success' => false, 'message' => 'Nenhum usuário autenticado'], 401);
    }
}
