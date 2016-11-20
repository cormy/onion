<?php

namespace Cormy\Server\Helpers;

use Zend\Diactoros\Response\TextResponse;

class Response extends TextResponse
{
    public function __construct(string $text, $status = 200, array $headers = [])
    {
        parent::__construct('', $status, $headers);
        $this->getBody()->write($text);
    }
}
