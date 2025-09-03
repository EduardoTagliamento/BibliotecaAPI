<?php

require_once "api/src/models/Categoria.php";
require_once "api/src/db/Database.php";
require_once "api/src/utils/Logger.php";

class CategoriaDAO
{
    public function create(Categoria $Categoria): Categoria
    {
        $idCategoria = $Categoria->getIdCategoria();
        if (isset($idCategoria)) {
            return $this->createWithId(Categoria: $Categoria);
        } else {
            return $this->createWithoutId(Categoria: $Categoria);
        }
    }

    private function createWithoutId(Categoria $Categoria): Categoria
    {
        $query = 'INSERT INTO Categoria (nomeCategoria) VALUES (:nomeCategoria)';
        $statement = Database::getConnection()->prepare(query: $query);
        $statement->bindValue(':nomeCategoria', $Categoria->getnomeCategoria(), PDO::PARAM_STR);
        $statement->execute();
        $Categoria->setIdCategoria(idCategoria: (int) Database::getConnection()->lastInsertId());
        return $Categoria;
    }

    private function createWithId(Categoria $Categoria): Categoria
    {
        $query = 'INSERT INTO Categoria (idCategoria, nomeCategoria) VALUES (:idCategoria, :nomeCategoria)';
        $statement = Database::getConnection()->prepare(query: $query);
        $statement->bindValue(':idCategoria', $Categoria->getIdCategoria(), PDO::PARAM_INT);
        $statement->bindValue(':nomeCategoria', $Categoria->getNomeCategoria(), PDO::PARAM_STR);
        $statement->execute();
        return $Categoria;
    }

    public function delete(int $idCategoria): bool
    {
        $query = 'DELETE FROM Categoria WHERE idCategoria = :idCategoria';
        $statement = Database::getConnection()->prepare(query: $query);
        $statement->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
        $statement->execute();
        return $statement->rowCount() > 0;
    }

    public function readAll(): array
    {
        $resultados = [];
        $query = 'SELECT idCategoria, nomeCategoria FROM Categoria ORDER BY nomeCategoria ASC';
        $statement = Database::getConnection()->query(query: $query);
        while ($linha = $statement->fetch(mode: PDO::FETCH_OBJ)) {
            $Categoria = (new Categoria())
                ->setIdCategoria(idCategoria: $linha->idCategoria)
                ->setNomeCategoria(nomeCategoria: $linha->nomeCategoria);
            $resultados[] = $Categoria;
        }
        return $resultados;
    }

    public function readByName(string $nomeCategoria): Categoria|null
    {
        $query = 'SELECT idCategoria, nomeCategoria FROM Categoria WHERE nomeCategoria = :nome';
        $statement = Database::getConnection()->prepare(query: $query);
        $statement->bindValue(':nome', $nomeCategoria, PDO::PARAM_STR);
        $statement->execute();
        $objStdCategoria = $statement->fetch(mode: PDO::FETCH_OBJ);
        if (!$objStdCategoria) {
            return null;
        }
        return (new Categoria())
            ->setIdCategoria(idCategoria: $objStdCategoria->idCategoria)
            ->setNomeCategoria(nomeCategoria: $objStdCategoria->nomeCategoria);
    }

    public function readById(int $idCategoria): array
    {
        $resultados = [];
        $query = 'SELECT idCategoria, nomeCategoria FROM Categoria WHERE idCategoria = :idCategoria;';
        $statement = Database::getConnection()->prepare(query: $query);
        $statement->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
        $statement->execute();
        $linha = $statement->fetch(mode: PDO::FETCH_OBJ);
        if (!$linha) {
            return [];
        } else {
            $Categoria = (new Categoria())
                ->setIdCategoria(idCategoria: $linha->idCategoria)
                ->setNomeCategoria(nomeCategoria: $linha->nomeCategoria);
            return [$Categoria];
        }
    }

    public function update(Categoria $Categoria): bool
    {
        $query = 'UPDATE Categoria SET nomeCategoria = :nomeCategoria WHERE idCategoria = :idCategoria';
        $statement = Database::getConnection()->prepare(query: $query);
        $statement->bindValue(':nomeCategoria', $Categoria->getnomeCategoria(), PDO::PARAM_STR);
        $statement->bindValue(':idCategoria', $Categoria->getIdCategoria(), PDO::PARAM_INT);
        $statement->execute();
        return $statement->rowCount() > 0;
    }
}