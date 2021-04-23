<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;

class AuthController extends Controller
{
  /**
  * Create a new AuthController instance.
  *
  * @return void
  */
  public function __construct()
  {
    $this->middleware('auth:api', ['except' => ['login']]);
  }

  /**
  * Get a JWT token via given credentials.
  *
  * @param  \Illuminate\Http\Request  $request
  *
  * @return \Illuminate\Http\JsonResponse
  */
  public function login(Request $request)
  {
    $erro01 = ['message'=>'Ocorreu um erro de validação'];
    $erro02 = [];
    $errors = [];
    $credentials = $request->only('email', 'password');

    if ($token = auth()->attempt($credentials)) {
      return $this->respondWithToken($token);
    } elseif (!$request->email && $request->password) {
      $erro02 = array ("errors" => array(["field"=>"email", "message"=>"O campo Email é obrigatório."]));
      $errors = array_merge($erro01,$erro02);
      return response()->json($errors);
    } elseif ($request->email && !$request->password) {
      $erro02 = array ("errors" => array(["field"=>"password", "message"=>"O campo Senha é obrigatório."]));
      $errors = array_merge($erro01,$erro02);
      return response()->json($errors);
    } elseif (!$request->email && !$request->password) {
      $erro02 = array ("errors" => array(["field"=>"email", "message"=>"O campo Email é obrigatório."],
                                        ["field"=>"password", "message"=>"O campo Senha é obrigatório."]));
      $errors = array_merge($erro01,$erro02);
      return response()->json($errors);
    } else {
      $erro02 = array ("error" => array("message"=>"Usuário ou senha inválida"));
      return response()->json($erro02);
    }

  }

  /**
  * Get the authenticated User
  *
  * @return \Illuminate\Http\JsonResponse
  */
  public function me()
  {
    return response()->json(["data" => array('user'=>UserTransformer::transform(auth()->user()))]);
  }

  /**
  * Log the user out (Invalidate the token)
  *
  * @return \Illuminate\Http\JsonResponse
  */
  public function logout()
  {
      auth()->logout();
      return response()->json(array('message' => 'Logout realizado com sucesso'));
  }

  /**
  * Refresh a token.
  *
  * @return \Illuminate\Http\JsonResponse
  */
  public function refresh(Request $request)
  {
    if ($request->token) {
      return $this->respondWithToken(auth()->refresh());
      //return response()->json(["data" => array('token'=>$token)]);
    } else {
      $erro02 = array ("error" => array("message"=>"Token inválido"));
      return response()->json($erro02);
    }
  }

  /**
  * Get the token array structure.
  *
  * @param  string $token
  *
  * @return \Illuminate\Http\JsonResponse
  */
  protected function respondWithToken($token)
  {
      return response()->json(["data" => array('token'=>$token,
      'user'=>UserTransformer::transform(auth()->user()))]);
  }

  /**
  * Get the guard to be used during authentication.
  *
  * @return \Illuminate\Contracts\Auth\Guard
  */
  // public function guard()
  // {
  //   return Auth::guard();
  // }
}
