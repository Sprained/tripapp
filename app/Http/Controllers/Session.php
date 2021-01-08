<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class Session extends Controller
{
    public function login(Request $request)
    {
        //validação dos dados
        $validate = Validator::make(
            $request->all(),
            [
                'email' => 'required|string|email',
                'senha' => 'required|string'
            ],
            [
                'email.required' => 'Campo email é obrigatorio!',
                'email.email' => 'Email invalido!',
                'senha.required' => 'Campo senha é obrigatorio!'
            ]
        );

        if($validate->fails()) {
            return response()->json(['message' => $validate->errors()->first()], 400);
        }

        //declaração das variaveis
        $email = $request->input('email');
        $senha = $request->input('senha');

        $sql = "SELECT idusuario AS id, senha FROM usuario WHERE email = '$email'";
        $user = DB::selectOne($sql);

        //verificação infos usuario
        if(!$user) {
            return response()->json(['message' => 'Usuario não cadastrado!'], 400);
        }

        if(Hash::check($user->senha, $senha)) {
            return response()->json(['message' => 'Usuario ou senha invalidos!'], 400);
        }

        session()->put('user', ['id' => $user->id]);

        return response()->json(['message' => 'Usuario logado com sucesso!'], 200);
    }

    public function logout()
    {
        session()->flush();

        return response()->json(['message' => 'Usuario deslogado!'], 200);
    }
}
