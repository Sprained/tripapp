<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class User extends Controller
{
    public function insert(Request $request)
    {
        //validação dos dados
        $validate = Validator::make(
            $request->all(),
            [
                'nome' => 'required|string',
                'email' => 'required|string|email',
                'senha' => 'required|string',
                'confirm_senha' => 'required|string|same:senha'
            ],
            [
                'nome.required' => 'Campo nome é obrigatorio!',
                'email.required' => 'Campo email é obrigatorio!',
                'email.email' => 'Email invalido!',
                'senha.required' => 'Campo senha é obrigatorio!',
                'confirm_senha.required' => 'Campo confirm_senha é obrigatorio!',
                'confirm_senha.same' => 'Senhas devem ser iguais!',
            ]
        );

        if($validate->fails()) {
            return response()->json(['message' => $validate->errors()->first()], 400);
        }

        //declaração das variaveis
        $nome = $request->input('nome');
        $email = $request->input('email');
        $senha = Hash::make($request->input('senha'));

        $sql = "INSERT INTO usuario (nome, email, senha) VALUES ('$nome', '$email', '$senha')";
        DB::insert($sql);

        return response()->json(['message' => 'Usuario cadastrado com sucesso!'], 201);
    }

    public function insertAvatar(Request $request)
    {
        $userId = session()->get('user')['id'];
        $image = $request->file('image');

        $filename = time() . '-' . md5($image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
        $image->storeAs("public/user", $filename);

        $sql = "SELECT idavatares, path FROM avatares_usuario WHERE id_usuario = $userId";
        $avatar = DB::selectOne($sql);

        if($avatar) {
            Storage::delete("public/user/$avatar->path");
            $sql = "UPDATE avatares_usuario SET path = '$filename' WHERE idavatares = $avatar->idavatares";
            DB::update($sql);

            return response()->json(['message' => 'Avatar atualizado com sucesso!'], 201);
        }

        $sql = "INSERT INTO avatares_usuario (id_usuario, path) VALUES ($userId, '$filename')";
        DB::insert($sql);

        return response()->json(['message' => 'Avatar cadastrado com sucesso!'], 201);
    }
}