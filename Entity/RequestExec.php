<?php

namespace Vsavritsky\MorpherBundle\Entity;

use Anchovy\CURLBundle\Exception\CurlException;

class RequestExec
{
    private $curl;
    private $username;
    private $pass;

    public function __construct($curl, $username = null, $pass = null)
    {
        $this->curl = $curl;
        $this->username = $username;
        $this->pass = $pass;
    }

    public function exec($url)
    {
        $options = array();
        if (!empty($this->username) && $this->pass) {
            $options = array(
                'CURLOPT_USERPWD' => $this->username.':'.$this->pass,
                'CURLOPT_HTTPAUTH' => CURLAUTH_BASIC,
            );
        }

        try {
            $return = $this->curl->setURL($url)->setOptions($options)->execute();
        } catch (CurlException $e) {
            return [];
        }

        return $this->parse($return);
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