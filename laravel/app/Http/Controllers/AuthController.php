<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;
use Tymon\JWTAuth\Contracts\Providers\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWTAuth as JWTAuthJWTAuth;

class AuthController extends Controller
{

  public function login(Request $request)
  {
    $erro01 = ['message'=>'Ocorreu um erro de validação'];
    $erro02 = [];
    $errors = [];
    $credentials = $request->only('email', 'password');

    if (!$token = JWTAuth::attempt($credentials)) {
      $erro02 = array ("error" => array("message"=>"Usuário ou senha inválida"));
      return response()->json($erro02);

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
      JWTAuth::setToken($token);
      return $this->respondWithToken($token);
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
  public function refresh()
  {
    return $this->respondWithToken(JWTAuth::parseToken()->refresh());
  }

  /**
  * Get the token array structure.
  *
  * @param  string $token
  *
  * @return \Illuminate\Http\JsonResponse
  */
  /*   protected function respondWithToken($token)
  {
      return response()->json(["data" => array('token'=>$token,
     'user'=>UserTransformer::transform(auth('api')->guard()->user()))]);
   }
  */
  protected function respondWithToken($token)
  {
    return response()->json(["data" => array('token'=>$token,
    'user'=>UserTransformer::transform(auth()->user()))]);
  }

}
