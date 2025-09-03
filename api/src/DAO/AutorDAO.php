<?php

require_once "api/src/models/Autor.php";
require_once "api/src/db/Database.php";
require_once "api/src/utils/Logger.php";

class AutorDAO
{
    public function create(Autor $Autor): Autor
    {
        $idAutor = $Autor->getIdAutor();
        if (isset($idAutor)) {
            return $this->createWithId(Autor: $Autor);
        } else {
            return $this->createWithoutId(Autor: $Autor);
        }
    }

    private function createWithoutId(Autor $Autor): Autor
    {
        $query = 'INSERT INTO Autor (nomeAutor, nacionalidade, anoNascimento) VALUES (:nomeAutor, :nacionalidade, :anoNascimento)';
        $statement = Database::getConnection()->prepare(query: $query);

        $statement->bindValue(':nomeAutor', $Autor->getNomeAutor(), PDO::PARAM_STR);
        $statement->bindValue(':nacionalidade', $Autor->getNacionalidade(), PDO::PARAM_STR);
        $statement->bindValue(':anoNascimento', $Autor->getAnoNascimento(), PDO::PARAM_INT);

        $statement->execute();
        $Autor->setIdAutor(idAutor: (int) Database::getConnection()->lastInsertId());
        return $Autor;
    }

    private function createWithId(Autor $Autor): Autor
    {
        $query = 'INSERT INTO Autor (idAutor, nomeAutor, nacionalidade, anoNascimento) VALUES (:idAutor, :nomeAutor, :nacionalidade, :anoNascimento)';
        $statement = Database::getConnection()->prepare(query: $query);

        $statement->bindValue(':idAutor', $Autor->getIdAutor(), PDO::PARAM_INT);
        $statement->bindValue(':nomeAutor', $Autor->getNomeAutor(), PDO::PARAM_STR);
        $statement->bindValue(':nacionalidade', $Autor->getNacionalidade(), PDO::PARAM_STR);
        $statement->bindValue(':anoNascimento', $Autor->getAnoNascimento(), PDO::PARAM_INT);

        $statement->execute();
        return $Autor;
    }

    public function readByName(string $nomeAutor): Autor|null
    {
        $query = 'SELECT idAutor, nomeAutor FROM Autor WHERE nomeAutor = :nome';
        $statement = Database::getConnection()->prepare(query: $query);
        $statement->bindValue(':nome', $nomeAutor, PDO::PARAM_STR);
        $statement->execute();
        $objStdAutor = $statement->fetch(mode: PDO::FETCH_OBJ);
        if (!$objStdAutor) {
            return null;
        }
        return (new Autor())
            ->setIdAutor(idAutor: $objStdAutor->idAutor)
            ->setNomeAutor(nomeAutor: $objStdAutor->nomeAutor);
    }

    public function delete(int $idAutor): bool
    {
        $query = 'DELETE FROM Autor WHERE idAutor = :idAutor';
        $statement = Database::getConnection()->prepare(query: $query);
        $statement->bindValue(':idAutor', $idAutor, PDO::PARAM_INT);
        $statement->execute();
        return $statement->rowCount() > 0;
    }

    public function readAll(): array
    {
        $resultados = [];
        $query = 'SELECT idAutor, nomeAutor, nacionalidade, anoNascimento FROM Autor ORDER BY nomeAutor ASC';
        $statement = Database::getConnection()->query(query: $query);
        while ($linha = $statement->fetch(mode: PDO::FETCH_OBJ)) {
            $Autor = (new Autor())
                ->setIdAutor(idAutor: $linha->idAutor)
                ->setNomeAutor(nomeAutor: $linha->nomeAutor)
                ->setNacionalidade(nacionalidade: $linha->nacionalidade)
                ->setAnoNascimento(anoNascimento: $linha->anoNascimento);
            $resultados[] = $Autor;
        }
        return $resultados;
    }

    public function readById(int $idAutor): array
    {
        $resultados = [];
        $query = 'SELECT idAutor, nomeAutor, nacionalidade, anoNascimento FROM Autor WHERE idAutor = :idAutor;';
        $statement = Database::getConnection()->prepare(query: $query);
        $statement->bindValue(':idAutor', $idAutor, PDO::PARAM_INT);
        $statement->execute();
        $linha = $statement->fetch(mode: PDO::FETCH_OBJ);
        if (!$linha) {
            return [];
        } else {
            $Autor = (new Autor())
                ->setIdAutor(idAutor: $linha->idAutor)
                ->setNomeAutor(nomeAutor: $linha->nomeAutor)
                ->setNacionalidade(nacionalidade: $linha->nacionalidade)
                ->setAnoNascimento(anoNascimento: $linha->anoNascimento);
            return [$Autor];
        }
    }

    public function update(Autor $Autor): bool
    {
        $query = 'UPDATE Autor SET nomeAutor = :nomeAutor, nacionalidade = :nacionalidade, anoNascimento = :anoNascimento WHERE idAutor = :idAutor';
        $statement = Database::getConnection()->prepare(query: $query);

        $statement->bindValue(':idAutor', $Autor->getIdAutor(), PDO::PARAM_INT);
        $statement->bindValue(':nomeAutor', $Autor->getnomeAutor(), PDO::PARAM_STR);
        $statement->bindValue(':nacionalidade', $Autor->getNacionalidade(), PDO::PARAM_STR);
        $statement->bindValue(':anoNascimento', $Autor->getanoNascimento(), PDO::PARAM_INT);

        $statement->execute();
        return $statement->rowCount() > 0;
    }
}