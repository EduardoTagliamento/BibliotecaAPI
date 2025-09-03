<?php
require_once "api/src/http/Response.php";

class CategoriaMiddleware
{
    public function stringJsonToStdClass($requestBody): stdClass
    {
        $stdCategoria = json_decode(json: $requestBody);

        if (json_last_error() !== JSON_ERROR_NONE) {
            (new Response(
                success: false,
                message: 'Categoria inválido',
                error: [
                    'code' => 'validation_error',
                    'messagem' => 'Json inválido',
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!isset($stdCategoria->categoria)) {
            (new Response(
                success: false,
                message: 'Categoria inválido',
                error: [
                    'code' => 'validation_error',
                    'messagem' => 'Não foi enviado o objeto Categoria',
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!isset($stdCategoria->categoria->nomeCategoria)) {
            (new Response(
                success: false,
                message: 'Categoria inválido',
                error: [
                    'code' => 'validation_error',
                    'messagem' => 'Não foi enviado o atributo nomeCategoria do Categoria',
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        $categoria = new Categoria();
        $categoria->setNomeCategoria(nomeCategoria: $stdCategoria->categoria->nomeCategoria);

        return $stdCategoria;
    }

    public function isValidNomeCategoria($nomeCategoria): self
    {
        if (!isset($nomeCategoria)) {
            (new Response(
                success: false,
                message: 'Categoria inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'A categoria não foi enviado'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (strlen(string: $nomeCategoria) < 3) {
            (new Response(
                success: false,
                message: 'Categoria inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'A categoria precisa de pelo menos 3 caracteres'
                ],
                httpCode: 400
            ))->send();

            exit();
        }

        if (!strlen(string: $nomeCategoria) > 3) {
            (new Response(
                success: false,
                message: 'Nome do categoria inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O nome da categoria não pode estar vazio ou ter menos que 3 letras',
                ],
                httpCode: 400
            ))->send();

            exit();
        }

        return $this;
    }

    public function hasCategoriaByName($nomeCategoria): self
    {
        $categoriaDAO = new CategoriaDAO();
        $categoria = $categoriaDAO->readByName(nomeCategoria: $nomeCategoria);

        if (isset($categoria)) {
            return $this;
        }

        (new Response(
            success: false,
            message: "já existe um Categoria cadastrado com o nome ($nomeCategoria)",
            error: [
                'code' => 'validation_error',
                'message' => 'Categoria não cadastrado anteriormente',
            ],
            httpCode: 400
        ))->send();

        exit();
    }

    public function hasNotCategoriaByName($nomeCategoria): self
    {
        $categoriaDAO = new CategoriaDAO();
        $categoria = $categoriaDAO->readByName(nomeCategoria: $nomeCategoria);

        if (!isset($categoria)) {
            return $this;
        }

        (new Response(
            success: false,
            message: "já existe um Categoria cadastrado com o nome ($nomeCategoria)",
            error: [
                'code' => 'validation_error',
                'message' => 'Categoria cadastrado anteriormente',
            ],
            httpCode: 400
        ))->send();

        exit();
    }

    public function hasCategoriaById($idCategoria): self
    {
        $categoriaDao = new CategoriaDAO();
        $categoria = $categoriaDao->readById(idCategoria: $idCategoria);
        if (!isset($categoria)) {
            (new Response(
                success: false,
                message: "Não existe um categoria com o id Fornecido",
                error: [
                    'code' => 'validation_error',
                    'message' => 'Categoria informado não existente',
                ],
                httpCode: 400
            ))->send();
        }
        return $this;
    }

    public function hasNotCategoriaById($idCategoria): self
    {
        $categoriaDao = new CategoriaDAO();
        $categoria = $categoriaDao->readById(idCategoria: $idCategoria);

        if ($categoria !== null) {
            (new Response(
                success: false,
                message: "Já existe um categoria com o ID fornecido ($idCategoria)",
                error: [
                    'code' => 'validation_error',
                    'message' => 'ID de categoria já está em uso',
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        return $this;
    }

    public function isValidId($idCategoria): self
    {
        if (!isset($idCategoria)) {
            (new Response(
                success: true,
                message: 'Não Foi possível buscar o categoria',
                error: [
                    'code' => 'categoria_validation_error',
                    'message' => 'O id Fornecido não é válido'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!is_numeric(value: $idCategoria)) {
            (new Response(
                success: true,
                message: 'Não Foi possível buscar o categoria',
                error: [
                    'code' => 'categoria_validation_error',
                    'message' => 'O id Fornecedio não é um número'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if ($idCategoria <= 0) {
            (new Response(
                success: true,
                message: 'Não Foi possível buscar o categoria',
                error: [
                    'code' => 'categoria_validation_error',
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