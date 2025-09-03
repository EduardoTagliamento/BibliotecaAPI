<?php
namespace Firebase\JWT;

use FastRoute\RouteParser\Std;
use stdClass;
use Firebase\JWT\Key;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\ExpiredException;
use DomainException;
use Exception;
use InvalidArgumentException;
use UnexpectedValueException;

require_once "jwt/JWT.php";
require_once "jwt/Key.php";
require_once "jwt/SignatureInvalidException.php";
require_once "jwt/ExpiredException.php";

class MeuTokenJWT
{
    // Constantes estáticas
    private const KEY = "x9S4q0v+V0IjvHkG20uAxaHx1ijj+q1HWjHKv+ohxp/oK+77qyXkVj/l4QYHHTF3";
    private const ALGORITHM = 'HS256';
    private const TYPE = 'JWT';
    public function __construct(
        private stdClass $payload = new stdClass(),
        private string $iss = 'http://localhost',
        private string $aud = 'http://localhost',
        private string $sub = 'acesso_sistema',
        private int $duration = 3600 * 24 * 30 // 30 dias
    ) {
    }
    public function gerarToken(stdClass $claims): string
    {
        $objHeaders = new stdClass();
        $objHeaders->alg = MeuTokenJWT::ALGORITHM;
        $objHeaders->typ = MeuTokenJWT::TYPE;

        $objPayload = new stdClass();
        $objPayload->iss = $this->iss;
        $objPayload->aud = $this->aud;
        $objPayload->sub = $this->sub;
        $objPayload->iat = time();
        $objPayload->exp = time() + $this->duration;
        $objPayload->nbf = time();
        $objPayload->jti = bin2hex(random_bytes(16));

        // Public Claims
        $objPayload->public = new stdClass();
        $objPayload->public->name = $claims->name ;
        $objPayload->public->email = $claims->email ;
        $objPayload->public->role = $claims->role ;
        // Private Claims
        $objPayload->private = new stdClass();
        $objPayload->private->idFuncionario = $claims->idFuncionario;
        return JWT::encode(
            payload: (array) $objPayload,
            key: MeuTokenJWT::KEY,
            alg: MeuTokenJWT::ALGORITHM,
            keyId: null,
            head: (array) $objHeaders
        );
    }

    public function validateToken($stringToken): bool
    {

        if (empty($stringToken)) {
            return false;
        }

        //esse padrão verifica se o token tem o padrao "caracteres.caracteres.caracteres";
        $padrao = '/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/';
        if (!preg_match($padrao, $stringToken) === 1) {
            return false;
        }
        $token = str_replace(["Bearer ", " "], "", $stringToken);

        try {
            $payloadValido = JWT::decode(jwt: $token, keyOrKeyArray: new Key(keyMaterial: MeuTokenJWT::KEY, algorithm: MeuTokenJWT::ALGORITHM));
            $this->setPayload($payloadValido);
            return true;
        } catch (
            SignatureInvalidException |
            \Firebase\JWT\BeforeValidException |
            ExpiredException |
            InvalidArgumentException |
            DomainException |
            UnexpectedValueException |
            Exception $e
        ) {
            return false;
        }
    }

    public function getPayload(): stdClass|null
    {
        return $this->payload;
    }

    public function setPayload(stdClass $payload): self
    {
        $this->payload = $payload;
        return $this;
    }


}
