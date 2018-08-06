<?php

namespace Vsavritsky\MorpherBundle\Entity;

use \GuzzleHttp\Client;
use \Exception;

class RequestExec
{
    private $client;
    private $username;
    private $pass;

    public function __construct($username = null, $pass = null)
    {
		$this->client = new \GuzzleHttp\Client();
        $this->username = $username;
        $this->pass = $pass;
    }

    public function exec($url)
    {
        $options = array();
        if (!empty($this->username) && $this->pass) {
            $options = ['auth' => [$this->username, $this->pass]];
        }

        try {
			$return = $this->client->request('GET', $url, $options);
			return $this->parse($return->getBody());
        } catch (Exception $e) {
            return [];
        }

        return false;
    }

    private function parse($xmlstring)
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlstring);
        if ($xml === false) {
            return [];
        }
        $json = json_encode($xml);
        $xmlArray = json_decode($json,TRUE);
        return $xmlArray;
    }
}
