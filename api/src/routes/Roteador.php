<?php

require_once "api/src/routes/Router.php"; // https://github.com/bramus/router

require_once "api/src/controllers/AutorControl.php";
require_once "api/src/middlewares/AutorMiddleware.php";

require_once "api/src/controllers/LivroControl.php";
require_once "api/src/middlewares/LivroMiddleware.php";

require_once "api/src/middlewares/CategoriaMiddleware.php";
require_once "api/src/controllers/CategoriaControl.php";


/**
 * Classe [Roteador]
 * 
 * Esta classe faz parte de uma API REST didática desenvolvida com o objetivo de
 * ensinar, de forma simples e prática, os principais conceitos da arquitetura REST
 * e do padrão de projeto MVC (Model-View-Controller).
 *
 * A API realiza o CRUD completo (Create, Read, Update, Delete) das tabelas `Autor` e `Livro`,
 * sendo ideal para estudantes e desenvolvedores que estão começando com PHP moderno e boas práticas de organização.
 *
 * A construção passo a passo desta API está disponível gratuitamente na playlist do YouTube:
 * https://www.youtube.com/playlist?list=PLpdOJd7P4_HsiLH8b5uyFAaaox4r547qe
 *
 * @author      Hélio Esperidião
 * @copyright   Copyright (c) 2025 Hélio Esperidião
 * @license     GPL (GNU General Public License)
 * @website http://helioesperidiao.com  
 * @github https://github.com/helioesperidiao
 * @linkedin https://www.linkedin.com/in/helioesperidiao/
 * @youtube https://www.youtube.com/c/HélioEsperidião
 * 
 */
class Roteador
{

    /**
     * Construtor da classe responsável por configurar o roteamento da aplicação.
     *
     * Este método é chamado automaticamente ao instanciar a classe. Ele realiza
     * três tarefas principais:
     *
     * 1. Cria uma instância do objeto `Router`, que será usado para mapear
     *    e gerenciar as rotas da aplicação.
     *
     * 2. Configura os cabeçalhos HTTP padrão da aplicação, como CORS (Cross-Origin
     *    Resource Sharing), tipo de conteúdo, entre outros.
     *
     * 3. Define as rotas que serão aceitas e tratadas pela aplicação, associando
     *    caminhos (URNs) a funções ou controladores específicos.
     *
     * Este padrão garante que a estrutura básica da API esteja pronta assim que a
     * classe for carregada, facilitando o uso e organização do sistema.
     */
    public function __construct(private Router $router = new Router())
    {
        // Cria uma nova instância do sistema de roteamento
        // Essa instância será usada para registrar e tratar rotas da aplicação
        $this->router = new Router();

        // Configura os cabeçalhos HTTP necessários para a API funcionar corretamente
        // Por exemplo: permitir requisições de diferentes domínios, definir tipo de conteúdo etc.
        $this->setupHeaders();

        // Registra todas as rotas que serão aceitas pela aplicação
        // Aqui são mapeadas URNs específicas para controladores e métodos que irão tratá-las
        $this->setupAutorRoutes();
        $this->setupLivroRoutes();
        $this->setupCategoriaRoutes();
        $this->setupBackupRoutes();
        $this->setup404Route();


    }

    private function setup404Route(): void
    {
        $this->router->set404(function (): void {
            header('Content-Type: application/json');
            (new Response(
                success: false,
                message: "Rota não encontrada",
                error: [
                    'code' => 'routing_error',  // Código de erro específico para este tipo de falha.
                    'message' => 'Rota não mapeada'  // Mensagem detalhada da exceção.
                ],
                httpCode: 404  // Código HTTP 404 - not found.
            ))->send();

        });
    }

    /**
     * Configura os cabeçalhos HTTP necessários para a aplicação.
     *
     * Esta função é responsável por configurar os cabeçalhos CORS (Cross-Origin Resource Sharing)
     * e outras definições relacionadas aos métodos HTTP e permissões de acesso da API.
     * 
     * O CORS permite que recursos da API sejam acessados a partir de diferentes origens (domínios),
     * o que é especialmente útil em aplicações front-end que fazem requisições para servidores diferentes.
     * 
     * Os cabeçalhos configurados incluem:
     * - Métodos permitidos (GET, POST, PUT, DELETE)
     * - Origem permitida (qualquer origem, usando "*")
     * - Cabeçalhos permitidos nas requisições (Content-Type, Authorization)
     */
    private function setupHeaders(): void
    {
        // Permite os métodos HTTP GET, POST, PUT e DELETE nas requisições para a API
        header(header: 'Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

        // Permite requisições de qualquer origem (domínio)
        // O "*" significa que qualquer domínio pode acessar os recursos da API
        header(header: 'Access-Control-Allow-Origin: *');

        // Permite os cabeçalhos Content-Type (para especificar o tipo de conteúdo) e Authorization
        // (usado para enviar tokens de autenticação ou credenciais) nas requisições
        header(header: 'Access-Control-Allow-Headers: Content-Type, Authorization');
    }

    /**
     * Envia uma resposta de erro para o cliente e registra a exceção no log.
     *
     * Esta função é responsável por lidar com exceções e erros lançados durante o processamento
     * de requisições na aplicação. Ela registra a exceção no arquivo de log para análise posterior
     * e, em seguida, envia uma resposta HTTP ao cliente com um código de erro 500 (Erro Interno do Servidor).
     * 
     * O objetivo dessa função é fornecer uma maneira centralizada de lidar com erros na aplicação,
     * garantindo que, sempre que um erro ocorrer, ele seja registrado e uma resposta adequada seja enviada
     * ao cliente.
     *
     * Parâmetros:
     * @param Throwable  O lançamento de problema que foi lançada durante o processamento da requisição.
     * @param string $message A mensagem de erro personalizada a ser enviada na resposta para o cliente.
     *
     * Retorno:
     * A função não retorna nenhum valor, pois ela termina o processamento e envia a resposta com a função `exit()`.
     * Isso impede que o código continue sendo executado após o envio da resposta de erro.
     *
     * Fluxo de execução:
     * 1. Registra a exceção no log usando o Logger.
     * 2. Cria uma nova instância da classe Response com os detalhes do erro.
     * 3. Envia a resposta para o cliente.
     * 4. Encerra a execução do script com `exit()`, garantindo que o erro seja tratado imediatamente.
     */
    private function sendErrorResponse(Throwable $throwable, string $message): never
    {
        // Registra a exceção no arquivo de log usando a classe Logger.
        // Isso ajuda a manter um histórico dos erros que ocorreram na aplicação.
        Logger::Log(throwable: $throwable);

        // Cria uma nova resposta HTTP com os detalhes do erro.
        // A resposta inclui:
        // - success: false, indicando que a requisição falhou.
        // - message: uma mensagem personalizada de erro que pode ser exibida ao cliente.
        // - error: um array com o código e a mensagem do erro.
        // - httpCode: 500, indicando um erro interno no servidor.
        (new Response(
            success: false,
            message: $message,
            error: [
                'code' => $throwable->getCode(),  // Código de erro específico para este tipo de falha.
                'message' => $throwable->getMessage()  // Mensagem detalhada da exceção.
            ],
            httpCode: 500  // Código HTTP 500 - Erro Interno do Servidor.
        ))->send();

        // Encerra a execução do script para garantir que o fluxo não continue após o envio da resposta.
        exit();
    }

    /**
     * Configura as rotas da aplicação.
     *
     * Esta função define as rotas da aplicação e associa cada uma a uma função anônima responsável pela
     * execução das operações. As rotas manipulam o recurso "autores", como listar, buscar, criar, atualizar
     * e excluir autores. Abaixo, para cada rota, são explicados os parâmetros esperados, a validação e as ações
     * realizadas.
     *
     * Exemplos de Endpoints:
     * - `GET /autores`: Lista todos os autores ou realiza paginação.
     * - `GET /autores/{id}`: Exibe um Autor específico pelo ID.
     * - `POST /autores`: Cria um novo Autor.
     * - `PUT /autores/{id}`: Atualiza um Autor existente.
     * - `DELETE /autores/{id}`: Exclui um Autor específico pelo ID.
     */
    private function setupAutorRoutes(): void
    {
        // Rota para listar todos os autores ou realizar paginação
        // Exemplo de endpoint: GET /autores
        // Exemplo de endpoint com paginação: GET /autores?page=1&limit=10
        // Fluxo de processamento:
        // 1. Roteador direciona para a rota
        // 2. Middleware valida parâmetros de página e limite
        // 3. Controle executa a lógica de negócios
        // 4. DAO (Data Access Object) interage com o banco de dados
       $this->router->get(pattern: '/autores', fn: function (): never {
            try {
                // Obtém os parâmetros 'page' (página) e 'limit' (limite de registros) da query string (URN)
                // Cria uma instância do controlador de categorias para lidar com as operações de listagem
                $AutorControl = new AutorControl();

                // Verifica se os parâmetros de paginação foram fornecidos na query string
                if ((isset($_GET['page'])) && isset($_GET['limit'])) {
                    $page = $_GET['page'];   // 'page' define a página a ser exibida na listagem
                    $limit = $_GET['limit']; // 'limit' define a quantidade de registros por página

                    // Se os parâmetros de paginação foram fornecidos, valida-os com o middleware
                    (new AutorMiddleware())
                        ->isValidPage(page: $page) // Valida se o número da página é válido
                        ->isValidLimit(limit: $limit); // Valida se o limite de registros é válido

                    // Chama o método do controlador para listar os categorias com paginação
                    $AutorControl->listPaginated(page: $page, limit: $limit);
                } else {
                    // Se os parâmetros de paginação não forem fornecidos, chama o método para listar todos os categorias
                    $AutorControl->index();
                }
            } catch (Throwable $throwable) {
                // Caso ocorra um erro durante o processamento, envia uma resposta de erro para o cliente
                $this->sendErrorResponse(
                    throwable: $throwable,
                    message: 'Erro na seleção de dados'
                );
            }

            // Finaliza a execução do script após o envio da resposta (não continua a execução do código)
            exit();
        });

        // Rota para buscar um Autor específico pelo ID
        // Exemplo de endpoint: GET /autores/123
        // Fluxo de processamento:
        // 1. Roteador direciona a requisição para a rota com o ID do Autor
        // 2. Middleware valida o ID fornecido
        // 3. Controle busca e exibe os dados do Autor no banco de dados
        // 4. DAO (Data Access Object) interage com o banco de dados para recuperar os dados
        $this->router->get(pattern: "/autores/(\d+)", fn: function ($idAutor): never {
            try {
                // Valida o ID do Autor fornecido na URN utilizando o middleware
                // O middleware assegura que o ID seja válido e corresponde a um formato esperado
                $AutorMiddleware = new AutorMiddleware();
                $AutorMiddleware
                    ->isValidId(idAutor: $idAutor); // Valida se o ID do Autor é válido

                // Cria uma instância do controlador de autores e chama o método para exibir os dados do Autor
                // O método 'show' busca os detalhes do Autor baseado no ID fornecido
                $AutorControl = new AutorControl();
                $AutorControl->show(idAutor: $idAutor); // Exibe os dados do Autor com o ID especificado

            } catch (Throwable $throwable) {
                // Caso ocorra um erro, como um ID inválido ou problemas de banco de dados,
                // envia uma resposta de erro para o cliente
                $this->sendErrorResponse(
                    throwable: $throwable, // Passa a exceção gerada
                    message: 'Erro na seleção de dados' // Mensagem de erro informando falha na seleção dos dados
                );
            }

            // Finaliza a execução do script após o envio da resposta (não prossegue com outras execuções)
            exit();
        });

        // Rota para criar um novo Autor
        // Endpoint de exemplo: POST /autores
        // Exemplo de corpo da requisição: {"Autor": {"nomeAutor": "Analista de Sistemas"}}
        // Fluxo de processamento:
        // 1. Roteador direciona a requisição POST para a rota '/autores'
        // 2. Middleware processa e valida os dados enviados no corpo da requisição
        // 3. Controlador armazena o novo Autor no banco de dados
        // 4. DAO (Data Access Object) interage com o banco para inserir o novo Autor
        $this->router->post(pattern: "/autores", fn: function (): never {
            try {
                // Obtém o corpo da requisição enviado em formato JSON
                // A função 'file_get_contents' é utilizada para ler os dados enviados pela requisição
                // que são acessados a partir do 'php://input', um fluxo de entrada para dados brutos.
                $requestBody = file_get_contents(filename: "php://input");

                // Cria uma instância do middleware, responsável por validar e processar os dados da requisição
                // O middleware lida com a conversão e validação dos dados recebidos.
                $AutorMiddleware = new AutorMiddleware();

                // Converte a string JSON recebida para um objeto padrão (StdClass),
                // permitindo que os dados sejam manipulados de maneira simples no código.
                $stdAutor = $AutorMiddleware->stringJsonToStdClass(requestBody: $requestBody);

                // Valida o nome do Autor recebido na requisição:
                // - Verifica se o nome do Autor é válido de acordo com as regras definidas no middleware.
                // - Verifica se já existe um Autor com o mesmo nome no banco de dados.
                $AutorMiddleware
                    ->isValidNomeAutor(nomeAutor: $stdAutor->Autor->nomeAutor) // Valida o nome do Autor
                    ->hasNotAutorByName(nomeAutor: $stdAutor->Autor->nomeAutor); // Verifica se o Autor já existe no banco

                // Após as validações, o controlador é chamado para armazenar o novo Autor no banco de dados
                // O método 'store' do controlador é responsável por realizar a operação de inserção do Autor.
                $AutorControl = new AutorControl();
                $AutorControl->store(stdAutor: $stdAutor); // Armazena o novo Autor no banco de dados

                // Finaliza a execução do script, encerrando a requisição após o processamento.
                exit();
            } catch (Throwable $throwable) {
                // Caso ocorra um erro durante o processamento, por exemplo, erro de validação
                // ou falha ao armazenar os dados, o sistema captura a exceção e envia uma resposta de erro.
                $this->sendErrorResponse(
                    throwable: $throwable, // Passa a exceção gerada para tratamento de erro
                    message: 'Erro ao inserir um novo Autor' // Mensagem explicativa do erro ocorrido
                );

                // Finaliza a execução do script após o erro ser tratado e a resposta enviada.
                exit();
            }
            // Finaliza a execução do script após a resposta ser enviada, não permitindo a execução de mais código.
        });


        // Rota para atualizar um Autor existente
        // Endpoint de exemplo: PUT /autores/123
        // Exemplo de corpo da requisição: {"Autor": {"nomeAutor": "Gerente de TI"}}
        // Fluxo de processamento:
        // 1. Roteador direciona a requisição PUT para a rota '/autores/{id}'
        // 2. Middleware processa e valida os dados enviados no corpo da requisição e o ID do Autor
        // 3. Controlador realiza a atualização do Autor no banco de dados
        // 4. DAO (Data Access Object) interage com o banco para realizar a atualização do Autor
        $this->router->put(pattern: "/autores/(\d+)", fn: function ($id): never {
            try {
                // Obtém o corpo da requisição enviado em formato JSON
                // A função 'file_get_contents' é utilizada para ler os dados enviados pela requisição
                // que são acessados a partir do 'php://input', um fluxo de entrada para dados brutos.
                $requestBody = file_get_contents(filename: "php://input");

                // Cria uma instância do middleware, responsável por validar e processar os dados da requisição
                // O middleware lida com a conversão e validação dos dados recebidos.
                $AutorMiddleware = new AutorMiddleware();

                // Converte a string JSON recebida para um objeto padrão (StdClass),
                // permitindo que os dados sejam manipulados de maneira simples no código.
                $stdAutor = $AutorMiddleware->stringJsonToStdClass(requestBody: $requestBody);

                // Valida o ID do Autor, o nome do Autor e verifica se o Autor com o nome já existe:
                // - Valida o ID do Autor para garantir que ele seja um identificador válido.
                // - Valida o nome do Autor, verificando se ele atende aos critérios definidos.
                // - Verifica se já existe um Autor com o mesmo nome no banco de dados.
                $AutorMiddleware
                    ->isValidId(idAutor: $id) // Valida o ID do Autor
                    ->isValidNomeAutor(nomeAutor: $stdAutor->Autor->nomeAutor) // Valida o nome do Autor
                    ->hasNotAutorByName(nomeAutor: $stdAutor->Autor->nomeAutor); // Verifica se o Autor já existe no banco

                // Após as validações, atribui o ID do Autor ao objeto para garantir que o Autor correto seja atualizado
                // O ID é essencial para localizar o Autor no banco de dados e realizar a atualização.
                $stdAutor->Autor->idAutor = $id;

                // O controlador é chamado para atualizar o Autor no banco de dados
                // O método 'edit' do controlador é responsável por realizar a operação de atualização.
                $AutorControl = new AutorControl();
                $AutorControl->edit(stdAutor: $stdAutor); // Atualiza o Autor no banco de dados

                // Finaliza a execução do script, encerrando a requisição após o processamento.
                exit();
            } catch (Throwable $throwable) {
                // Caso ocorra um erro durante o processamento, por exemplo, erro de validação
                // ou falha ao atualizar os dados, o sistema captura a exceção e envia uma resposta de erro.
                $this->sendErrorResponse(
                    throwable: $throwable, // Passa a exceção gerada para tratamento de erro
                    message: 'Erro na atualização dos dados' // Mensagem explicativa do erro ocorrido
                );

                // Finaliza a execução do script após o erro ser tratado e a resposta enviada.
                exit();
            }
            // Finaliza a execução do script após a resposta ser enviada, não permitindo a execução de mais código.
        });


        // Rota para excluir um Autor específico pelo ID
        // Endpoint de exemplo: DELETE /autores/123
        // Fluxo de processamento:
        // 1. O roteador direciona a requisição DELETE para a rota '/autores/{id}'
        // 2. Middleware valida o ID do Autor a ser excluído
        // 3. Controlador chama a função de exclusão no banco de dados
        // 4. DAO (Data Access Object) interage com o banco de dados para realizar a exclusão do Autor
        $this->router->delete(pattern: "/autores/(\d+)", fn: function ($idAutor): never {
            try {
                // Valida o ID do Autor a ser excluído
                // O middleware é responsável por garantir que o ID fornecido seja válido
                // O método 'isValidId' verifica se o ID é numérico e adequado para ser utilizado na exclusão
                $AutorMiddleware = new AutorMiddleware();
                $AutorMiddleware->isValidId(idAutor: $idAutor);

                // Após a validação do ID, o controlador é chamado para excluir o Autor no banco de dados
                // O método 'destroy' é responsável por realizar a exclusão do Autor com o ID fornecido
                $AutorControl = new AutorControl();
                $AutorControl->destroy(idAutor: $idAutor); // Realiza a exclusão do Autor no banco de dados

                // A execução do script é finalizada, encerrando o processo após a exclusão
                exit();
            } catch (Throwable $throwable) {
                // Caso ocorra um erro durante o processamento (ex: erro de validação ou falha ao excluir no banco),
                // a exceção é capturada e uma resposta de erro é enviada ao cliente.
                $this->sendErrorResponse(
                    throwable: $throwable, // Passa a exceção gerada para o tratamento de erro
                    message: 'Erro na exclusão de dados' // Mensagem explicativa do erro ocorrido
                );

                // Finaliza a execução do script após o erro ser tratado e a resposta enviada
                exit();
            }
            // A execução é finalizada aqui, após a resposta ser enviada.
        });
    }

    private function setupCategoriaRoutes(): void
    {
        // Rota para listar todos os Categorias ou realizar paginação
        // Exemplo de endpoint: GET /categorias
        // Exemplo de endpoint com paginação: GET /categorias?page=1&limit=10
        // Fluxo de processamento:
        // 1. Roteador direciona para a rota
        // 2. Middleware valida parâmetros de página e limite
        // 3. Controle executa a lógica de negócios
        // 4. DAO (Data Access Object) interage com o banco de dados
        $this->router->get(pattern: '/categorias', fn: function (): never {
            try {
                // Obtém os parâmetros 'page' (página) e 'limit' (limite de registros) da query string (URN)
                // Cria uma instância do controlador de Categorias para lidar com as operações de listagem
                $CategoriaControl = new CategoriaControl();
                // Verifica se os parâmetros de paginação foram fornecidos na query string
                if ((isset($_GET['page'])) && isset($_GET['limit'])) {
                    $page = $_GET['page'];   // 'page' define a página a ser exibida na listagem
                    $limit = $_GET['limit']; // 'limit' define a quantidade de registros por página

                    // Se os parâmetros de paginação foram fornecidos, valida-os com o middleware
                    (new AutorMiddleware())
                        ->isValidPage(page: $page) // Valida se o número da página é válido
                        ->isValidLimit(limit: $limit); // Valida se o limite de registros é válido

                    // Chama o método do controlador para listar os categorias com paginação
                    $CategoriaControl->listPaginated(page: $page, limit: $limit);
                } else {
                    // Se os parâmetros de paginação não forem fornecidos, chama o método para listar todos os categorias
                    $CategoriaControl->index();
                }
            } catch (Throwable $throwable) {
                // Caso ocorra um erro durante o processamento, envia uma resposta de erro para o cliente
                $this->sendErrorResponse(
                    throwable: $throwable,
                    message: 'Erro na seleção de dados'
                );
            }

            // Finaliza a execução do script após o envio da resposta (não continua a execução do código)
            exit();
        });

        // Rota para buscar um Categoria específico pelo ID
        // Exemplo de endpoint: GET /categorias/123
        // Fluxo de processamento:
        // 1. Roteador direciona a requisição para a rota com o ID do Categoria
        // 2. Middleware valida o ID fornecido
        // 3. Controle busca e exibe os dados do Categoria no banco de dados
        // 4. DAO (Data Access Object) interage com o banco de dados para recuperar os dados
        $this->router->get(pattern: "/categorias/(\d+)", fn: function ($idCategoria): never {
            try {
                // Valida o ID do Categoria fornecido na URN utilizando o middleware
                // O middleware assegura que o ID seja válido e corresponde a um formato esperado
                $CategoriaMiddleware = new CategoriaMiddleware();
                $CategoriaMiddleware
                    ->isValidId(idCategoria: $idCategoria); // Valida se o ID do Categoria é válido

                // Cria uma instância do controlador de Categorias e chama o método para exibir os dados do Categoria
                // O método 'show' busca os detalhes do Categoria baseado no ID fornecido
                $CategoriaControl = new CategoriaControl();
                $CategoriaControl->show(idCategoria: $idCategoria); // Exibe os dados do Categoria com o ID especificado

            } catch (Throwable $throwable) {
                // Caso ocorra um erro, como um ID inválido ou problemas de banco de dados,
                // envia uma resposta de erro para o cliente
                $this->sendErrorResponse(
                    throwable: $throwable, // Passa a exceção gerada
                    message: 'Erro na seleção de dados' // Mensagem de erro informando falha na seleção dos dados
                );
            }

            // Finaliza a execução do script após o envio da resposta (não prossegue com outras execuções)
            exit();
        });

        // Rota para criar um novo Categoria
        // Endpoint de exemplo: POST /categorias
        // Exemplo de corpo da requisição: {"Categoria": {"nomeCategoria": "Analista de Sistemas"}}
        // Fluxo de processamento:
        // 1. Roteador direciona a requisição POST para a rota '/categorias'
        // 2. Middleware processa e valida os dados enviados no corpo da requisição
        // 3. Controlador armazena o novo Categoria no banco de dados
        // 4. DAO (Data Access Object) interage com o banco para inserir o novo Categoria
        $this->router->post(pattern: "/categorias", fn: function (): never {
            try {
                // Obtém o corpo da requisição enviado em formato JSON
                // A função 'file_get_contents' é utilizada para ler os dados enviados pela requisição
                // que são acessados a partir do 'php://input', um fluxo de entrada para dados brutos.
                $requestBody = file_get_contents(filename: "php://input");

                // Cria uma instância do middleware, responsável por validar e processar os dados da requisição
                // O middleware lida com a conversão e validação dos dados recebidos.
                $CategoriaMiddleware = new CategoriaMiddleware();

                // Converte a string JSON recebida para um objeto padrão (StdClass),
                // permitindo que os dados sejam manipulados de maneira simples no código.
                $stdCategoria = $CategoriaMiddleware->stringJsonToStdClass(requestBody: $requestBody);

                // Valida o nome do Categoria recebido na requisição:
                // - Verifica se o nome do Categoria é válido de acordo com as regras definidas no middleware.
                // - Verifica se já existe um Categoria com o mesmo nome no banco de dados.
                $CategoriaMiddleware
                    ->isValidNomeCategoria(nomeCategoria: $stdCategoria->categoria->nomeCategoria) // Valida o nome do Categoria
                    ->hasNotCategoriaByName(nomeCategoria: $stdCategoria->categoria->nomeCategoria); // Verifica se o Categoria já existe no banco

                // Após as validações, o controlador é chamado para armazenar o novo Categoria no banco de dados
                // O método 'store' do controlador é responsável por realizar a operação de inserção do Categoria.
                $CategoriaControl = new CategoriaControl();
                $CategoriaControl->store(stdCategoria: $stdCategoria); // Armazena o novo Categoria no banco de dados

                // Finaliza a execução do script, encerrando a requisição após o processamento.
                exit();
            } catch (Throwable $throwable) {
                // Caso ocorra um erro durante o processamento, por exemplo, erro de validação
                // ou falha ao armazenar os dados, o sistema captura a exceção e envia uma resposta de erro.
                $this->sendErrorResponse(
                    throwable: $throwable, // Passa a exceção gerada para tratamento de erro
                    message: 'Erro ao inserir um novo Categoria' // Mensagem explicativa do erro ocorrido
                );

                // Finaliza a execução do script após o erro ser tratado e a resposta enviada.
                exit();
            }
            // Finaliza a execução do script após a resposta ser enviada, não permitindo a execução de mais código.
        });


        // Rota para atualizar um Categoria existente
        // Endpoint de exemplo: PUT /categorias/123
        // Exemplo de corpo da requisição: {"Categoria": {"nomeCategoria": "Gerente de TI"}}
        // Fluxo de processamento:
        // 1. Roteador direciona a requisição PUT para a rota '/categorias/{id}'
        // 2. Middleware processa e valida os dados enviados no corpo da requisição e o ID do Categoria
        // 3. Controlador realiza a atualização do Categoria no banco de dados
        // 4. DAO (Data Access Object) interage com o banco para realizar a atualização do Categoria
        $this->router->put(pattern: "/categorias/(\d+)", fn: function ($id): never {
        try {
            // Lê o corpo da requisição
            $requestBody = file_get_contents('php://input');

            // Converte JSON para objeto
            $stdCategoria = json_decode($requestBody, false);

            // Se o objeto não tiver a chave Categoria, cria ela
            if (!isset($stdCategoria->Categoria)) {
                $stdCategoria = (object) ['Categoria' => $stdCategoria];
            }

            // Cria instância do middleware
            $CategoriaMiddleware = new CategoriaMiddleware();

            // Valida o ID e o nome do Categoria
            $CategoriaMiddleware
                ->isValidId(idCategoria: $id)
                ->isValidNomeCategoria(nomeCategoria: $stdCategoria->Categoria->nomeCategoria)
                ->hasNotCategoriaByName(nomeCategoria: $stdCategoria->Categoria->nomeCategoria);

            // Atribui ID ao objeto
            $stdCategoria->Categoria->idCategoria = $id;

            // Atualiza o Categoria via controlador
            $CategoriaControl = new CategoriaControl();
            $CategoriaControl->edit(stdCategoria: $stdCategoria);

            exit();

        } catch (Throwable $throwable) {
            $this->sendErrorResponse(
                throwable: $throwable,
                message: 'Erro na atualização dos dados'
            );
            exit();
        }
     });


        // Rota para excluir um Categoria específico pelo ID
        // Endpoint de exemplo: DELETE /categorias/123
        // Fluxo de processamento:
        // 1. O roteador direciona a requisição DELETE para a rota '/categorias/{id}'
        // 2. Middleware valida o ID do Categoria a ser excluído
        // 3. Controlador chama a função de exclusão no banco de dados
        // 4. DAO (Data Access Object) interage com o banco de dados para realizar a exclusão do Categoria
        $this->router->delete(pattern: "/categorias/(\d+)", fn: function ($idCategoria): never {
            try {
                // Valida o ID do Categoria a ser excluído
                // O middleware é responsável por garantir que o ID fornecido seja válido
                // O método 'isValidId' verifica se o ID é numérico e adequado para ser utilizado na exclusão
                $CategoriaMiddleware = new CategoriaMiddleware();
                $CategoriaMiddleware->isValidId(idCategoria: $idCategoria);

                // Após a validação do ID, o controlador é chamado para excluir o Categoria no banco de dados
                // O método 'destroy' é responsável por realizar a exclusão do Categoria com o ID fornecido
                $CategoriaControl = new CategoriaControl();
                $CategoriaControl->destroy(idCategoria: $idCategoria); // Realiza a exclusão do Categoria no banco de dados

                // A execução do script é finalizada, encerrando o processo após a exclusão
                exit();
            } catch (Throwable $throwable) {
                // Caso ocorra um erro durante o processamento (ex: erro de validação ou falha ao excluir no banco),
                // a exceção é capturada e uma resposta de erro é enviada ao cliente.
                $this->sendErrorResponse(
                    throwable: $throwable, // Passa a exceção gerada para o tratamento de erro
                    message: 'Erro na exclusão de dados' // Mensagem explicativa do erro ocorrido
                );

                // Finaliza a execução do script após o erro ser tratado e a resposta enviada
                exit();
            }
            // A execução é finalizada aqui, após a resposta ser enviada.
        });

         // Rota para exportar livros em formato CSV
        // Endpoint de exemplo: GET /livros/exportar/csv
        // Esta rota permite exportar os dados dos livros em formato CSV para o cliente.
        // Fluxo de processamento:
        // 1. O roteador direciona a requisição GET para a rota '/livros/exportar/csv'
        // 2. O controlador é chamado para gerar e exportar os dados dos livros para um arquivo CSV
        // 3. O DAO (Data Access Object) interage com o banco de dados para obter os dados dos livros
        // 4. O CSV gerado é enviado como resposta para o cliente
        $this->router->get(pattern: "/livros/exportar/csv", fn: function (): never {
            try {
                // Chama o método do controlador para exportar os dados dos livros para um arquivo CSV
                // O método 'exportCSV' do controlador 'LivroControl' é responsável por gerar o arquivo CSV
                // contendo os dados de todos os livros cadastrados no banco de dados
                (new LivroControl())->exportCSV(); // Gera o arquivo CSV e envia a resposta ao cliente

                // Finaliza a execução do script após a exportação ser realizada e a resposta enviada
                exit();
            } catch (Throwable $throwable) {
                // Caso ocorra um erro durante o processo de exportação (ex: falha ao gerar o CSV),
                // a exceção é capturada e uma resposta de erro é enviada ao cliente.
                $this->sendErrorResponse(
                    throwable: $throwable, // Passa a exceção gerada para o tratamento de erro
                    message: 'Erro na exportação CSV' // Mensagem explicativa do erro ocorrido
                );

                // Finaliza a execução do script após o erro ser tratado e a resposta enviada
                exit();
            }
            // A execução é finalizada aqui, após a resposta ser enviada.
        });

        // Rota para exportar livros em formato JSON
        // Endpoint de exemplo: GET /livros/exportar/json
        // Esta rota permite exportar os dados dos livros em formato JSON para o cliente.
        // Fluxo de processamento:
        // 1. O roteador direciona a requisição GET para a rota '/livros/exportar/json'
        // 2. O controlador é chamado para gerar e exportar os dados dos livros para um arquivo JSON
        // 3. O DAO (Data Access Object) interage com o banco de dados para obter os dados dos livros
        // 4. O JSON gerado é enviado como resposta para o cliente
        $this->router->get(pattern: "/livros/exportar/json", fn: function (): never {
            try {
                // Chama o método do controlador para exportar os dados dos livros para um arquivo JSON
                // O método 'exportJSON' do controlador 'LivroControl' é responsável por gerar o arquivo JSON
                // contendo os dados de todos os livros cadastrados no banco de dados
                (new LivroControl())->exportJSON(); // Gera o arquivo JSON e envia a resposta ao cliente

                // Finaliza a execução do script após a exportação ser realizada e a resposta enviada
                exit();
            } catch (Throwable $throwable) {
                // Caso ocorra um erro durante o processo de exportação (ex: falha ao gerar o JSON),
                // a exceção é capturada e uma resposta de erro é enviada ao cliente.
                $this->sendErrorResponse(
                    throwable: $throwable, // Passa a exceção gerada para o tratamento de erro
                    message: 'Erro na exportação JSON' // Mensagem explicativa do erro ocorrido
                );

                // Finaliza a execução do script após o erro ser tratado e a resposta enviada
                exit();
            }
            // A execução é finalizada aqui, após a resposta ser enviada.
        });
        // Rota para exportar livros em formato XML
        // Endpoint de exemplo: GET /livros/exportar/xml
        // Esta rota permite exportar os dados dos livros em formato XML para o cliente.
        // Fluxo de processamento:
        // 1. O roteador direciona a requisição GET para a rota '/livros/exportar/xml'
        // 2. O controlador é chamado para gerar e exportar os dados dos livros para um arquivo XML
        // 3. O DAO (Data Access Object) interage com o banco de dados para obter os dados dos livros
        // 4. O XML gerado é enviado como resposta para o cliente
        $this->router->get(pattern: "/livros/exportar/xml", fn: function (): never {
            try {
                // Chama o método do controlador para exportar os dados dos livros para um arquivo XML
                // O método 'exportXML' do controlador 'LivroControl' é responsável por gerar o arquivo XML
                // contendo os dados de todos os livros cadastrados no banco de dados
                (new LivroControl())->exportXML(); // Gera o arquivo XML e envia a resposta ao cliente

                // Finaliza a execução do script após a exportação ser realizada e a resposta enviada
                exit();
            } catch (Throwable $throwable) {
                // Caso ocorra um erro durante o processo de exportação (ex: falha ao gerar o XML),
                // a exceção é capturada e uma resposta de erro é enviada ao cliente.
                $this->sendErrorResponse(
                    throwable: $throwable, // Passa a exceção gerada para o tratamento de erro
                    message: 'Erro na exportação XML' // Mensagem explicativa do erro ocorrido
                );

                // Finaliza a execução do script após o erro ser tratado e a resposta enviada
                exit();
            }
            // A execução é finalizada aqui, após a resposta ser enviada.
        });

        // Rota para importar livros a partir de um arquivo CSV
        // Endpoint de exemplo: POST /livros/importar/csv
        // Esta rota permite importar dados de livros a partir de um arquivo CSV enviado pelo cliente.
        // Fluxo de processamento:
        // 1. O roteador direciona a requisição POST para a rota '/livros/importar/csv'
        // 2. O controlador é chamado para processar o arquivo CSV e importar os dados dos livros
        // 3. O arquivo CSV é enviado pelo cliente via $_FILES['csv']
        // 4. O controlador interage com o DAO para inserir os dados no banco de dados
        $this->router->post(pattern: "/livros/importar/csv", fn: function (): never {
            // Chama o método do controlador para importar os livros a partir do arquivo CSV
            // O método 'importCSV' do controlador 'LivroControl' é responsável por processar o arquivo CSV
            // e inserir os dados dos livros no banco de dados
            $controle = new LivroControl();
            $controle->importCSV(csvFile: $_FILES['csv']); // Processa e importa os dados do arquivo CSV

            // Finaliza a execução do script após a importação ser realizada
            exit();
        });




        // Rota para importar livros a partir de um arquivo JSON
        // Endpoint de exemplo: POST /livros/importar/json
        // Esta rota permite importar dados de livros a partir de um arquivo JSON enviado pelo cliente.
        // Fluxo de processamento:
        // 1. O roteador direciona a requisição POST para a rota '/livros/importar/json'
        // 2. O controlador é chamado para processar o arquivo JSON e importar os dados dos livros
        // 3. O arquivo JSON é enviado pelo cliente via $_FILES['json']
        // 4. O controlador interage com o DAO para inserir os dados no banco de dados
        $this->router->post(pattern: "/livros/importar/json", fn: function (): never {
            // Cria uma instância do controlador para lidar com a importação dos livros
            $controle = new LivroControl();

            // Chama o método do controlador para importar os livros a partir do arquivo JSON
            // O método 'importJson' do controlador 'LivroControl' é responsável por processar o arquivo JSON
            // e inserir os dados dos livros no banco de dados
            $controle->importJson(jsonFile: $_FILES['json']); // Processa e importa os dados do arquivo JSON

            // Finaliza a execução do script após a importação ser realizada
            exit();
        });

        // Rota para importar livros a partir de um arquivo XML
        // Endpoint de exemplo: POST /livros/importar/xml
        // Esta rota permite importar dados de livros a partir de um arquivo XML enviado pelo cliente.
        // Fluxo de processamento:
        // 1. O roteador direciona a requisição POST para a rota '/livros/importar/xml'
        // 2. O controlador é chamado para processar o arquivo XML e importar os dados dos livros
        // 3. O arquivo XML é enviado pelo cliente via $_FILES['xml']
        // 4. O controlador interage com o DAO para inserir os dados no banco de dados
        $this->router->post(pattern: "/livros/importar/xml", fn: function (): never {
            // Cria uma instância do controlador para lidar com a importação dos livros
            $controle = new LivroControl();

            // Chama o método do controlador para importar os livros a partir do arquivo XML
            // O método 'importXML' do controlador 'LivroControl' é responsável por processar o arquivo XML
            // e inserir os dados dos livros no banco de dados
            $controle->importXML(xmlFile: $_FILES['xml']); // Processa e importa os dados do arquivo XML

            // Finaliza a execução do script após a importação ser realizada
            exit();
        });
    }

    private function setupLivroRoutes(): void
    {
        $this->router->get(pattern: "/livros", fn: function (): never {
            (new LivroControl())->index();
        });

        // Define uma rota para a obtenção de um funcionário específico pelo ID
        $this->router->get(pattern: "/livros/(\d+)", fn: function ($idLivro): never {
            (new LivroControl())->controleLivroReadById(idLivro: $idLivro);
        });

        // Define uma rota para a criação de um novo funcionário
        $this->router->post(pattern: "/livros", fn: function (): never {

            try {
                $requestBody = file_get_contents(filename: "php://input");

                $LivroMiddlware = new LivroMiddleware();

                $stdLivro = $LivroMiddlware->stringJsonToStdClass(requestBody: $requestBody);

                $LivroMiddlware
                    ->isValidNomeLivro(nomeLivro: $stdLivro->Livro->nomeLivro);

                //->hasNotLivroByEmail($email)
                $AutorMiddleware = new AutorMiddleware();
                $AutorMiddleware
                    ->isValidId(idAutor: $stdLivro->Livro->Autor->idAutor)
                    ->hasAutorById(idAutor: $stdLivro->Livro->Autor->idAutor);
                $CategoriaMiddleware = new CategoriaMiddleware();
                $CategoriaMiddleware
                    ->isValidId(idCategoria: $stdLivro->Livro->Categoria->idCategoria)
                    ->hasCategoriaById(idCategoria: $stdLivro->Livro->Categoria->idCategoria);

                $LivroControl = new LivroControl();
                $LivroControl->store(stdLivro: $stdLivro);
                //echo "recebeu o texto json:  $requestBody";
            } catch (Throwable $throwable) {
                $this->sendErrorResponse(throwable: $throwable, message: 'Erro na seleção de autores');
            }
            exit();
        });

        // Define uma rota para a atualização de um funcionário existente pelo ID
        $this->router->put(pattern: "/livros/(\d+)", fn: function (int $idLivro): never {
            // Requer o arquivo de controle responsável por atualizar um funcionário pelo ID
            try {
                $requestBody = file_get_contents('php://input');
                $stdLivro = json_decode($requestBody, false);

                // Se o JSON veio sem a chave "Livro", encapsula
                if (!isset($stdLivro->Livro)) {
                    $stdLivro = (object) ['Livro' => $stdLivro];
                }

                $LivroMiddlware = new LivroMiddleware();

                $LivroMiddlware
                    ->isValidNomeLivro(nomeLivro: $stdLivro->Livro->nomeLivro)
                    ->isValidId(idLivro: $idLivro);

                $stdLivro->Livro->idLivro = $idLivro;

                $LivroControl = new LivroControl();
                $LivroControl->edit(stdLivro: $stdLivro);
            } catch (Throwable $throwable) {
                $this->sendErrorResponse(throwable: $throwable, message: 'Erro na atualização do Autor');
            }
            exit();
        });

        // Define uma rota para a exclusão de um funcionário existente pelo ID
        $this->router->delete(pattern: "/livros/(\d+)", fn: function (int $idLivro): never {
            // Requer o arquivo de controle responsável por excluir um funcionário pelo ID
            try {
                $LivroMiddlware = new LivroMiddleware();
                $LivroMiddlware->isValidId(idLivro: $idLivro);
                $LivroControl = new LivroControl();
                $LivroControl->destroy($idLivro);
                /*
                  
                                 $AutorMiddlwaware = new AutorMiddleware();
                $AutorMiddlwaware->isValidId(idAutor: $idAutor);
                $AutorControl = new AutorControl();
                $AutorControl->destroy(idAutor: $idAutor);
                  
                 */
            } catch (Throwable $throwable) {
                $this->sendErrorResponse(throwable: $throwable, message: 'Erro na atualização do Autor');
            }
            exit();
        });

        // Define uma rota para enviar um arquivo CSV para cadastrar todos os autores
        $this->router->post(pattern: "/livros/csv", fn: function (): never {
            // Requer o arquivo de controle responsável por processar o arquivo CSV e cadastrar os autores

            exit();
        });

         // Rota para exportar livros em formato CSV
        // Endpoint de exemplo: GET /livros/exportar/csv
        // Esta rota permite exportar os dados dos livros em formato CSV para o cliente.
        // Fluxo de processamento:
        // 1. O roteador direciona a requisição GET para a rota '/livros/exportar/csv'
        // 2. O controlador é chamado para gerar e exportar os dados dos livros para um arquivo CSV
        // 3. O DAO (Data Access Object) interage com o banco de dados para obter os dados dos livros
        // 4. O CSV gerado é enviado como resposta para o cliente
        $this->router->get(pattern: "/livros/exportar/csv", fn: function (): never {
            try {
                // Chama o método do controlador para exportar os dados dos livros para um arquivo CSV
                // O método 'exportCSV' do controlador 'LivroControl' é responsável por gerar o arquivo CSV
                // contendo os dados de todos os livros cadastrados no banco de dados
                (new LivroControl())->exportCSV(); // Gera o arquivo CSV e envia a resposta ao cliente

                // Finaliza a execução do script após a exportação ser realizada e a resposta enviada
                exit();
            } catch (Throwable $throwable) {
                // Caso ocorra um erro durante o processo de exportação (ex: falha ao gerar o CSV),
                // a exceção é capturada e uma resposta de erro é enviada ao cliente.
                $this->sendErrorResponse(
                    throwable: $throwable, // Passa a exceção gerada para o tratamento de erro
                    message: 'Erro na exportação CSV' // Mensagem explicativa do erro ocorrido
                );

                // Finaliza a execução do script após o erro ser tratado e a resposta enviada
                exit();
            }
            // A execução é finalizada aqui, após a resposta ser enviada.
        });

        // Rota para exportar livros em formato JSON
        // Endpoint de exemplo: GET /livros/exportar/json
        // Esta rota permite exportar os dados dos livros em formato JSON para o cliente.
        // Fluxo de processamento:
        // 1. O roteador direciona a requisição GET para a rota '/livros/exportar/json'
        // 2. O controlador é chamado para gerar e exportar os dados dos livros para um arquivo JSON
        // 3. O DAO (Data Access Object) interage com o banco de dados para obter os dados dos livros
        // 4. O JSON gerado é enviado como resposta para o cliente
        $this->router->get(pattern: "/livros/exportar/json", fn: function (): never {
            try {
                // Chama o método do controlador para exportar os dados dos livros para um arquivo JSON
                // O método 'exportJSON' do controlador 'LivroControl' é responsável por gerar o arquivo JSON
                // contendo os dados de todos os livros cadastrados no banco de dados
                (new LivroControl())->exportJSON(); // Gera o arquivo JSON e envia a resposta ao cliente

                // Finaliza a execução do script após a exportação ser realizada e a resposta enviada
                exit();
            } catch (Throwable $throwable) {
                // Caso ocorra um erro durante o processo de exportação (ex: falha ao gerar o JSON),
                // a exceção é capturada e uma resposta de erro é enviada ao cliente.
                $this->sendErrorResponse(
                    throwable: $throwable, // Passa a exceção gerada para o tratamento de erro
                    message: 'Erro na exportação JSON' // Mensagem explicativa do erro ocorrido
                );

                // Finaliza a execução do script após o erro ser tratado e a resposta enviada
                exit();
            }
            // A execução é finalizada aqui, após a resposta ser enviada.
        });
        // Rota para exportar livros em formato XML
        // Endpoint de exemplo: GET /livros/exportar/xml
        // Esta rota permite exportar os dados dos livros em formato XML para o cliente.
        // Fluxo de processamento:
        // 1. O roteador direciona a requisição GET para a rota '/livros/exportar/xml'
        // 2. O controlador é chamado para gerar e exportar os dados dos livros para um arquivo XML
        // 3. O DAO (Data Access Object) interage com o banco de dados para obter os dados dos livros
        // 4. O XML gerado é enviado como resposta para o cliente
        $this->router->get(pattern: "/livros/exportar/xml", fn: function (): never {
            try {
                // Chama o método do controlador para exportar os dados dos livros para um arquivo XML
                // O método 'exportXML' do controlador 'LivroControl' é responsável por gerar o arquivo XML
                // contendo os dados de todos os livros cadastrados no banco de dados
                (new LivroControl())->exportXML(); // Gera o arquivo XML e envia a resposta ao cliente

                // Finaliza a execução do script após a exportação ser realizada e a resposta enviada
                exit();
            } catch (Throwable $throwable) {
                // Caso ocorra um erro durante o processo de exportação (ex: falha ao gerar o XML),
                // a exceção é capturada e uma resposta de erro é enviada ao cliente.
                $this->sendErrorResponse(
                    throwable: $throwable, // Passa a exceção gerada para o tratamento de erro
                    message: 'Erro na exportação XML' // Mensagem explicativa do erro ocorrido
                );

                // Finaliza a execução do script após o erro ser tratado e a resposta enviada
                exit();
            }
            // A execução é finalizada aqui, após a resposta ser enviada.
        });

        // Rota para importar livros a partir de um arquivo CSV
        // Endpoint de exemplo: POST /livros/importar/csv
        // Esta rota permite importar dados de livros a partir de um arquivo CSV enviado pelo cliente.
        // Fluxo de processamento:
        // 1. O roteador direciona a requisição POST para a rota '/livros/importar/csv'
        // 2. O controlador é chamado para processar o arquivo CSV e importar os dados dos livros
        // 3. O arquivo CSV é enviado pelo cliente via $_FILES['csv']
        // 4. O controlador interage com o DAO para inserir os dados no banco de dados
        $this->router->post(pattern: "/livros/importar/csv", fn: function (): never {
            // Chama o método do controlador para importar os livros a partir do arquivo CSV
            // O método 'importCSV' do controlador 'LivroControl' é responsável por processar o arquivo CSV
            // e inserir os dados dos livros no banco de dados
            $controle = new LivroControl();
            $controle->importCSV(csvFile: $_FILES['csv']); // Processa e importa os dados do arquivo CSV

            // Finaliza a execução do script após a importação ser realizada
            exit();
        });




        // Rota para importar livros a partir de um arquivo JSON
        // Endpoint de exemplo: POST /livros/importar/json
        // Esta rota permite importar dados de livros a partir de um arquivo JSON enviado pelo cliente.
        // Fluxo de processamento:
        // 1. O roteador direciona a requisição POST para a rota '/livros/importar/json'
        // 2. O controlador é chamado para processar o arquivo JSON e importar os dados dos livros
        // 3. O arquivo JSON é enviado pelo cliente via $_FILES['json']
        // 4. O controlador interage com o DAO para inserir os dados no banco de dados
        $this->router->post(pattern: "/livros/importar/json", fn: function (): never {
            // Cria uma instância do controlador para lidar com a importação dos livros
            $controle = new LivroControl();

            // Chama o método do controlador para importar os livros a partir do arquivo JSON
            // O método 'importJson' do controlador 'LivroControl' é responsável por processar o arquivo JSON
            // e inserir os dados dos livros no banco de dados
            $controle->importJson(jsonFile: $_FILES['json']); // Processa e importa os dados do arquivo JSON

            // Finaliza a execução do script após a importação ser realizada
            exit();
        });

        // Rota para importar livros a partir de um arquivo XML
        // Endpoint de exemplo: POST /livros/importar/xml
        // Esta rota permite importar dados de livros a partir de um arquivo XML enviado pelo cliente.
        // Fluxo de processamento:
        // 1. O roteador direciona a requisição POST para a rota '/livros/importar/xml'
        // 2. O controlador é chamado para processar o arquivo XML e importar os dados dos livros
        // 3. O arquivo XML é enviado pelo cliente via $_FILES['xml']
        // 4. O controlador interage com o DAO para inserir os dados no banco de dados
        $this->router->post(pattern: "/livros/importar/xml", fn: function (): never {
            // Cria uma instância do controlador para lidar com a importação dos livros
            $controle = new LivroControl();

            // Chama o método do controlador para importar os livros a partir do arquivo XML
            // O método 'importXML' do controlador 'LivroControl' é responsável por processar o arquivo XML
            // e inserir os dados dos livros no banco de dados
            $controle->importXML(xmlFile: $_FILES['xml']); // Processa e importa os dados do arquivo XML

            // Finaliza a execução do script após a importação ser realizada
            exit();
        });
    }


    private function setupBackupRoutes(): void
    {
        // Rota para listar todos os autores ou realizar paginação
        // Endpoint de exemplo: GET /autores?page=1&limit=10
        $this->router->get(pattern: '/backup', fn: function (): never {
            try {
                require_once "api/src/db/Database.php";
                Database::backup();
            } catch (Throwable $throwable) {
                // Caso ocorra um erro, chama a função para enviar uma resposta de erro ao cliente
                $this->sendErrorResponse(throwable: $throwable, message: 'Erro na seleção de dados');
            }
            // Finaliza a execução do script após a resposta ser enviada
            exit();
        });
    }




    


    /**
     * Inicia o roteador e executa o processamento da requisição.
     *
     * Este método é responsável por iniciar o processo de roteamento das requisições HTTP, 
     * acionando o roteador configurado e fazendo com que ele trate a requisição atual. 
     * Ele garante que as rotas definidas sejam executadas corretamente com base na URN da requisição.
     * 
     * Exemplos de operação:
     * - O método `run()` do roteador é chamado, o que permite que o framework ou sistema processe
     *   as rotas configuradas e as ações correspondentes.
     * - Após a execução deste método, a requisição é tratada, uma resposta é gerada e enviada ao cliente.
     */
    public function start(): void
    {
        // Executa o roteador para processar a requisição e buscar a rota correspondente
        $this->router->run();
    }
}
