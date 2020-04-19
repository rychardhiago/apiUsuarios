<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class JwtMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        $token = $request->get('token');

        if(!$token) {
            return response()->json(
                [
                    'retorno' => false,
                    'mensagem' => 'Ocorreu um erro na sua requisição. Erro original: Token inválido'
                ],
                401,
                [],
                JSON_UNESCAPED_UNICODE);
        }

        try {
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        } catch(ExpiredException $e) {
            return response()->json(
                [
                    'retorno' => false,
                    'mensagem' => 'Ocorreu um erro na sua requisição. Erro original: Token expirado'
                ],
                400,
                [],
                JSON_UNESCAPED_UNICODE);
        } catch(Exception $e) {
            return response()->json(
                [
                    'retorno' => false,
                    'mensagem' => 'Ocorreu um erro na sua requisição. Erro original: Não foi possível identificar o Token'
                ],
                401,
                [],
                JSON_UNESCAPED_UNICODE);
        }

        $request->auth = $credentials->sub;

        return $next($request);
    }
}
