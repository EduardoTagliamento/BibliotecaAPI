<?php

declare(strict_types=1);

class Livro implements JsonSerializable
{
    public function __construct(
        private ?int $idLivro = null,
        private string $nomeLivro = "",
        private string $dataLancamento = "",
        private Autor $Autor = new Autor(),
        private Categoria $Categoria = new Categoria()
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'idLivro' => $this->getIdLivro(),
            'nomeLivro' => $this->getNomeLivro(),
            'dataLancamento' => $this->getDataLancamento(),
            'Autor' => [
                'idAutor' => $this->Autor->getIdAutor(),
                'nomeAutor' => $this->Autor->getNomeAutor(),
                'nacionalidade' => $this->Autor->getNacionalidade(),
                'anoNascimento' => $this->Autor->getAnoNascimento()
            ],
            'Categoria' => [
                'idCategoria' => $this->Categoria->getIdCategoria(),
                'nomeCategoria' => $this->Categoria->getNomeCategoria()
            ]
        ];
    }

    public function getIdLivro(): int|null
    {
        return $this->idLivro;
    }

    public function setIdLivro(int $idLivro): self
    {
        $this->idLivro = $idLivro;
        return $this;
    }

    public function getNomeLivro(): string
    {
        return $this->nomeLivro;
    }

    public function setNomeLivro(string $nomeLivro): self
    {
        $this->nomeLivro = $nomeLivro;
        return $this;
    }

    public function getDataLancamento(): string
    {
        return $this->dataLancamento;
    }

    public function setDataLancamento(string $dataLancamento): self
    {
        $this->dataLancamento = $dataLancamento;
        return $this;
    }

    public function getAutor(): Autor
    {
        return $this->Autor;
    }

    public function setAutor(Autor $Autor): self
    {
        $this->Autor = $Autor;
        return $this;
    }

    public function getCategoria(): Categoria
    {
        return $this->Categoria;
    }

    public function setCategoria(Categoria $Categoria): self
    {
        $this->Categoria = $Categoria;
        return $this;
    }
}
