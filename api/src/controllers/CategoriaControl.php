<?php

class CategoriaControl
{
    public function index(): never
    {
        $CategoriaDAO = new CategoriaDAO();
        $Categorias = $CategoriaDAO->readAll();

        (new Response(
            success: true,
            message: 'Dados selecionados com sucesso',
            data: ['Categorias' => $Categorias],
            httpCode: 200
        ))->send();

        exit();
    }

    public function show(int $idCategoria): never
    {
        $CategoriaDAO = new CategoriaDAO();
        $Categoria = $CategoriaDAO->readById(idCategoria: $idCategoria);

        if (isset($Categoria)) {
            (new Response(
                success: true,
                message: 'Categoria encontrado com sucesso',
                data: ['Categorias' => $Categoria],
                httpCode: 200
            ))->send();
        } else {
            (new Response(
                success: false,
                message: 'Categoria não encontrado',
                httpCode: 404
            ))->send();
        }

        exit();
    }

    public function listPaginated(int $page = 1, int $limit = 10): never
    {
        if ($page < 1) $page = 1;
        if ($limit < 1) $limit = 10;

        $CategoriaDAO = new CategoriaDAO();
        $Categorias = $CategoriaDAO->readByPage(page: $page, limit: $limit);

        (new Response(
            success: true,
            message: 'Categorias recuperados com sucesso',
            data: [
                'page' => $page,
                'limit' => $limit,
                'Categorias' => $Categorias
            ],
            httpCode: 200
        ))->send();

        exit();
    }

    public function store(stdClass $stdCategoria): never
    {
        $Categoria = new Categoria();
        $Categoria->setNomeCategoria(nomeCategoria: $stdCategoria->categoria->nomeCategoria);

        $CategoriaDAO = new CategoriaDAO();
        $novoCategoria = $CategoriaDAO->create($Categoria);

        (new Response(
            success: true,
            message: 'Categoria cadastrado com sucesso',
            data: ['Categorias' => $novoCategoria],
            httpCode: 200
        ))->send();

        exit();
    }

    public function edit(stdClass $stdCategoria): never
    {
        $CategoriaDAO = new CategoriaDAO();

        $Categoria = (new Categoria())
            ->setIdCategoria(idCategoria: $stdCategoria->Categoria->idCategoria)
            ->setNomeCategoria(nomeCategoria: $stdCategoria->Categoria->nomeCategoria);

        if ($CategoriaDAO->update(Categoria: $Categoria) == true) {
            (new Response(
                success: true,
                message: "Atualizado com sucesso",
                data: ['Categorias' => $Categoria],
                httpCode: 200
            ))->send();
        } else {
            (new Response(
                success: false,
                message: "Não foi possível atualizar o Categoria.",
                error: [
                    'codigoError' => 'validation_error',
                    'message' => 'Não é possível atualizar para um Categoria que já existe',
                ],
                httpCode: 400
            ))->send();
        }

        exit();
    }

    public function destroy(int $idCategoria): never
    {
        $CategoriaDAO = new CategoriaDAO();

        if ($CategoriaDAO->delete(idCategoria: $idCategoria) == true) {
            (new Response(httpCode: 204))->send();
        } else {
            (new Response(
                success: false,
                message: 'Não foi possível excluir o Categoria',
                error: [
                    'cod' => 'delete_error',
                    'message' => 'O Categoria não pode ser excluído'
                ],
                httpCode: 400
            ))->send();
        }

        exit();
    }

    public function exportCSV(): never
    {
        $CategoriaDAO = new CategoriaDAO();
        $Categorias = $CategoriaDAO->readAll();

        header(header: 'Content-Type: text/csv; charset=utf-8');
        header(header: 'Content-Disposition: attachment; filename="Categorias.csv"');

        $saida = fopen(filename: 'php://output', mode: 'w');
        fputcsv(stream: $saida, fields: ['ID', 'Nome da Categoria']);

        foreach ($Categorias as $Categoria) {
            fputcsv(stream: $saida, fields: [
                $Categoria->getIdCategoria(),
                $Categoria->getNomeCategoria()
            ]);
        }

        fclose(stream: $saida);
        exit();
    }

    public function exportJSON(): never
    {
        $CategoriaDAO = new CategoriaDAO();
        $Categorias = $CategoriaDAO->readAll();

        header(header: 'Content-Type: application/json; charset=utf-8');
        header(header: 'Content-Disposition: attachment; filename="Categorias.json"');

        $exportar = [];

        foreach ($Categorias as $Categoria) {
            $novoCategoria = new Categoria();
            $novoCategoria
                ->setIdCategoria(idCategoria: $Categoria->getIdCategoria())
                ->setNomeCategoria(nomeCategoria: $Categoria->getNomeCategoria());

            $exportar[] = $novoCategoria;
        }

        $resposta = [
            'Categorias' => $exportar
        ];

        echo json_encode(value: $resposta);
        exit();
    }

    public function exportXML(): never
    {
        $CategoriaDAO = new CategoriaDAO();
        $Categorias = $CategoriaDAO->readAll();

        header(header: 'Content-Type: application/xml; charset=utf-8');
        header(header: 'Content-Disposition: attachment; filename="Categorias.xml"');

        $xml = new SimpleXMLElement('<Categorias/>');

        foreach ($Categorias as $Categoria) {
            $CategoriaXML = $xml->addChild(qualifiedName: 'Categoria');
            $CategoriaXML->addChild(qualifiedName: 'idCategoria', value: $Categoria->getIdCategoria());
            $CategoriaXML->addChild(qualifiedName: 'nomeCategoria', value: $Categoria->getNomeCategoria());
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

    $CategoriaDAO = new CategoriaDAO();
    $CategoriasCriados = [];
    $CategoriasNaoCriados = [];

    foreach ($xml->Categoria as $CategoriaNode) {
        $Categoria = new Categoria();
        $Categoria->setIdCategoria(idCategoria: (int) $CategoriaNode->idCategoria)
                  ->setNomeCategoria(nomeCategoria: (string) $CategoriaNode->nomeCategoria);

        $CategoriaCriado = $CategoriaDAO->create(Categoria: $Categoria);

        if ($CategoriaCriado == false) {
            $CategoriasNaoCriados[] = $Categoria;
        } else {
            $CategoriasCriados[] = $Categoria;
        }
    }

    (new Response(
        success: true,
        message: 'Importação realizada com sucesso',
        data: [
            "CategoriasCriados" => $CategoriasCriados,
            "CategoriasNaoCriados" => $CategoriasNaoCriados,
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

    $CategoriaDAO = new CategoriaDAO();
    $CategoriasCriados = [];
    $CategoriasNaoCriados = [];

    while (($linhaArquivo = fgetcsv($ponteiroArquivo, 1000, ",")) !== false) {
        foreach ($linhaArquivo as &$campo) {
            if (!mb_detect_encoding($campo, 'UTF-8', true)) {
                $campo = mb_convert_encoding($campo, 'UTF-8', 'ISO-8859-1');
            }
        }

        if (count($linhaArquivo) < 2) {
            continue;
        }

        $Categoria = new Categoria();
        $Categoria->setIdCategoria($linhaArquivo[0])
                  ->setNomeCategoria($linhaArquivo[1]);

        $CategoriaCriado = $CategoriaDAO->create($Categoria);

        if ($CategoriaCriado == false) {
            $CategoriasNaoCriados[] = $Categoria;
        } else {
            $CategoriasCriados[] = $Categoria;
        }
    }

    fclose($ponteiroArquivo);

    (new Response(
        success: true,
        message: 'Importação executada com sucesso.',
        data: [
            "CategoriasCriados" => $CategoriasCriados,
            "CategoriasNaoCriados" => $CategoriasNaoCriados,
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

    if (!isset($dadosJson->Categorias)) {
        (new Response(
            success: false,
            message: 'Dados de Categorias não encontrados no JSON',
            httpCode: 400
        ))->send();
        exit();
    }

    $CategoriaDAO = new CategoriaDAO();
    $CategoriasCriados = [];
    $CategoriasNaoCriados = [];

    foreach ($dadosJson->Categorias as $CategoriaNode) {
        $Categoria = new Categoria();
        $Categoria->setIdCategoria(idCategoria: (int) $CategoriaNode->idCategoria)
                  ->setNomeCategoria(nomeCategoria: (string) $CategoriaNode->nomeCategoria);

        $CategoriaCriado = $CategoriaDAO->create(Categoria: $Categoria);

        if ($CategoriaCriado == false) {
            $CategoriasNaoCriados[] = $Categoria;
        } else {
            $CategoriasCriados[] = $Categoria;
        }
    }

    (new Response(
        success: true,
        message: 'Importação realizada com sucesso',
        data: [
            "CategoriasCriados" => $CategoriasCriados,
            "CategoriasNaoCriados" => $CategoriasNaoCriados,
        ],
        httpCode: 200
    ))->send();

    exit();
}
}
