<?php
require_once "api/src/http/Response.php";

class AutorMiddleware
{
    public function stringJsonToStdClass($requestBody): stdClass
    {
        $stdAutor = json_decode(json: $requestBody);

        if (json_last_error() !== JSON_ERROR_NONE) {
            (new Response(
                success: false,
                message: 'Autor inválido',
                error: [
                    'code' => 'validation_error',
                    'messagem' => 'Json inválido',
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!isset($stdAutor->Autor)) {
            (new Response(
                success: false,
                message: 'Autor inválido',
                error: [
                    'code' => 'validation_error',
                    'messagem' => 'Não foi enviado o objeto Autor',
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!isset($stdAutor->Autor->nomeAutor)) {
            (new Response(
                success: false,
                message: 'Autor inválido',
                error: [
                    'code' => 'validation_error',
                    'messagem' => 'Não foi eniado o atributo nomeAutor do Autor',
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        $Autor = new Autor();
        $Autor->setNomeAutor(nomeAutor: $stdAutor->Autor->nomeAutor);

        return $stdAutor;
    }

    public function isValidNomeAutor($nomeAutor): self
    {
        if (!isset($nomeAutor)) {
            (new Response(
                success: false,
                message: 'Autor inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O Autor não foi enviado'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (strlen(string: $nomeAutor) < 3) {
            (new Response(
                success: false,
                message: 'Autor inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O Autor precisa de pelo menos 3 caracteres'
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        if (!strlen(string: $nomeAutor) > 3) {
            (new Response(
                success: false,
                message: 'Nome do Autor inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O nome do Autor não pode estar vazio ou ter menos que 3 letras',
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        return $this;
    }

    public function hasAutorByName($nomeAutor): self
    {
        $AutorDAO = new AutorDAO();
        $Autor = $AutorDAO->readByName(nomeAutor: $nomeAutor);

        if (isset($Autor)) {
            return $this;
        }

        (new Response(
            success: false,
            message: "já existe um Autor cadastrado com o nome ($nomeAutor)",
            error: [
                'code' => 'validation_error',
                'message' => 'Autor não cadastrado anteriormente',
            ],
            httpCode: 400
        ))->send();
        exit();
    }

    public function hasNotAutorByName($nomeAutor): self
    {
        $AutorDAO = new AutorDAO();
        $Autor = $AutorDAO->readByName(nomeAutor: $nomeAutor);

        if (!isset($Autor)) {
            return $this;
        }

        (new Response(
            success: false,
            message: "já existe um Autor cadastrado com o nome ($nomeAutor)",
            error: [
                'code' => 'validation_error',
                'message' => 'Autor cadastrado anteriormente',
            ],
            httpCode: 400
        ))->send();
        exit();
    }

    public function hasAutorById($idAutor): self
    {
        $AutorDao = new AutorDAO();
        $Autor = $AutorDao->readById(idAutor: $idAutor);

        if (!isset($Autor)) {
            (new Response(
                success: false,
                message: "Não existe um Autor com o id Fornecido",
                error: [
                    'code' => 'validation_error',
                    'message' => 'Autor informado não existente',
                ],
                httpCode: 400
            ))->send();
        }

        return $this;
    }

    public function hasNotAutorById($idAutor): self
    {
        $AutorDao = new AutorDAO();
        $Autor = $AutorDao->readById(idAutor: $idAutor);

        if ($Autor !== null) {
            (new Response(
                success: false,
                message: "Já existe um Autor com o ID fornecido ($idAutor)",
                error: [
                    'code' => 'validation_error',
                    'message' => 'ID de Autor já está em uso',
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        return $this;
    }

    public function isValidId($idAutor): self
    {
        if (!isset($idAutor)) {
            (new Response(
                success: true,
                message: 'Não Foi possível buscar o Autor',
                error: [
                    'code' => 'Autor_validation_error',
                    'message' => 'O id Fornecido não é válido'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!is_numeric(value: $idAutor)) {
            (new Response(
                success: true,
                message: 'Não Foi possível buscar o Autor',
                error: [
                    'code' => 'Autor_validation_error',
                    'message' => 'O id Fornecedio não é um número'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if ($idAutor <= 0) {
            (new Response(
                success: true,
                message: 'Não Foi possível buscar o Autor',
                error: [
                    'code' => 'Autor_validation_error',
                    'message' => 'O id Fornecedio não é positivo'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else {
            return $this;
        }
    }
}







