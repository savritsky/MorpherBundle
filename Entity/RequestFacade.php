<?php

namespace Vsavritsky\MorpherBundle\Entity;

use Doctrine\ORM\EntityManager;
use Vsavritsky\MorpherBundle\Exception\ExpectedParameter;
use Vsavritsky\MorpherBundle\Exception\UnknownCaseWord;
use Vsavritsky\MorpherBundle\Exception\UrlTypeIncorrect;

class RequestFacade
{
    private $em;
    private $repository;
    private $requestExec;

    const BASE_URL = 'http://api.morpher.ru/WebService.asmx';
    const INFLECT = '/GetXml?s=';
    const LIMIT = '/GetDailyQueryLimit';

    const REQUEST_INFLECT_TYPE = 'inflect';
    const REQUEST_LIMIT_TYPE = 'limit';

    const CASE_IM = 'И'; // actual for pluralization requests.
    const CASE_ROD = 'Р';
    const CASE_DAT = 'Д';
    const CASE_VIN = 'В';
    const CASE_TVOR = 'Т';
    const CASE_PREDL = 'П';
    const CASE_GDE = 'где';
    const PLURAL = 'множественное';

    /**
     * RequestFacade constructor.
     *
     * @param EntityManager $entityManager
     * @param RequestExec $requestExec
     */
    public function __construct(EntityManager $entityManager, RequestExec $requestExec)
    {
        $this->em = $entityManager;
        $this->repository = $entityManager->getRepository('VsavritskyMorpherBundle:Request');
        $this->requestExec = $requestExec;
    }

    /**
     * @param string $word
     * @param string $case One of the RequestFacade::CASE_* constants.
     * @param string|null $default
     * @param bool|false $plural
     * @return string
     */
    public function inflect($word, $case, $default = null, $plural = false)
    {
        if (! in_array($case, array(
            self::CASE_IM,
            self::CASE_ROD,
            self::CASE_DAT,
            self::CASE_VIN,
            self::CASE_TVOR,
            self::CASE_PREDL,
            self::CASE_GDE))
        ) {
            throw new UnknownCaseWord();
        }

        $result = $this->getResultWithCache(self::REQUEST_INFLECT_TYPE, $word);

        if (is_array($result)) {
            if ($plural && isset($result[self::PLURAL]) && isset($result[self::PLURAL][$case])) {
                return $result[self::PLURAL][$case];

            } elseif (isset($result[$case])) {
                return $result[$case];
            }
        }

        return $default;
    }

    public function getLimit()
    {
        return $this->getResultLimit(self::REQUEST_LIMIT_TYPE);
    }

    private function getResultWithCache($type, $word)
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

    private function getResultLimit($type)
    {
        $url = $this->getUrlByType($type);
        $result = $this->requestExec->exec($url);

        return $result;
    }

    private function getPublicType()
    {
        return [self::REQUEST_INFLECT_TYPE, self::REQUEST_LIMIT_TYPE];
    }

    private function getUrlByType($type, $word = '')
    {
        if (empty($type) || !in_array($type, $this->getPublicType())) {
            throw new UrlTypeIncorrect('error params getUrlByType');
        }

        $url = '';
        switch ($type) {
            case self::REQUEST_INFLECT_TYPE:
                $url = self::BASE_URL.self::INFLECT.urlencode($word);
                break;
            case self::REQUEST_LIMIT_TYPE:
                $url = self::BASE_URL.self::LIMIT;
                break;
        }

        return $url;
    }

    private function getCache($type, $word)
    {
        if (empty($type) || empty($word)) {
            throw new ExpectedParameter('error params getCache');
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
            throw new ExpectedParameter('error params saveCache');
        }

        $request = new Request();
        $request->setType($type);
        $request->setWord($word);
        $request->setResult($result);
        $this->em->persist($request);
        $this->em->flush();

        return $request->getResult();
    }

    private function errorRequest($result)
    {
        /*
        1	Превышен лимит на количество запросов в сутки. Перейдите на следующий тарифный план.
        2	Превышен лимит на количество одинаковых запросов в сутки. Реализуйте кеширование.
        3	IP заблокирован.
        4	Склонение числительных в GetXml не поддерживается. Используйте метод Propis.
        5	Не найдено русских слов.
        6	Не указан обязательный параметр s.
        7	Необходимо оплатить услугу.
        8	Пользователь с таким ID не зарегистрирован.
        9	Неправильное имя пользователя или пароль.
        */
        if (empty($result)) {
            return true;
        }

        if (isset($result['code']) && isset($result['error'])) {
            return true;
            /*switch ($result['code']) {
                case 1:
                    throw new ExceededLimitRequests();
                    break;
                case 2:
                    throw new ExceededLimitRequests();
                    break;
                case 3:
                    throw new IpBlocked();
                    break;
                case 4:
                    throw new MethodError();
                    break;
                case 5:
                    throw new ExpectedRussianWord();
                    break;
                case 6:
                    throw new ExpectedParameter();
                    break;
                case 7:
                    throw new ServiceNotPaid();
                    break;
                case 8:
                    throw new UserNotFound();
                    break;
                case 9:
                    throw new AuthorisationError();
                    break;
            }
            return true;*/
        }

        return false;
    }
}
