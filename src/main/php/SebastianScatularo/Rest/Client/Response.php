<?php

namespace SebastianScatularo\Rest\Client;

/**
 * Response
 *
 * @author sebastianscatularo@gmail.com
 */
class Response
{

    private $payload;
    private $info;
    private $error;

    public static function newResponse($payload, $info, $error)
    {
        return new static($payload, $info, $error);
    }

    private function __construct($payload, $info, $error)
    {
        $this->payload = $payload;
        $this->info = $info;
        $this->error = $error;
    }

    public function getContentType()
    {
        return $this->info->content_type;
    }

    public function getPayload()
    {
        return $this->payload;
    }

}
