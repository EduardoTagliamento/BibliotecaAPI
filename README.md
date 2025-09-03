# API RESTful - CRUD de Autor, Categoria e Livro com Autenticação JWT

Esta é uma API RESTful didática construída para fins de aprendizado. A API implementa conceitos de REST e MVC, permitindo realizar operações CRUD (Criar, Ler, Atualizar e Deletar) nas tabelas Autor, Categoria e Livro.

O sistema possui login com token JWT: somente após autenticação bem-sucedida é permitido acessar o CRUD completo.

---

## Recursos

* Autenticação via JWT.
* CRUD completo para Autor, Categoria e Livro.
* Implementação simples de MVC.
* Foco no aprendizado e simplicidade.

---

## Funcionalidades

### Login

**POST /login**
Autentica o usuário e retorna um token JWT.

**Corpo da requisição:**

```json
{
  "usuario": "admin",
  "senha": "123456"
}
```

**Resposta:**

```json
{
  "success": true,
  "message": "Login efetuado com sucesso",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR..."
  }
}
```

---

### Autor

* **GET /autores**: Lista todos os autores.
* **GET /autores/{id}**: Retorna um autor específico.
* **POST /autores**: Cria um novo autor.
* **PUT /autores/{id}**: Atualiza um autor existente.
* **DELETE /autores/{id}**: Deleta um autor.

---

### Categoria

* **GET /categorias**: Lista todas as categorias.
* **GET /categorias/{id}**: Retorna uma categoria específica.
* **POST /categorias**: Cria uma nova categoria.
* **PUT /categorias/{id}**: Atualiza uma categoria existente.
* **DELETE /categorias/{id}**: Deleta uma categoria.

---

### Livro

* **GET /livros**: Lista todos os livros com autor e categoria embutidos.
* **GET /livros/{id}**: Retorna um livro específico.
* **POST /livros**: Cria um novo livro.
* **PUT /livros/{id}**: Atualiza um livro existente.
* **DELETE /livros/{id}**: Deleta um livro.

**Exemplo de JSON para criação/atualização de livro:**

```json
{
  "Livro": {
    "nomeLivro": "Dom Quixote",
    "dataLancamento": "1605-01-01",
    "Autor": { "idAutor": 3 },
    "Categoria": { "idCategoria": 2 }
  }
}
```

**Exemplo de retorno de um livro:**

```json
{
  "idLivro": 1,
  "nomeLivro": "Dom Quixote",
  "dataLancamento": "1605-01-01",
  "Autor": {
    "idAutor": 3,
    "nomeAutor": "Miguel de Cervantes"
  },
  "Categoria": {
    "idCategoria": 2,
    "nomeCategoria": "Romance"
  }
}
```

---

## Tecnologias Utilizadas

* PHP 8.x ou superior
* PDO para interação com o banco de dados
* MySQL/MariaDB para persistência de dados
* MVC para organização do código
* JWT para autenticação
* REST para a estrutura da API

---

## Requisitos

* PHP 8.x ou superior
* Banco de dados MySQL ou MariaDB

---

## Observações de Uso

* Para qualquer operação CRUD, é necessário enviar o **token JWT** obtido no login no header:

```http
Authorization: Bearer <token>
```

* O CRUD permite **selecionar, criar, atualizar e deletar** cada entidade (Autor, Categoria e Livro) via interface front-end ou chamadas HTTP.

---

## Licença

Esta API é licenciada sob a **MIT License**.
