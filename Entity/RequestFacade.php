<?php

namespace Vsavritsky\MorpherBundle\Entity;

use Doctrine\ORM\EntityManager;

class RequestFacade
{
    private $em;
    private $repository;
    private $requestExec;

    const BASE_URL = 'http://api.morpher.ru/WebService.asmx';
    const INFLECT = '/GetXml?s=';
    const REQUEST_INFLECT_TYPE = 'inflect';

    public function __construct(EntityManager $entityManager, RequestExec $requestExec)
    {
        $this->em = $entityManager;
        $this->repository = $entityManager->getRepository('VsavritskyMorpherBundle:Request');
        $this->requestExec = $requestExec;
    }

    public function inflect($word)
    {
        return $this->getResult(self::REQUEST_INFLECT_TYPE, $word);
    }

    private function getResult($type, $word)
    {
        $result = $this->getCache($type, $word);
        if (!empty($result)) {
            return $result;
        }

        $url = $this->getUrlByType($type, $word);
        $result = $this->requestExec->exec($url);

        if (!$this->errorRequest($result)) {
            $this->saveCache($type, $word, $result);
        }

        return $result;
    }

    private function errorRequest($result)
    {
        if (isset($result['code'])) {
            return true;
        }

        return false;
    }

    private function getPublicType()
    {
        return [self::REQUEST_INFLECT_TYPE];
    }

    private function getUrlByType($type, $word)
    {
        if (empty($type) || empty($word) || !in_array($type, $this->getPublicType())) {
            throw new Exception('error params getUrlByType');
        }

        $url = '';
        switch ($type) {
            case self::REQUEST_INFLECT_TYPE:
                $url = self::BASE_URL.self::INFLECT.$word;
                break;
        }

        return $url;
    }

    private function getCache($type, $word)
    {
        if (empty($type) || empty($word)) {
            throw new Exception('error params getCache');
        }

        $request = $this->repository->findOneBy(['type' => $type, 'word' => $word]);
        if (empty($request)) {
            return;
        }
        return $request->getResult();
    }

    private function saveCache($type, $word, array $result)
    {
        if (empty($type) || empty($word) || empty($result)) {
            throw new Exception('error params saveCache');
        }

        $request = new Request();
        $request->setType($type);
        $request->setWord($word);
        $request->setResult($result);
        $this->em->persist($request);
        $this->em->flush();

        return $request->getResult();
    }

    private function getData($result)
    {
        return $result;
    }
}