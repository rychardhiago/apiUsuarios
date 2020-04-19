<?php

namespace App\Transformers;

use App\Entities\Usuario;

class UsuarioTransformer
{
    public function transform(Usuario $usuario)
    {
        return [
            'id' => $usuario->getId(),
            'email' => $usuario->getEmail(),
            'nome' => $usuario->getNome(),
            'data_cadastro' => $usuario->getDataCadastro(),
            'data_ult_alt' => $usuario->getDataUltAlt()
        ];
    }

    public function transformAll(array $posts) {
        return array_map(
            function ($post) {
                return $this->transform($post);
            }, $posts
        );
    }
}
