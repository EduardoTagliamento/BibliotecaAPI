<?php
require_once "api/src/models/Livro.php";
require_once "api/src/db/Database.php";
require_once "api/src/utils/Logger.php";

class LivroDAO
{
    public function create(Livro $Livro): Livro|false
    {
        $idLivro = $Livro->getIdLivro();
        if (isset($idLivro)) {
            return $this->createWithoutId(Livro: $Livro);
        }
        return $this->createWithId(Livro: $Livro);
    }

    private function createWithoutId(Livro $Livro): Livro
    {
        $query = 'INSERT INTO Livro (
                            nomeLivro,
                            dataLancamento,
                            idCategoria,
                            idAutor
                        ) VALUES ( 
                            :nomeLivro,
                            :dataLancamento,
                            :idCategoria,
                            :idAutor )';

        $statement = Database::getConnection()->prepare($query);

        $statement->bindValue(
            param: ':nomeLivro',
            value: $Livro->getNomeLivro(),
            type: PDO::PARAM_STR
        );

        $statement->bindValue(
            param: ':dataLancamento',
            value: $Livro->getDataLancamento(),
            type: PDO::PARAM_STR
        );

        $statement->bindValue(
            param: ':idCategoria',
            value: $Livro->getCategoria()->getIdCategoria(),
            type: PDO::PARAM_INT
        );

        $statement->bindValue(
            param: ':idAutor',
            value: $Livro->getAutor()->getIdAutor(),
            type: PDO::PARAM_INT
        );

        $statement->execute();

        $Livro->setIdLivro((int) Database::getConnection()->lastInsertId());

        return $Livro;
    }

    private function createWithId(Livro $Livro): Livro
    {
        $query = 'INSERT INTO Livro (
            idLivro,
            nomeLivro,
            dataLancamento,
            idCategoria,
            idAutor
        ) VALUES (
            :idLivro,
            :nomeLivro,
            :dataLancamento,
            :idCategoria,
            :idAutor )';

        $statement = Database::getConnection()->prepare(query: $query);

        $statement->bindValue(
            param: ':idLivro',
            value: $Livro->getIdLivro(),
            type: PDO::PARAM_INT
        );

        $statement->bindValue(
            param: ':nomeLivro',
            value: $Livro->getNomeLivro(),
            type: PDO::PARAM_STR
        );

        $statement->bindValue(
            param: ':dataLancamento',
            value: $Livro->getDataLancamento(),
            type: PDO::PARAM_STR
        );

        $statement->bindValue(
            param: ':idCategoria',
            value: $Livro->getCategoria()->getidCategoria(),
            type: PDO::PARAM_INT
        );

        $statement->bindValue(
            param: ':idAutor',
            value: $Livro->getAutor()->getIdAutor(),
            type: PDO::PARAM_INT
        );

        $statement->execute();

        return $Livro;
    }

    public function readAll(): array
    {
        $query = '
        SELECT
            Livro.idLivro,
            Livro.nomeLivro,
            Livro.dataLancamento,
            Livro.idCategoria,
            Livro.idAutor,
            Autor.nomeAutor,
            Autor.anoNascimento,
            Autor.nacionalidade,
            Categoria.nomeCategoria
        FROM Livro
        JOIN Autor ON Autor.idAutor = Livro.idAutor
        JOIN Categoria ON Livro.idCategoria = Categoria.idCategoria
        ORDER BY nomeLivro ASC
    ';

        $statement = Database::getConnection()->query(query: $query);

        $resultados = [];

        while ($stdLinha = $statement->fetch(mode: PDO::FETCH_OBJ)) {
            $Livro = (new Livro())
                ->setIdLivro(idLivro: $stdLinha->idLivro)
                ->setNomeLivro(nomeLivro: $stdLinha->nomeLivro)
                ->setDataLancamento(dataLancamento: $stdLinha->dataLancamento);

            $Livro->getAutor()
                ->setIdAutor(idAutor: $stdLinha->idAutor)
                ->setNomeAutor(nomeAutor: $stdLinha->nomeAutor)
                ->setNacionalidade(nacionalidade: $stdLinha->nacionalidade);
            $Livro->getCategoria()
                ->setIdCategoria(idCategoria: $stdLinha->idCategoria)
                ->setNomeCategoria(nomeCategoria: $stdLinha->nomeCategoria);

            $resultados[] = $Livro;
        }

        return $resultados;
    }

    public function readById(int $idLivro): array
    {
        $query = '
        SELECT
            Livro.idLivro,
            Livro.nomeLivro,
            Livro.dataLancamento,
            Livro.idCategoria,
            Livro.idAutor,
            Autor.nomeAutor,
            Autor.nacionalidade,
            Autor.anoNascimento,
            Categoria.nomeCategoria
        FROM Livro 
        JOIN Autor ON Autor.idAutor = Livro.idAutor
        JOIN Categoria ON Livro.idCategoria = Categoria.idCategoria
        WHERE idLivro = :idLivro
        ORDER BY nomeLivro ASC
    ';

        $statement = Database::getConnection()->prepare(query: $query);

        $statement->bindValue(
            param: ':idLivro',
            value: $idLivro,
            type: PDO::PARAM_INT
        );

        $statement->execute();

        $Livro = new Livro();

        if ($stdLinha = $statement->fetch(mode: PDO::FETCH_OBJ)) {
            $Livro->setIdLivro(idLivro: $stdLinha->idLivro)
                ->setNomeLivro(nomeLivro: $stdLinha->nomeLivro)
                ->setDataLancamento(dataLancamento: $stdLinha->dataLancamento);

            $Livro->getAutor()
                ->setIdAutor(idAutor: $stdLinha->idAutor)
                ->setNomeAutor(nomeAutor: $stdLinha->nomeAutor)
                ->setNacionalidade(nacionalidade: $stdLinha->nacionalidade)
                ->setAnoNascimento(anoNascimento: $stdLinha->anoNascimento);

            $Livro->getCategoria()
                ->setIdCategoria(idCategoria: $stdLinha->idCategoria)
                ->setNomeCategoria(nomeCategoria: $stdLinha->nomeCategoria);
        }

        return [$Livro];
    }
public function update(Livro $Livro): bool
{
    $query = 'UPDATE Livro
              SET 
                nomeLivro = :nomeLivro,     
                dataLancamento = :dataLancamento,
                idCategoria = :idCategoria,
                idAutor = :idAutor
              WHERE 
                idLivro = :idLivro';

    $statement = Database::getConnection()->prepare($query);

    $statement->bindValue(
        param: ':nomeLivro',
        value: $Livro->getNomeLivro(),
        type: PDO::PARAM_STR
    );

    $statement->bindValue(
        param: ':dataLancamento',
        value: $Livro->getdataLancamento(),
        type: PDO::PARAM_STR
    );

    $statement->bindValue(
        param: ':idCategoria',
        value: $Livro->getCategoria()->getidCategoria(),
        type: PDO::PARAM_INT
    );

    $statement->bindValue(
        param: ':idAutor',
        value: $Livro->getAutor()->getIdAutor(),
        type: PDO::PARAM_INT
    );

    $statement->bindValue(
        param: ':idLivro',
        value: $Livro->getIdLivro(),
        type: PDO::PARAM_INT
    );

    $statement->execute();

    if ($statement->rowCount() > 0) {
        return true;
    }

    return false;
}

public function delete(int $idLivro): bool
{
    $query = 'DELETE FROM 
          Livro 
          WHERE 
          idLivro = :idLivro';

    $statement = Database::getConnection()->prepare($query);

    $statement->bindValue(
        param: ':idLivro',
        value: $idLivro,
        type: PDO::PARAM_INT
    );

    $statement->execute();

    return $statement->rowCount() > 0;
}
}