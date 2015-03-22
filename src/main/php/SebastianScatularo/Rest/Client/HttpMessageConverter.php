<?php

namespace SebastianScatularo\Rest\Client;

/**
 *
 * @author sebastianscatularo@gmail.com
 */
interface HttpMessageConverter
{
    public function getSupportMediaType();
    public function read();
    public function write();
}
