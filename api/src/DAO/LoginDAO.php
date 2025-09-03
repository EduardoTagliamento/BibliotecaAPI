<?php
require_once "api/src/models/Cargo.php";
require_once "api/src/db/Database.php";
require_once "api/src/utils/Logger.php";
/**
 *
 * @author      Hélio Esperidião
 * @copyright   Copyright (c) 2025 Hélio Esperidião
 * @license     GPL (GNU General Public License)
 * @website http://helioesperidiao.com
 * @github https://github.com/helioesperidiao
 * @linkedin https://www.linkedin.com/in/helioesperidiao/
 * @youtube https://www.youtube.com/c/HélioEsperidião
 */

class LoginDAO{

    public function verificarLogin(Funcionario $funcionario): array
    {
        $query = '  SELECT
                        idFuncionario,
                        nomeFuncionario,
                        email,
                        recebeValeTransporte,
                        idCargo,
                        nomeCargo
                    FROM funcionario 
                    JOIN cargo ON Cargo_idCargo = idCargo
                    WHERE 
                        email = :email AND
                        senha = md5(:senha)
                    ORDER BY nomeFuncionario ASC
                ';

        // Prepara a instrução SQL, protegendo contra SQL Injection
        $statement = Database::getConnection()->prepare(query: $query);

        $statement->bindValue(
            param: ':email',
            value: $funcionario->getEmail(),
            type: PDO::PARAM_STR
        );

        $statement->bindValue(
            param: ':senha',
            value: $funcionario->getSenha(),
            type: PDO::PARAM_STR
        );
        // Busca a única linha esperada da consulta como um objeto genérico (stdClass)
        $statement->execute();

        // Instancia um novo objeto Funcionario que será preenchido com os dados do banco
        $funcionario = new Funcionario();

        // Busca a única linha esperada da consulta como um objeto genérico (stdClass)
        $linha = $statement->fetch(mode: PDO::FETCH_OBJ);

        if (!$linha) {
               return []; // Retorna array vazio caso não encontre nenhum funcionário com esse idFuncionario
        }
        // Preenche os dados básicos do funcionário no objeto Funcionario
        $funcionario
            ->setIdFuncionario(idFuncionario: $linha->idFuncionario)                 // ID do funcionário
            ->setNomeFuncionario(nomeFuncionario: $linha->nomeFuncionario)           // Nome do funcionário
            ->setEmail(email: $linha->email);                                         // E-mail
           

        // Acessa o objeto Cargo dentro do Funcionario e preenche os dados do cargo
        $funcionario
            ->getCargo()
            ->setIdCargo(idCargo: $linha->idCargo)            // ID do cargo associado
            ->setNomeCargo(nomeCargo: $linha->nomeCargo);     // Nome do cargo

        // Retorna o objeto Funcionario completamente preenchido

    
        return [$funcionario];
    }


}
