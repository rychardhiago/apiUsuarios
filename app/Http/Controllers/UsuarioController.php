<?php

namespace App\Http\Controllers;

use App\Entities\Usuario;
use App\Transformers\UsuarioTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use DateTime;


/**
 * Class UsuarioController
 * @package App\Http\Controllers
 */
class UsuarioController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @SWG\Get(
     *   path="/usuarios",
     *   summary="usuarios",
     *     @SWG\Parameter(
     *         name="token",
     *         in="query",
     *         description="token obtido pela autenticação",
     *         required=true,
     *         type="string",
     *         format="json",
     *         @SWG\Property(property="result", type="json", example={"token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsdW1lbi1qd3QiLCJzdWIiOjIsImlhdCI6MTU4NzI0MTQ3NSwiZXhwIjoxNTg3MjQ1MDc1fQ.KU79YEEMGY9TnutnnvbTxHTT3RdJKZCmoXsG8RzMHPM"})
     *     ),
     *   @SWG\Response(
     *     response=200,
     *     description="Retorna um array com os dados",
     *     @SWG\Property(property="result", type="json", example={"id": 2,"email": "teste@gmail.com","nome": "Teste usuario","data_cadastro": {"date": "2020-04-18 15:38:45.000000","timezone_type": 3,"timezone": "America/Sao_Paulo"},"data_ult_alt": null})
     *   ),
     *   @SWG\Response(
     *     response="401",
     *     description="Retorna uma mensagem de validação",
     *     @SWG\Property(property="result", type="json", example={"retorno": false, "mensagem": "Ocorreu um erro na sua requisição. Erro original: Token inválido"})
     *   )
     * )
     */
    public function index(EntityManagerInterface $entityManager) {
        $usuario = $entityManager
            ->getRepository(Usuario::class)
            ->findBy(['excluido' => 'N']);

        $transformer = new UsuarioTransformer();
        return $transformer->transformAll($usuario);
    }

    /**
     * @SWG\Get(
     *   path="/usuario/{id}",
     *   summary="usuario/{id}",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="id do usuário procurado",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="token",
     *         in="query",
     *         description="token obtido pela autenticação",
     *         required=true,
     *         type="string",
     *         format="json",
     *         @SWG\Property(property="result", type="json", example={"token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsdW1lbi1qd3QiLCJzdWIiOjIsImlhdCI6MTU4NzI0MTQ3NSwiZXhwIjoxNTg3MjQ1MDc1fQ.KU79YEEMGY9TnutnnvbTxHTT3RdJKZCmoXsG8RzMHPM"})
     *     ),
     *   @SWG\Response(
     *     response=200,
     *     description="Retorna um array com os dados",
     *     @SWG\Property(property="result", type="json", example={"id": 2,"email": "teste@gmail.com","nome": "Teste usuario","data_cadastro": {"date": "2020-04-18 15:38:45.000000","timezone_type": 3,"timezone": "America/Sao_Paulo"},"data_ult_alt": null})
     *   ),
     *   @SWG\Response(
     *     response="401",
     *     description="Retorna uma mensagem de validação",
     *     @SWG\Property(property="result", type="json", example={"retorno": false, "mensagem": "Ocorreu um erro na sua requisição. Erro original: Token inválido"})
     *   )
     * )
     */
    public function show($id, EntityManagerInterface $entityManager) {
        $usuario = $entityManager
            ->getRepository(Usuario::class)
            ->findOneBy([
                'id' => $id,
                'excluido' => 'N'
            ]);
        if($usuario) {
            $transformer = new UsuarioTransformer();
            return $transformer->transform($usuario);
        } else{
            return 'Usuário não encontrado.';
        }
    }

    /**
     * @SWG\Post(
     *   path="/usuario",
     *   summary="usuario",
     *     @SWG\Parameter(
     *         name="dados",
     *         in="query",
     *         description="json com os dados para serem inseridos e com o token. Este método possui validações a cerca dos valores inseridos.",
     *         required=true,
     *         type="string",
     *         format="json",
     *         @SWG\Property(property="result", type="json", example={"nome": "Rychard Hiago","email": "rychardhiago@gmail.com","senha": "A1abcdef@222","token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsdW1lbi1qd3QiLCJzdWIiOjIsImlhdCI6MTU4NzI0MTQ3NSwiZXhwIjoxNTg3MjQ1MDc1fQ.KU79YEEMGY9TnutnnvbTxHTT3RdJKZCmoXsG8RzMHPM"})
     *     ),
     *   @SWG\Response(
     *     response="200",
     *     description="Sucesso na execução",
     *     @SWG\Property(property="result", type="json", example={"retorno": true,"mensagem":"Comando executado com sucesso."})
     *   ),
     *   @SWG\Response(
     *     response="401",
     *     description="Retorna uma mensagem de validação",
     *     @SWG\Property(property="result", type="json", example={"retorno": false, "mensagem": "Ocorreu um erro na sua requisição. Erro original: Token inválido"})
     *   ),
     *   @SWG\Response(
     *     response="400",
     *     description="Retorna uma mensagem de validação",
     *     @SWG\Property(property="result", type="json", example={"retorno": false, "mensagem": "Ocorreu um erro na sua requisição. Erro original: Token expirado"})
     *   )
     * )
     */
    public function store(Request $request, EntityManagerInterface $entityManager) {
        try {
            $usuario = $entityManager
                ->getRepository(Usuario::class)
                ->findOneBy([
                    'email' => $request->get('email'),
                    'excluido' => 'N'
                ]);

            if (!$usuario) {
                $usuario = new Usuario(
                    $request->get('email'),
                    $request->get('senha'),
                    $request->get('nome')
                );


                $entityManager->persist($usuario);
                $entityManager->flush();
                return response()->json(
                    [
                        'retorno' => true,
                        'mensagem' => 'Comando executado com sucesso.'
                    ],
                    201
                );
            } else {
                throw new \Exception('ERRO-004: Email já cadastrado.');
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'retorno' => false,
                    'mensagem' => 'Ocorreu um erro na sua requisição. Erro original: '.$e->getMessage()
                ],
                500,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }
    }

    /**
     * @SWG\Put(
     *   path="/usuario/{id}",
     *   summary="usuario/{id}",
     *     @SWG\Parameter(
     *         name="dados",
     *         in="query",
     *         description="json com os dados para serem alterados e com o token. Este método possui validações a cerca dos valores inseridos.",
     *         required=true,
     *         type="string",
     *         format="json",
     *         @SWG\Property(property="result", type="json", example={"nome": "Rychard Hiago","email": "rychardhiago@gmail.com","senha": "A1abcdef@222","token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsdW1lbi1qd3QiLCJzdWIiOjIsImlhdCI6MTU4NzI0MTQ3NSwiZXhwIjoxNTg3MjQ1MDc1fQ.KU79YEEMGY9TnutnnvbTxHTT3RdJKZCmoXsG8RzMHPM"})
     *     ),
     *   @SWG\Response(
     *     response="200",
     *     description="Sucesso na execução",
     *     @SWG\Property(property="result", type="json", example={"retorno": true,"mensagem":"Comando executado com sucesso."})
     *   ),
     *   @SWG\Response(
     *     response="401",
     *     description="Retorna uma mensagem de validação",
     *     @SWG\Property(property="result", type="json", example={"retorno": false, "mensagem": "Ocorreu um erro na sua requisição. Erro original: Token inválido"})
     *   ),
     *   @SWG\Response(
     *     response="400",
     *     description="Retorna uma mensagem de validação",
     *     @SWG\Property(property="result", type="json", example={"retorno": false, "mensagem": "Ocorreu um erro na sua requisição. Erro original: Token expirado"})
     *   )
     * )
     */
    public function update($id, Request $request, EntityManagerInterface $entityManager)
    {
        try {
            $usuario = $entityManager
                ->getRepository(Usuario::class)
                ->findOneBy([
                    'id' => $id,
                    'excluido' => 'N'
                ]);
            if($usuario) {
                $usuario->setEmail($request->get('email'));
                $usuario->setSenha($request->get('senha'));
                $usuario->setNome($request->get('nome'));
                $dataAlt = new DateTime();
                $usuario->setDataUltAlt($dataAlt);

                $entityManager->flush();
                $transformer = new UsuarioTransformer();
                return response()->json(
                    [
                        'retorno' => true,
                        'mensagem' => 'Comando executado com sucesso.',
                        'data' => $transformer->transform($usuario)
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'retorno' => false,
                        'mensagem' => 'Ocorreu um erro na sua requisição. Erro original: Usuário não encontrado'
                    ],
                    400,
                    [],
                    JSON_UNESCAPED_UNICODE
                );
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'retorno' => false,
                    'mensagem' => 'Ocorreu um erro na sua requisição. Erro original: '.$e->getMessage()
                ],
                500,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }
    }

    /**
     * @SWG\Delete(
     *   path="/usuario/{id}",
     *   summary="usuario/{id}",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="id do usuário procurado",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="token",
     *         in="query",
     *         description="token obtido pela autenticação",
     *         required=true,
     *         type="string",
     *         format="json",
     *         @SWG\Property(property="result", type="json", example={"token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsdW1lbi1qd3QiLCJzdWIiOjIsImlhdCI6MTU4NzI0MTQ3NSwiZXhwIjoxNTg3MjQ1MDc1fQ.KU79YEEMGY9TnutnnvbTxHTT3RdJKZCmoXsG8RzMHPM"})
     *     ),
     *   @SWG\Response(
     *     response="200",
     *     description="Sucesso na execução",
     *     @SWG\Property(property="result", type="json", example={"retorno": true,"mensagem":"Comando executado com sucesso."})
     *   ),
     *   @SWG\Response(
     *     response="401",
     *     description="Retorna uma mensagem de validação",
     *     @SWG\Property(property="result", type="json", example={"retorno": false, "mensagem": "Ocorreu um erro na sua requisição. Erro original: Token inválido"})
     *   ),
     *   @SWG\Response(
     *     response="400",
     *     description="Retorna uma mensagem de validação",
     *     @SWG\Property(property="result", type="json", example={"retorno": false, "mensagem": "Ocorreu um erro na sua requisição. Erro original: Token expirado"})
     *   )
     * )
     */
    public function destroy($id, Request $request, EntityManagerInterface $entityManager) {
        try {
            $usuario = $entityManager
                ->getRepository(Usuario::class)
                ->findOneBy([
                    'id' => $id
                ]);

            if($usuario) {

                $temPermissao = $request->get('permissaoExclusao');
                if ($temPermissao == 'S') {
                    $entityManager->remove($usuario);
                    $entityManager->flush();
                } else {
                    /*
                     * Caso não tenha permissão faz uma alteração no banco
                     */
                    $usuario->setExcluido('S');
                    $dataExc = new DateTime();
                    $usuario->setDataExclusao($dataExc);
                    $usuario->setIpExclusao($request->ip());

                    $entityManager->flush();
                }

                return response()->json(
                    [
                        'retorno' => true,
                        'mensagem' => 'Comando executado com sucesso.'
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'retorno' => false,
                        'mensagem' => 'Ocorreu um erro na sua requisição. Erro original: Usuário não encontrado'
                    ],
                    400,
                    [],
                    JSON_UNESCAPED_UNICODE
                );
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'retorno' => false,
                    'mensagem' => 'Ocorreu um erro na sua requisição. Erro original: '.$e->getMessage()
                ],
                500,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }
    }

    protected function jwt(Usuario $usuario) {
        $payload = [
            'iss' => "lumen-jwt",
            'sub' => $usuario->getId(),
            'iat' => time(),
            'exp' => time() + 60*60
        ];

        return JWT::encode($payload, env('JWT_SECRET'));
    }

    /**
     * @SWG\Post(
     *   path="/auth/login",
     *   summary="auth/login",
     *     @SWG\Parameter(
     *         name="dados",
     *         in="query",
     *         description="json com os dados para login.",
     *         required=true,
     *         type="string",
     *         format="json",
     *         @SWG\Property(property="result", type="json", example={"email": "rychardhiago@gmail.com","senha": "A1abcdef@222"})
     *     ),
     *   @SWG\Response(
     *     response="200",
     *     description="Sucesso na execução",
     *     @SWG\Property(property="result", type="json", example={"retorno": true,"mensagem":"Comando executado com sucesso.", "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsdW1lbi1qd3QiLCJzdWIiOjIsImlhdCI6MTU4NzI0MTQ3NSwiZXhwIjoxNTg3MjQ1MDc1fQ.KU79YEEMGY9TnutnnvbTxHTT3RdJKZCmoXsG8RzMHPM"})
     *   ),
     *   @SWG\Response(
     *     response="400",
     *     description="Retorna uma mensagem de validação",
     *     @SWG\Property(property="result", type="json", example={"retorno": false, "mensagem": "Ocorreu um erro na sua requisição. Erro original: Email não encontrado"}),
     *     @SWG\Property(property="result", type="json", example={"retorno": false, "mensagem": "Ocorreu um erro na sua requisição. Erro original: Senha está inválida"})
     *   )
     * )
     */
    public function authenticate(Request $request, EntityManagerInterface $entityManager) {


        $usuario = $entityManager
            ->getRepository(Usuario::class)
            ->findOneBy([
                'email' => $request->get('email'),
                'excluido' => 'N'
            ]);

        if (!$usuario) {
            return response()->json(
                [
                    'retorno' => false,
                    'mensagem' => 'Ocorreu um erro na sua requisição. Erro original: Email não encontrado'
                ],
                400,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }

        if(!password_verify($request->get('senha'), $usuario->getSenha())){
            return response()->json(
                [
                    'retorno' => false,
                    'mensagem' => 'Ocorreu um erro na sua requisição. Erro original: Senha está incorreta.'
                ],
                400,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }

        return response()->json(
            [
                'retorno' => true,
                'mensagem' => 'Login executado com sucesso.',
                'token' => $this->jwt($usuario)
            ],
            200
        );
    }


}
