<?php

namespace App\Exceptions\Custom;

use Illuminate\Http\Response;

class UnprocessableException extends \RuntimeException {

    private $statusCode;

    public function __construct(string $message = '', \Throwable $previous = null) {
        $this->statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

        parent::__construct($message, $this->statusCode, $previous);
    }

    public function getStatusCode() {
        return $this->statusCode;
    }
}
