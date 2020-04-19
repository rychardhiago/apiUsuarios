<?php

class UsuarioTest extends TestCase
{

    /*
     * TESTES COM DADOS VÁLIDOS
     */
    public function testAutenticacao() {

        $data = [
            'email' => 'rychardhiago@gmail.com',
            'senha' => 'A1abcdef@222',
        ];

        $this->call('POST', 'auth/login',$data);
        $this->assertResponseOk();
    }

    public function testListarTodos() {

        $data = [
            'email' => 'rychardhiago@gmail.com',
            'senha' => 'A1abcdef@222',
        ];
        $response = $this->json('POST', 'auth/login',$data)->response->getOriginalContent();

        $data = [
            'token' => $response['token']
        ];

        $this->json('GET', 'usuarios', $data)->response->getOriginalContent();
        $this->assertResponseOk();
    }

    public function testListarUnico() {

        $data = [
            'email' => 'rychardhiago@gmail.com',
            'senha' => 'A1abcdef@222',
        ];
        $response = $this->json('POST', 'auth/login',$data)->response->getOriginalContent();

        $data = [
            'token' => $response['token']
        ];

        $this->json('GET', 'usuario/3', $data)->response->getOriginalContent();
        $this->assertResponseOk();
    }

    public function testInsert() {
        function generateRandomString($length = 10) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }

        $data = [
            'email' => 'rychardhiago@gmail.com',
            'senha' => 'A1abcdef@222',
        ];
        $response = $this->json('POST', 'auth/login',$data)->response->getOriginalContent();

        $email = generateRandomString().'@novo.com';
        $data = [
            'nome' => 'usuario novo',
            'email' => $email,
            'senha' => 'A123456a@',
            'token' => $response['token']
        ];

        $this->json('POST', 'usuario', $data)->response->getOriginalContent();
        $this->assertResponseStatus(201);
    }

    public function testUpdate() {

        $data = [
            'email' => 'rychardhiago@gmail.com',
            'senha' => 'A1abcdef@222',
        ];
        $response = $this->json('POST', 'auth/login',$data)->response->getOriginalContent();

        $data = [
            'nome' => 'nome editado',
            'token' => $response['token']
        ];

        $this->json('PUT', 'usuario/2', $data)->response->getOriginalContent();
        $this->assertResponseOk();
    }

    public function testDelete() {

        $data = [
            'email' => 'rychardhiago@gmail.com',
            'senha' => 'A1abcdef@222',
        ];
        $response = $this->json('POST', 'auth/login',$data)->response->getOriginalContent();

        $data = [
            'token' => $response['token']
        ];

        $this->json('DELETE', 'usuario/2', $data)->response->getOriginalContent();
        $this->assertResponseOk();
    }

    /*
     * TESTES COM DADOS INVÁLIDOS
     */

    //Email inválido
    public function testInsertEmailInvalido() {

        $data = [
            'email' => 'rychardhiago@gmail.com',
            'senha' => 'A1abcdef@222',
        ];
        $response = $this->json('POST', 'auth/login',$data)->response->getOriginalContent();

        $data = [
            'nome' => 'usuario novo',
            'email' => 'email@novo',
            'senha' => 'A123456a@',
            'token' => $response['token']
        ];

        $this->json('POST', 'usuario', $data)->response->getOriginalContent();
        $this->assertResponseStatus(500);
    }

    //Nome inválido com números
    public function testInsertNomeNumero() {

        $data = [
            'email' => 'rychardhiago@gmail.com',
            'senha' => 'A1abcdef@222',
        ];
        $response = $this->json('POST', 'auth/login',$data)->response->getOriginalContent();

        $data = [
            'nome' => 'usuario novo 123',
            'email' => 'email@novo.com',
            'senha' => 'A123456a@',
            'token' => $response['token']
        ];

        $this->json('POST', 'usuario', $data)->response->getOriginalContent();
        $this->assertResponseStatus(500);
    }

    //Nome sem sobrenome
    public function testInsertNomeSemSobrenome() {

        $data = [
            'email' => 'rychardhiago@gmail.com',
            'senha' => 'A1abcdef@222',
        ];
        $response = $this->json('POST', 'auth/login',$data)->response->getOriginalContent();

        $data = [
            'nome' => 'usuario',
            'email' => 'email@novo.com',
            'senha' => 'A123456a@',
            'token' => $response['token']
        ];

        $this->json('POST', 'usuario', $data)->response->getOriginalContent();
        $this->assertResponseStatus(500);
    }

    //Senha inválida
    public function testInsertSenha() {

        $data = [
            'email' => 'rychardhiago@gmail.com',
            'senha' => 'A1abcdef@222',
        ];
        $response = $this->json('POST', 'auth/login',$data)->response->getOriginalContent();

        $data = [
            'nome' => 'usuario novo',
            'email' => 'email@novo.com',
            'senha' => 'A13@',
            'token' => $response['token']
        ];

        $this->json('POST', 'usuario', $data)->response->getOriginalContent();
        $this->assertResponseStatus(500);
    }

    //Usuário não existente
    public function testUpdateUsuario() {

        $data = [
            'email' => 'rychardhiago@gmail.com',
            'senha' => 'A1abcdef@222',
        ];
        $response = $this->json('POST', 'auth/login',$data)->response->getOriginalContent();

        $data = [
            'nome' => 'nome editado',
            'token' => $response['token']
        ];

        $this->json('PUT', 'usuario/0', $data)->response->getOriginalContent();
        $this->assertResponseStatus(400);
    }

    //Usuário não existente
    public function testDeleteUsuario() {

        $data = [
            'email' => 'rychardhiago@gmail.com',
            'senha' => 'A1abcdef@222',
        ];
        $response = $this->json('POST', 'auth/login',$data)->response->getOriginalContent();

        $data = [
            'token' => $response['token']
        ];

        $this->json('DELETE', 'usuario/0', $data)->response->getOriginalContent();
        $this->assertResponseStatus(400);
    }

    //Email inexistente
    public function testAutenticacaoEmail() {

        $data = [
            'email' => 'emailErro@gmail.com',
            'senha' => 'A1abcdef@222',
        ];

        $this->call('POST', 'auth/login',$data);
        $this->assertResponseStatus(400);
    }

    //Token invalido
    public function testListarTodosToken() {

        $data = [
            'token' => 'token'
        ];

        $this->json('GET', 'usuarios', $data)->response->getOriginalContent();
        $this->assertResponseStatus(401);
    }

    //Token invalido
    public function testListarUnicoToken() {

        $data = [
            'token' => 'token'
        ];

        $this->json('GET', 'usuario/3', $data)->response->getOriginalContent();
        $this->assertResponseStatus(401);
    }
}
