<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ProductsController extends Controller
{
  // protected $user;
  //
  // public function __construct()
  // {
  //     $this->user = JWTAuth::parseToken()->authenticate();
  // }
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index()
  {
    return responder()->success(Products::paginate())->respond();
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
      return responder()->error('','Um dos campos não foi informado ou é inválido')->respond();
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
        $nfile = '/images/'.$nameFile;

        // Faz o upload:
        $upload = $request->image->storeAs('images', $nameFile);
        // Se tiver funcionado o arquivo foi armazenado em storage/app/public/images/nomedinamicoarquivo.extensao
        if ( !$upload ) {
          return responder()->error('','Arquivo de imagem não enviado')->respond();
        } else {
          $product = Products::create([
            'nome' => $request->nome,
            'peso' => $request->peso,
            'valor' => $request->valor,
            'imagem' => $nfile,
          ]);
          $product->save();

          return responder()->success([Products::paginate()])->respond();
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
      return responder()->error('','Um dos campos não foi informado ou é inválido')->respond();
    } else {
      $produto = Products::where('id', $request->id)->first();
      if ($produto) {

        $nameFile = null;

        // Verifica se informou o arquivo e se é válido
        if ($request->hasFile('image') && $request->file('image')->isValid()) {

          // Define um aleatório para o arquivo baseado no timestamps atual
          $name = uniqid(date('HisYmd'));

          // Recupera a extensão do arquivo
          $extension = $request->image->extension();

          // Define finalmente o nome
          $nameFile = "{$name}.{$extension}";
          $nfile = '/images/'.$nameFile;

          // Faz o upload:
          $upload = $request->image->storeAs('images', $nameFile);
          // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao
          if ( !$upload ) {
            return responder()->error('','Arquivo de imagem não enviado')->respond();
          } else {

            $old_imagem = $produto->imagem;

            Products::where('id', $request->id)->update(array(
              'nome' => $request->nome,
              'peso' => $request->peso,
              'valor' => $request->valor,
              'imagem' => $nfile,
            ));
            File::delete(public_path('storage'.$old_imagem));
            return responder()->success([Products::paginate()])->respond();
            }
          }
        } else {
          return responder()->error('','Produto não encontrado')->respond();
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
      File::delete(public_path('storage'.$old_imagem));

      Products::where('id', $request->id)->delete();

      return responder()->success(Products::paginate())->respond();
    } else {
      return responder()->error('','Produto não encontrado')->respond();
    }
  }
}
