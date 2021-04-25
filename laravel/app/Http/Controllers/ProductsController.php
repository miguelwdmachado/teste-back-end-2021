<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;
use Tymon\JWTAuth\Contracts\Providers\Auth;
//use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductsController extends Controller
{
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index(Request $request)
  {
    $produtos = Products::all();
    if ($produtos->isEmpty()) {
      return response()->json(array('message' => 'Nenhum produto encontrado'));
    } else {
      return responder()->success(Products::paginate())->respond();
    }
  }

  /**
  * Store a newly created resource in storage.
  *
  * @param  \Illuminate\Http\Request  $request
  * @return \Illuminate\Http\Response
  */
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(),
    [
      'nome' => 'required|string|regex:/^(.){5,80}+$/',
      'peso' => 'required|numeric',
      'valor' => 'required|numeric',
      'image' => 'required|image|mimes:jpg,png',
    ], [
      'nome.required' => 'O nome de produto é obrigatório',
      'peso.required'  => 'O peso do produto é obrigatório',
      'valor.required' => 'O valor do produto é obrigatório',
      'image.required' => 'Uma imagem do produto é obrigatória',
      'image.images' => 'O tipo de imagem é inválido',
    ]);

    if ($validator->fails()) {
      return response()->json(array('message' => 'Um dos campos não foi informado ou é inválido'));
    } else {

      // Define o valor default para a variável que contém o nome da imagem
      $nameFile = null;

      // Verifica se informou o arquivo e se é válido
      if ($request->hasFile('image') && $request->file('image')->isValid()) {

        // Define um aleatório para o arquivo baseado no timestamps atual
        $name = uniqid(date('HisYmd'));

        // Recupera a extensão do arquivo
        $extension = $request->image->extension();

        // Define finalmente o nome
        $nameFile = "{$name}.{$extension}";
        $nfile = $nameFile;

        // Faz o upload:
        $upload = $request->image->storeAs('images', $nameFile, 'local');
        // Se tiver funcionado o arquivo foi armazenado em storage/app/public/images/nomedinamicoarquivo.extensao
        if ( !$upload ) {
          return response()->json(array('message' => 'Arquivo de imagem não enviado'));
        } else {
          $product = Products::create([
            'nome' => $request->nome,
            'peso' => $request->peso,
            'valor' => $request->valor,
            'imagem' => $nfile,
          ]);
          $product->save();

          return responder()->success(['message' => 'Produto incluído com sucesso', Products::paginate()])->respond();
        }
      }
    }
  }

  /**
  * Update the specified resource in storage.
  *
  * @param  \Illuminate\Http\Request  $request
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function update(Request $request)
  {
    $validator = Validator::make($request->all(),
    [
      'nome' => 'required|string|regex:/^(.){5,80}+$/',
      'peso' => 'required|numeric',
      'valor' => 'required|numeric',
      'image' => 'required|image|mimes:jpg,png',
    ], [
      'nome.required' => 'O nome de produto é obrigatório',
      'peso.required'  => 'O peso do produto é obrigatório',
      'valor.required' => 'O valor do produto é obrigatório',
      'image.required' => 'Uma imagem do produto é obrigatória',
      'image.images' => 'O tipo de imagem é inválido',
    ]);

    if ($validator->fails()) {
      return response()->json(array('message' => 'Um dos campos não foi informado ou é inválido'));
    } else {
      $produto = Products::where('id', $request->id)->first();
      if ($produto) {

        $nameFile = null;

        $old_imagem = $produto->imagem;

        // Verifica se informou o arquivo e se é válido
        if ($request->hasFile('image') && $request->file('image')->isValid()) {

          // Define um aleatório para o arquivo baseado no timestamps atual
          $name = uniqid(date('HisYmd'));

          // Recupera a extensão do arquivo
          $extension = $request->image->extension();

          // Define finalmente o nome
          $nameFile = "{$name}.{$extension}";
          $nfile = $nameFile;

          // Faz o upload:
          $upload = $request->image->storeAs('images', $nameFile, 'local');
          // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao
          if ( !$upload ) {
            return response()->json(array('message' => 'Arquivo de imagem não enviado'));
          } else {

            Products::where('id', $request->id)->update(array(
              'nome' => $request->nome,
              'peso' => $request->peso,
              'valor' => $request->valor,
              'imagem' => $nfile,
            ));
            Storage::delete('images/'.$old_imagem);
            return responder()->success(['message' => 'Produto alterado com sucesso',Products::paginate()])->respond();
          }
        }
      } else {
        return response()->json(array('message' => 'Produto não encontrado'));
      }
    }
  }

  /**
  * Remove the specified resource from storage.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function destroy(Request $request)
  {
    $produto = Products::where('id', $request->id)->first();
    if ($produto) {
      $old_imagem = $produto->imagem;
      Storage::delete('images/'.$old_imagem);

      Products::where('id', $request->id)->delete();

      return response()->json(array('message' => 'Produto excluído com sucesso'));
    } else {
      return response()->json(array('message' => 'Produto não encontrado'));
    }
  }

}
