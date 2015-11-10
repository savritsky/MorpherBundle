<?php

namespace Vsavritsky\MorpherBundle\Entity;

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

        $return = $this->curl->setURL($url)->setOptions($options)->execute();

        return $this->parse($return);
    }

    private function parse($xmlstring)
    {
        $xml = simplexml_load_string($xmlstring);
        $json = json_encode($xml);
        $xmlArray = json_decode($json,TRUE);
        return $xmlArray;
    }
}