<?php
require_once "api/src/DAO/LoginDAO.php";
require_once "api/src/http/Response.php";
require_once "api/src/utils/Logger.php";
require_once "api/src/utils/MeuTokenJWT.php";

use Firebase\JWT\MeuTokenJWT;
/**
 * Classe [LoginControl]

 *
 * @author      Hélio Esperidião
 * @copyright   Copyright (c) 2025 Hélio Esperidião
 * @license     GPL (GNU General Public License)
 * @website http://helioesperidiao.com
 * @github https://github.com/helioesperidiao
 * @linkedin https://www.linkedin.com/in/helioesperidiao/
 * @youtube https://www.youtube.com/c/HélioEsperidião
 */


class LoginControl
{

    public function autenticar(stdClass $stdLogin): never
    {
        // Cria uma instância do DAO para acessar os dados no banco
        $loginDAO = new LoginDAO();

        $funcionario = new Funcionario();


        $funcionario->setEmail($stdLogin->funcionario->email);
        $funcionario->setSenha($stdLogin->funcionario->senha);

        // Obtém todos os cargos cadastrados
        $funcionarioLogado = $loginDAO->verificarLogin($funcionario);

        if (empty($funcionarioLogado)) {
            // Envia a resposta JSON com os dados encontrados
            (new Response(
                success: false,
                message: 'Usuário e senha inválidos',

                httpCode: 401
            ))->send();
        } else {
            // echo json_encode($funcionarioLogado  );
            // echo $funcionarioLogado[0]->getCargo()->getNomeCargo();
            //exit;

            $claims = new stdClass();

            $claims->name = $funcionarioLogado[0]->getNomeFuncionario();
            $claims->email = $funcionarioLogado[0]->getEmail();
            $claims->role = $funcionarioLogado[0]->getCargo()->getNomeCargo();
            $claims->idFuncionario = $funcionarioLogado[0]->getIdFuncionario();


            $meuToken = new MeuTokenJWT();

            $token = $meuToken->gerarToken($claims);

            (new Response(
                success: true,
                message: 'Usuário e senha validados',
                data: [
                    'token' => $token,
                    'funcionario' => $funcionarioLogado
                ],

                httpCode: 200
            ))->send();
        }




        // Encerra a execução do script
        exit();
    }





}
