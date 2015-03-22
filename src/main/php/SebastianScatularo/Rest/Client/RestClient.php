<?php

namespace SebastianScatularo\Rest\Client;

/**
 * RestClient
 *
 * @author sebastianscatularo@gmail.com
 */
class RestClient
{

    private $converters = array();

    public static function newRestClient()
    {
        return new static();
    }

    private function __construct()
    {
        if (!Utils::hasCurlSupport()) {
            throw new CurlException("Must have installed cURL extension");
        }
    }

    public function post($url, $payload, $header = array())
    {
        return $this->execute($url, CURL::POST, $payload, $header);
    }

    public function get($url, $header = array())
    {
        return $this->execute($url, CURL::GET, NULL, $header);
    }

    public function put($url, $payload, $header)
    {
        return $this->execute($url, CURL::PUT, $payload, $header);
    }

    public function delete($url, $header)
    {
        return $this->execute($url, CURL::DELETE, NULL, $header);
    }

    private function execute($endpoint, $method, $payload, $headers = array())
    {
        $curl = CURL::newCURL($endpoint, $method);
        $request = Request::newRequest($payload, $headers);
        $response = $curl->send($request);
        $object = $this->convert($response);
        unset($curl);
        return $object;
    }

    public function addMessageConverter(HttpMessageConverter $converter)
    {
        $this->converters[] = $converter;
    }

    private function convert(Response $response)
    {
        foreach ($this->converters as $converter) {
            if ($converter->support($response->getContentType())) {
                return $converter->write($response->getPayload());
            }
            throw new \UnknowConverter($response->getContentType());
        }
        return $response;
    }

}

class CURL
{

    const POST = "POST";
    const GET = "GET";
    const PUT = "PUT";
    const DELETE = "DELETE";

    private $handler;
    private $method;
    private $options = array();

    public static function newCURL($endpoint, $method)
    {
        return new static($endpoint, $method);
    }

    private function __construct($endpoint, $method)
    {
        $this->handler = curl_init($endpoint);
        $this->method = $method;
        $this->addOption(CURLOPT_USERAGENT, "Simple REST Client");
        $this->addOption(CURLOPT_RETURNTRANSFER, true);
        $this->addOption(CURLOPT_HEADER, false);
    }

    public function send(Request $request)
    {
        $this->setOptions($request->getOptions());
        switch ($this->method) {
            case CURL::DELETE:
                $this->addOption(CURLOPT_CUSTOMREQUEST, CURL::DELETE);
                break;
            case CURL::PUT:
                $this->addOption(CURLOPT_PUT, true);
                $this->addOption(CURLOPT_POST, $request->getPayload());
                break;
            case CURL::POST:
                /**
                 * curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                 * curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);        
                 * curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                 * 'Content-Type: application/json',
                 * 'Content-Length: ' . strlen($data_string))                                                                       
                 * );                                                                                                                    
                 */
                $this->addOption(CURLOPT_CUSTOMREQUEST, CURL::POST);
                $this->addOption(CURLOPT_POSTFIELDS, $request->getPayload());
                $this->addOption(CURLOPT_HTTPHEADER, array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($request->getPayload())
                ));
                break;
        }
        curl_setopt_array($this->handler, $this->getOptions());
        $response = Response::newResponse(
                        curl_exec($this->handler), curl_getinfo($this->handler), curl_error($this->handler)
        );
        return $response;
    }

    private function addOption($option, $value)
    {
        $this->options[$option] = $value;
    }

    private function setOptions($options = array())
    {
        /**
         * This allow over write default values;
         */
        array_merge($this->options, $options);
    }

    private function getOptions()
    {
        return $this->options;
    }

    public function __destruct()
    {
        curl_close($this->handler);
        unset($this->handler);
        unset($this->method);
        unset($this->options);
    }

}

class CURLException extends \ErrorException
{
    
}
