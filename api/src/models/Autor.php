<?php

declare(strict_types=1);

class Autor implements JsonSerializable
{
    public function __construct(
        private ?int $idAutor = null,
        private string $nomeAutor = "",
        private string $nacionalidade = "",
        private int $anoNascimento = 0
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'idAutor' => $this->idAutor,
            'nomeAutor' => $this->nomeAutor,
            'nacionalidade' => $this->nacionalidade,
            'anoNascimento' => $this->anoNascimento
        ];
    }

    public function getIdAutor(): int|null
    {
        return $this->idAutor;
    }

    public function setIdAutor(int $idAutor): self
    {
        $this->idAutor = $idAutor;
        return $this;
    }

    public function getNomeAutor(): string
    {
        return $this->nomeAutor;
    }

    public function setNomeAutor(string $nomeAutor): self
    {
        $this->nomeAutor = $nomeAutor;
        return $this;
    }

    public function getNacionalidade(): string|null
    {
        return $this->nacionalidade;
    }

    public function setNacionalidade(string $nacionalidade): self
    {
        $this->nacionalidade = $nacionalidade;
        return $this;
    }

    public function getAnoNascimento(): int|null
    {
        return $this->anoNascimento;
    }

    public function setAnoNascimento(int $anoNascimento): self
    {
        $this->anoNascimento = $anoNascimento;
        return $this;
    }
}