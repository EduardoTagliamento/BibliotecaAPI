<?php
require_once "api/src/http/Response.php";

class LivroMiddleware
{
    public function stringJsonToStdClass($requestBody): stdClass
    {
        $stdLivro = json_decode($requestBody);

        if (json_last_error() !== JSON_ERROR_NONE) {
            (new Response(
                success: false,
                message: 'Livro inválido',
                error: [
                    'codigoError' => 'validation_error',
                    'messagem' => 'Json inválido',
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!isset($stdLivro->Livro)) {
            (new Response(
                success: false,
                message: 'Livro inválido',
                error: [
                    'codigoError' => 'validation_error',
                    'messagem' => 'Não foi enviado o objeto Livro',
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!isset($stdLivro->Livro->nomeLivro)) {
            (new Response(
                success: false,
                message: 'Nome do Livro inválido',
                error: [
                    'codigoError' => 'validation_error',
                    'messagem' => 'Não foi enviado o nome do Livro',
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!isset($stdLivro->Livro->dataLancamento)) {
            (new Response(
                success: false,
                message: 'data lançamento do Livro inválida',
                error: [
                    'codigoError' => 'validation_error',
                    'messagem' => 'Não foi enviado se o Livro recebe vale transporte',
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!isset($stdLivro->Livro->Categoria->idCategoria)) {
            (new Response(
                success: false,
                message: 'idCategoria do Livro inválida',
                error: [
                    'codigoError' => 'validation_error',
                    'messagem' => 'Não foi enviado o idCategoria do Livro',
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!isset($stdLivro->Livro->Autor->idAutor)) {
            (new Response(
                success: false,
                message: 'idAutor do Livro inválida',
                error: [
                    'codigoError' => 'validation_error',
                    'messagem' => 'Não foi enviado o idAutor do Livro',
                ],
                httpCode: 400
            ))->send();
            exit();
        }
        return $stdLivro;
    }

    public function isValidNomeLivro(string $nomeLivro = null): self
    {
        if (!isset($nomeLivro)) {
            (new Response(
                success: false,
                message: 'Nome do Livro inválido',
                error: [
                    'codigoError' => 'validation_error',
                    'message' => 'Nome não fornecido',
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        $nomeLivro = trim(string: $nomeLivro);

        if (strlen(string: $nomeLivro) < 3) {
            (new Response(
                success: false,
                message: 'Nome do Livro inválido',
                error: [
                    'codigoError' => 'validation_error',
                    'message' => 'O nome do Livro não pode estar vazio ou ter menos que 4 letras',
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        return $this;
    }

    public function isValidId($idLivro): self
    {
        if (!isset($idLivro)) {
            (new Response(
                success: false,
                message: 'Não Foi possível buscar o Livro',
                error: [
                    'code' => 'Livro_validation_error',
                    'message' => 'O id do funcionário não é válido'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!is_numeric(value: $idLivro)) {
            (new Response(
                success: false,
                message: 'Não Foi possível buscar o Livro',
                error: [
                    'code' => 'Livro_validation_error',
                    'message' => 'O id Fornecido não é um número'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if ($idLivro <= 0) {
            (new Response(
                success: false,
                message: 'Não Foi possível buscar o Livro',
                error: [
                    'code' => 'Livro_validation_error',
                    'message' => 'O id Fornecido não é positivo'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else {
            return $this;
        }
    }
}

