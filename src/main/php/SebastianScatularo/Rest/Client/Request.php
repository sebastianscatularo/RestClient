<?php

namespace SebastianScatularo\Rest\Client;

/**
 * Request
 *
 * @author sebastianscatularo@gmail.com
 */

class Request
{
    private $options = array();
    private $payload;
    
    public static function newRequest($payload, $header) {
        return new static($payload, $header);
    }
    
    public function __construct($payload, $headers)
    {
        $this->payload = $payload;
        foreach ($headers as $header => $value) {
            $this->addHeader($header, $value);
        }
    }
    public function addHeader($header, $value) {
        $this->options[CURLOPT_HTTPHEADER][] = $header . ': ' . $value;
    }

    public function addOption($option, $value) {
        $this->options[$option] = $value;
    }
    
    public function getOptions() {
        return $this->options;
    }
    
    public function getPayload() {
        return $this->payload;
    }
}
