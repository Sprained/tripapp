<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Address extends Controller
{
    public function insert(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'rua' => 'required|string',
                'cidade' => 'required|string',
                'numero' => 'string',
                'estado' => 'required|string',
                'uf' => 'required|string|min:2|max:2',
                'cep' => 'required|string|min:8|max:8',
                'municipio' => 'required|string',
                'complemento' => 'string'
            ],
            [
                'rua.required' => 'Campo Rua é obrigatório!',
                'cidade.required' => 'Campo Cidade é obrigatório!',
                'estado.required' => 'Campo Estado é obrigatório!',
                'uf.required' => 'Campo UF é obrigatório!',
                'uf.max' => 'Campo UF está inválido!',
                'uf.min' => 'Campo UF está inválido!',
                'cep.required' => 'Campo CEP é obrigatório!',
                'cep.max' => 'Campo CEP é inválido!',
                'cep.min' => 'Campo CEP é inválido!',
                'municipio.required' => 'Campo Municipio é obrigatório!'
            ]
        );

        if($validate->fails()){
            return response()->json(['message' => $validate->errors()->first()], 400);
        }

        $id_usuario = session()->get('user')['id'];
        $rua = $request->input('rua');
        $cidade = $request->input('cidade');
        $numero = $request->input('numero');
        $estado = $request->input('estado');
        $uf = $request->input('uf');
        $cep = $request->input('cep');
        $municipio = $request->input('municipio');
        $complemento = $request->input('complemento');

        $values = "";

        $valores = "";

        if($complemento){
            $values .= ", complemento";
            $valores .= ", '$complemento'";
        }

        if($numero){
            $values .= ", numero";
            $valores .= ", '$numero'";
        }

        $sql = "INSERT INTO endereco_usuario (rua, cidade, estado, uf, cep, municipio, id_usuario $values) VALUES ('$rua', '$cidade', '$estado', '$uf', '$cep', '$municipio', $id_usuario $valores)";
        DB::insert($sql);

        return response()->json(['message' => 'Endereço cadastrado com sucesso!'], 201);
    }

    public function list()
    {
        $usuario_id = session()->get('user')['id'];

        $sql = "SELECT idendereco, rua, cidade, estado, uf, cep, municipio, complemento FROM endereco_usuario WHERE id_usuario = $usuario_id";
        $select = DB::select($sql);

        return response()->json($select);
    }

    public function delete($id)
    {
        $sql = "SELECT idendereco FROM endereco_usuario WHERE idendereco = $id";
        $select = DB::selectOne($sql);

        if(!$select){
            return response()->json(['message' => 'Endereço não cadastrado na base de dados!'], 400);
        }

        $sql = "DELETE FROM endereco_usuario WHERE idendereco = $id";
        DB::delete($sql);

        return response()->json(['message' => 'Endereço deletado com sucesso!']);
    }
}