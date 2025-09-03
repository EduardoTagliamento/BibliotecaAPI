<?php

declare(strict_types=1);

class Response implements JsonSerializable
{
    public function __construct(
        private bool $success = true,
        private ?string $message = null,
        private ?array $data = null,
        private ?array $error = null,
        private int $httpCode = 200
    ) {

    }

    public function jsonSerialize(): array
    {
        $response = [];
        $response['success'] = $this->success;

        if (!empty($this->message)) {
            $response['message'] = $this->message;
        }

        if (!empty($this->data)) {
            $response['data'] = $this->data;
        }

        if (!empty($this->error)) {
            $response['error'] = $this->error;
        }

        return $response;
    }

    public function send(): never
    {
        header(header: "Content-Type: application/json");
        http_response_code(response_code: $this->httpCode);
        echo json_encode(value: $this);
        exit();
    }
}