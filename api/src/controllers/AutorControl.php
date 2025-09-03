<?php
require_once "api/src/models/Autor.php";
require_once "api/src/DAO/AutorDAO.php";
require_once "api/src/http/Response.php";
require_once "api/src/utils/Logger.php";

class AutorControl
{

    public function index(): never
    {
        $AutorDAO = new AutorDAO();
        $Autores = $AutorDAO->readAll();
        (new Response(
            success: true,
            message: 'Dados selecionados com sucesso',
            data: ['Autores' => $Autores],
            httpCode: 200
        ))->send();
        exit();
    }

    public function show(int $idAutor): never
    {
        $AutorDAO = new AutorDAO();
        $Autor = $AutorDAO->readById(idAutor: $idAutor);
        if (isset($Autor)) {
            (new Response(
                success: true,
                message: 'Autor encontrado com sucesso',
                data: ['Autores' => $Autor], 
                httpCode: 200 
            ))->send();
        } else {
            (new Response(
                success: false,
                message: 'Autor não encontrado',
                httpCode: 404
            ))->send();
        }
        exit();
    }

    public function listPaginated(int $page = 1, int $limit = 10): never
    {
        if ($page < 1) {
            $page = 1;
        }
        if ($limit < 1) {
            $limit = 10;
        }
        $AutorDAO = new AutorDAO();
        $Autores = $AutorDAO->readByPage(page: $page, limit: $limit);
        (new Response(
            success: true,
            message: 'Autores recuperados com sucesso',
            data: [
                'page' => $page,       
                'limit' => $limit,     
                'Autores' => $Autores    
            ],
            httpCode: 200
        ))->send();
        exit();
    }
    public function store(stdClass $stdAutor): never
    {
        $Autor = new Autor();
        $Autor->setNomeAutor(nomeAutor: $stdAutor->Autor->nomeAutor);
        $Autor->setNacionalidade(nacionalidade: $stdAutor->Autor->nacionalidade);
        $Autor->setAnoNascimento(anoNascimento: $stdAutor->Autor->anoNascimento);
        $AutorDAO = new AutorDAO();
        $novoAutor = $AutorDAO->create($Autor);
        (new Response(
            success: true,
            message: 'Autor cadastrado com sucesso',
            data: ['Autores' => $novoAutor],
            httpCode: 200
        ))->send();
        exit();
    }
    public function edit(stdClass $stdAutor): never
    {
        $AutorDAO = new AutorDAO();
        $Autor = (new Autor())
            ->setIdAutor(idAutor: $stdAutor->Autor->idAutor)
            ->setNomeAutor(nomeAutor: $stdAutor->Autor->nomeAutor)
            ->setNacionalidade(nacionalidade: $stdAutor->Autor->nacionalidade)
            ->setAnoNascimento(anoNascimento: $stdAutor->Autor->anoNascimento);
        if ($AutorDAO->update(Autor: $Autor) == true) {
            (new Response(
                success: true,
                message: "Atualizado com sucesso",
                data: ['Autores' => $Autor],
                httpCode: 200
            ))->send();
            exit();
        } else {
            (new Response(
                success: false,
                message: "Não foi possível atualizar o Autor.",
                error: [
                    'codigoError' => 'validation_error',
                    'message' => 'Não é possível atualizar para um Autor que já existe',
                ],
                httpCode: 400
            ))->send();
            exit();
        }
    }

    public function destroy(int $idAutor): never
    {
        $AutorDAO = new AutorDAO();
        if ($AutorDAO->delete(idAutor: $idAutor) == true) {
            (new Response(httpCode: 204))->send();
        } else {
            (new Response(
                success: false,
                message: 'Não foi possível excluir o Autor',
                error: [
                    'cod' => 'delete_error',
                    'message' => 'O Autor não pode ser excluído'
                ],
                httpCode: 400
            ))->send();
            exit();
        }
    }

    public function exportCSV(): never
    {
        $AutorDAO = new AutorDAO();
        $Autores = $AutorDAO->readAll();
        header(header: 'Content-Type: text/csv; charset=utf-8');
        header(header: 'Content-Disposition: attachment; filename="Autores.csv"');
        $saida = fopen(filename: 'php://output', mode: 'w');
        fputcsv(stream: $saida, fields: ['ID', 'Nome da Autor']);
        foreach ($Autores as $Autor) {
            fputcsv(stream: $saida, fields: [
                $Autor->getIdAutor(),
                $Autor->getNomeAutor(),
                $Autor->getNacionalidade(),
                $Autor->getAnoNascimento()
            ]);
        }
        fclose(stream: $saida);
        exit();
    }

    public function exportJSON(): never
    {
        $AutorDAO = new AutorDAO();
        $Autores = $AutorDAO->readAll();
        header(header: 'Content-Type: application/json; charset=utf-8');
        header(header: 'Content-Disposition: attachment; filename="Autores.json"');
        $exportar = [];
        foreach ($Autores as $Autor) {
            $novoAutor = new Autor();
            $novoAutor
                ->setIdAutor(idAutor: $Autor->getIdAutor())
                ->setNomeAutor(nomeAutor: $Autor->getNomeAutor())
                ->setNacionalidade(nacionalidade: $Autor->getNacionalidade());
            $exportar[] = $novoAutor;
        }

        $resposta = [
            'Autores' => $exportar
        ];
        echo json_encode(value: $resposta);
        exit();
    }

    public function exportXML(): never
    {
        $AutorDAO = new AutorDAO();
        $Autores = $AutorDAO->readAll();
        header(header: 'Content-Type: application/xml; charset=utf-8');
        header(header: 'Content-Disposition: attachment; filename="Autores.xml"');
        $xml = new SimpleXMLElement('<Autores/>');
        foreach ($Autores as $Autor) {
            $AutorXML = $xml->addChild(qualifiedName: 'Autor');
            $AutorXML->addChild(qualifiedName: 'idAutor', value: $Autor->getIdAutor());
            $AutorXML->addChild(qualifiedName: 'nomeAutor', value: $Autor->getNomeAutor());
            $AutorXML->addChild(qualifiedName: 'nacionalidade', value: $Autor->getNacionalidade());
            $AutorXML->addChild(qualifiedName: 'anoNascimento', value: $Autor->getAnoNascimento());
        }
        echo $xml->asXML();
        exit();
    }

public function importXML(array $xmlFile): never
{
    $nomeTemporario = $xmlFile['tmp_name'];
    $xml = simplexml_load_file(filename: $nomeTemporario);

    if (!$xml) {
        (new Response(
            success: false,
            message: 'Erro ao carregar o arquivo XML',
            httpCode: 400
        ))->send();
        exit();
    }

    $AutorDAO = new AutorDAO();
    $AutoresCriados = [];
    $AutoresNaoCriados = [];

    foreach ($xml->Autor as $AutorNode) {
        $Autor = new Autor();
        $Autor->setIdAutor(idAutor: (int) $AutorNode->idAutor)
              ->setNomeAutor(nomeAutor: (string) $AutorNode->nomeAutor)
              ->setNacionalidade(nacionalidade: (string) $AutorNode->nacionalidade)
              ->setAnoNascimento(anoNascimento: (int) $AutorNode->AnoNascimento);

        $AutorCriado = $AutorDAO->create(Autor: $Autor);

        if ($AutorCriado == false) {
            $AutoresNaoCriados[] = $Autor;
        } else {
            $AutoresCriados[] = $Autor;
        }
    }

    (new Response(
        success: true,
        message: 'Importação realizada com sucesso',
        data: [
            "AutoresCriados" => $AutoresCriados,
            "AutoresNaoCriados" => $AutoresNaoCriados,
        ],
        httpCode: 200
    ))->send();

    exit();
}

public function importCSV(array $csvFile): never
{
    $nomeTemporario = $csvFile['tmp_name'];

    if (!is_uploaded_file($nomeTemporario)) {
        (new Response(
            success: false,
            message: 'Arquivo inválido.',
            httpCode: 400
        ))->send();
        exit();
    }

    $ponteiroArquivo = fopen($nomeTemporario, "r");

    if ($ponteiroArquivo === false) {
        (new Response(
            success: false,
            message: 'Não foi possível abrir o arquivo.',
            httpCode: 500
        ))->send();
        exit();
    }

    $AutorDAO = new AutorDAO();
    $AutoresCriados = [];
    $AutoresNaoCriados = [];

    while (($linhaArquivo = fgetcsv($ponteiroArquivo, 1000, ",")) !== false) {
        foreach ($linhaArquivo as &$campo) {
            if (!mb_detect_encoding($campo, 'UTF-8', true)) {
                $campo = mb_convert_encoding($campo, 'UTF-8', 'ISO-8859-1');
            }
        }

        if (count($linhaArquivo) < 2) {
            continue;
        }

        $Autor = new Autor();
        $Autor->setIdAutor($linhaArquivo[0])
              ->setNomeAutor($linhaArquivo[1]);

        $AutorCriado = $AutorDAO->create($Autor);

        if ($AutorCriado == false) {
            $AutoresNaoCriados[] = $Autor;
        } else {
            $AutoresCriados[] = $Autor;
        }
    }

    fclose($ponteiroArquivo);

    (new Response(
        success: true,
        message: 'Importação executada com sucesso.',
        data: [
            "AutoresCriados" => $AutoresCriados,
            "AutoresNaoCriados" => $AutoresNaoCriados,
        ],
        httpCode: 200
    ))->send();

    exit();
}

public function importJson(array $jsonFile): never
{
    $nomeTemporario = $jsonFile['tmp_name'];
    $conteudoArquivo = file_get_contents($nomeTemporario);
    $dadosJson = json_decode($conteudoArquivo);

    if ($dadosJson === null) {
        (new Response(
            success: false,
            message: 'Erro ao decodificar o arquivo JSON',
            httpCode: 400
        ))->send();
        exit();
    }

    if (!isset($dadosJson->Autores)) {
        (new Response(
            success: false,
            message: 'Dados de Autores não encontrados no JSON',
            httpCode: 400
        ))->send();
        exit();
    }

    $AutorDAO = new AutorDAO();
    $AutoresCriados = [];
    $AutoresNaoCriados = [];

    foreach ($dadosJson->Autores as $AutorNode) {
        $Autor = new Autor();
        $Autor->setIdAutor(idAutor: (int) $AutorNode->idAutor)
              ->setNomeAutor(nomeAutor: (string) $AutorNode->nomeAutor)
              ->setNacionalidade(nacionalidade: (string) $AutorNode->nacionalidade)
              ->setAnoNascimento(anoNascimento: (int) $AutorNode->AnoNascimento);

        $AutorCriado = $AutorDAO->create(Autor: $Autor);

        if ($AutorCriado == false) {
            $AutoresNaoCriados[] = $Autor;
        } else {
            $AutoresCriados[] = $Autor;
        }
    }

    (new Response(
        success: true,
        message: 'Importação realizada com sucesso',
        data: [
            "AutoresCriados" => $AutoresCriados,
            "AutoresNaoCriados" => $AutoresNaoCriados,
        ],
        httpCode: 200
    ))->send();

    exit();
    }
}