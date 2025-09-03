<?php
require_once "api/src/models/Categoria.php";
require_once "api/src/DAO/CategoriaDAO.php";
require_once "api/src/models/Livro.php";
require_once "api/src/DAO/LivroDAO.php";
require_once "api/src/http/Response.php";
require_once "api/src/utils/Logger.php";

class LivroControl
{
    public function index(): never
    {
        $LivroDAO = new LivroDAO();
        $Livros = $LivroDAO->readAll();

        (new Response(
            success: true,
            message: 'Dados selecionados com sucesso',
            data: ['Livros' => $Livros],
            httpCode: 200
        ))->send();
        exit();
    }

    public function controleLivroReadById(int $idLivro): never
    {
        $LivroDAO = new LivroDAO();
        $Livro = $LivroDAO->readById(idLivro: $idLivro);

        (new Response(
            success: true,
            message: 'Dados selecionados com sucesso',
            data: ['Livros' => $Livro],
            httpCode: 200
        ))->send();
    }

    public function store(stdClass $stdLivro): never
    {
        $Livro = new Livro();
        $Livro->setNomeLivro(nomeLivro: $stdLivro->Livro->nomeLivro);
        $Livro->setDataLancamento(dataLancamento: $stdLivro->Livro->dataLancamento);
        $Livro->getAutor()->setIdAutor(idAutor: $stdLivro->Livro->Autor->idAutor);
        $Livro->getCategoria()->setIdCategoria(idCategoria: $stdLivro->Livro->Categoria->idCategoria);

        $LivroDAO = new LivroDAO();
        $novoLivro = $LivroDAO->create(Livro: $Livro);

        (new Response(
            success: true,
            message: 'Livro Cadastrado com sucesso',
            data: ['Livros' => $novoLivro],
            httpCode: 200
        ))->send();

        exit();
    }

    public function destroy($idLivro): never
    {
        $LivroDAO = new LivroDAO();

        if ($LivroDAO->delete(idLivro: $idLivro)) {
            (new Response(
                success: true,
                message: 'Livro excluído com sucesso',
                httpCode: 204
            ))->send();
        } else {
            (new Response(
                success: false,
                message: 'Não foi possível excluir o Livro',
                error: [
                    'cod' => 'delete_error',
                    'message' => 'O Livro não pode ser excluído'
                ],
                httpCode: 400
            ))->send();
        }

        exit();
    }

    public function edit(stdClass $stdLivro): never
    {
        $Livro = new Livro();
        $Livro->setIdLivro(idLivro: $stdLivro->Livro->idLivro);
        $Livro->setNomeLivro(nomeLivro: $stdLivro->Livro->nomeLivro);
        $Livro->setDataLancamento(dataLancamento: $stdLivro->Livro->dataLancamento);
        $Livro->getAutor()->setIdAutor(idAutor: $stdLivro->Livro->Autor->idAutor);
        $Livro->getCategoria()->setIdCategoria(idCategoria: $stdLivro->Livro->Categoria->idCategoria);

        $LivroDAO = new LivroDAO();

        if ($LivroDAO->update(Livro: $Livro)) {
            (new Response(
                success: true,
                message: 'Livro Atualizado com sucesso',
                data: ['Livros' => $Livro],
                httpCode: 200
            ))->send();
        } else {
            (new Response(
                success: true,
                message: 'Livro não Atualizado',
                error: [
                    'code' => "Livro_update",
                    'message' => "Não foi possível atualizar o funcionário"
                ],
                httpCode: 400
            ))->send();
        }

        exit();
    }

    public function controleLivroCreateFromCSV(array $file): never
    {
        $nomeArquivo = $file["csv"]["tmp_name"];
        $ponteiroArquivo = fopen($nomeArquivo, "r");

        $LivroDAO = new LivroDAO();
        $LivrosCriados = [];

        while (($linhaArguivo = fgetcsv($ponteiroArquivo, 1000, ";")) !== false) {
            $linhaArguivo = array_map("utf8_encode", $linhaArguivo);

            $Livro = new Livro();
            $Livro->setIdLivro(idLivro: $linhaArguivo[0])
                  ->setNomeLivro(nomeLivro: $linhaArguivo[1]);

            $LivrosCriados[] = $LivroDAO->create($Livro);
        }

        (new Response(
            success: true,
            message: 'Categorias cadastrados com sucesso!',
            data: $LivrosCriados,
            httpCode: 200
        ))->send();

        exit();
    }
}
