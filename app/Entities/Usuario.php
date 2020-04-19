<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Doctrine\ORM\Auth\Authenticatable;

/**
 * @ORM\Entity
 * @ORM\Table(name="usuario")
 */
class Usuario
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string",nullable=false,unique=true)
     */
    private $email;
    /**
     * @ORM\Column(type="string",nullable=false)
     */
    private $senha;
    /**
     * @ORM\Column(type="string",nullable=false)
     */
    private $nome;
    /**
     * @ORM\Column(name="data_cadastro",type="datetime",nullable=false)
     */
    private $dataCadastro;
    /**
     * @ORM\Column(name="data_ult_alt",type="datetime",nullable=true)
     */
    private $dataUltAlt;
    /**
     * @ORM\Column(type="string",nullable=false,options={"default" : "N"})
     */
    private $excluido;
    /**
     * @ORM\Column(name="data_exclusao",type="datetime",nullable=true)
     */
    private $dataExclusao;
    /**
     * @ORM\Column(name="ip_exclusao",type="string",nullable=true)
     */
    private $ipExclusao;

    /**
     * Usuario constructor.
     * @param $email
     * @param $senha
     * @param $nome
     */
    public function __construct($email, $senha, $nome)
    {
        $this->setEmail($email);
        $this->setSenha($senha);
        $this->setNome($nome);
        if(empty($this->getDataCadastro())) {
            $this->dataCadastro = new DateTime();
        }
        if(empty($this->getExcluido())) {
            $this->setExcluido('N');
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email = null): void
    {
        if (!empty($email)) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->email = $email;
            } else {
                throw new \Exception('ERRO-001: Email inválido.');
            }
        }
    }

    /**
     * @return mixed
     */
    public function getSenha()
    {
        return $this->senha;
    }

    /**
     * @param mixed $senha
     */
    public function setSenha($senha): void
    {
        if (!empty($senha)) {
            // Validate password strength
            $uppercase = preg_match('@[A-Z]@', $senha);
            $lowercase = preg_match('@[a-z]@', $senha);
            $number    = preg_match('@[0-9]@', $senha);
            $specialChars = preg_match('@[^\w]@', $senha);

            if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($senha) < 8) {
                throw new \Exception('ERRO-002: Senha deve conter no mínimo 8 caracteres, pelo menos 1 letra maiúscula, pelo menos 1 letra minúscula, pelo menos 1 número, and pelo menos 1 caracter especial(!@#$%¨&*).');
            }

            $new_password = password_hash($senha, PASSWORD_DEFAULT);
            $this->senha = $new_password;
        }
    }

    /**
     * @return mixed
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param mixed $nome
     */
    public function setNome($nome): void
    {
        if (!empty($nome)) {
            $nome = preg_replace('/[\s\W]+/', ' ', $nome);
            $number    = preg_match('@[0-9]@', $nome);

            if(!strpos($nome,' ') || $number) {
                throw new \Exception('ERRO-003: Nome deve ser completo, com nome e sobrenome. E não pode conter números.');
            }
            $this->nome = $nome;
        }
    }

    /**
     * @return mixed
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * @return mixed
     */
    public function getDataUltAlt()
    {
        return $this->dataUltAlt;
    }

    /**
     * @param mixed $dataUltAlt
     */
    public function setDataUltAlt($dataUltAlt): void
    {
        if (!empty($dataUltAlt)) {
            $this->dataUltAlt = $dataUltAlt;
        }
    }

    /**
     * @return mixed
     */
    public function getExcluido()
    {
        return $this->excluido;
    }

    /**
     * @param mixed $excluido
     */
    public function setExcluido($excluido): void
    {
        $this->excluido = $excluido;
    }

    /**
     * @return mixed
     */
    public function getDataExclusao()
    {
        return $this->dataExclusao;
    }

    /**
     * @param mixed $dataExclusao
     */
    public function setDataExclusao($dataExclusao): void
    {
        $this->dataExclusao = $dataExclusao;
    }

    /**
     * @return mixed
     */
    public function getIpExclusao()
    {
        return $this->ipExclusao;
    }

    /**
     * @param mixed $ipExclusao
     */
    public function setIpExclusao($ipExclusao): void
    {
        $this->ipExclusao = $ipExclusao;
    }

}
