<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Transformers\UserEmailObrigatorio;
use App\Transformers\UserPasswordObrigatorio;
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
    $credentials = $request->only('email', 'password');

    if ($token = $this->guard()->attempt($credentials)) {
      return $this->respondWithToken($token);
    } elseif (!$request->email or !$request->password) {
      return responder()->error('500','Ocorreu um erro de validação')->respond();
    } else {
      return responder()->error('','Usuário ou senha inválida')->respond();
    }

  }

  /**
  * Get the authenticated User
  *
  * @return \Illuminate\Http\JsonResponse
  */
  public function show()
  {
    return responder()->success(
      ['user'=>UserTransformer::transform($this->guard()->user())]
      )->respond();
  }

  /**
  * Log the user out (Invalidate the token)
  *
  * @return \Illuminate\Http\JsonResponse
  */
  public function sair()
  {
      $this->guard()->logout();
      return responder()->success(['message' => 'Logout realizado com sucesso'])->respond();
  }

  /**
  * Refresh a token.
  *
  * @return \Illuminate\Http\JsonResponse
  */
  public function refresh(Request $request)
  {
    if ($request->token) {
      return $this->respondWithToken($this->guard()->refresh());
    } else {
      return responder()->error('','Token inválido')->respond();
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
    return responder()->success(
      ['token'=>$token,
      'user'=>UserTransformer::transform($this->guard()->user())]
      )->respond();
  }

  /**
  * Get the guard to be used during authentication.
  *
  * @return \Illuminate\Contracts\Auth\Guard
  */
  public function guard()
  {
    return Auth::guard();
  }
}
