<?php

namespace DOKU;

use DOKU\Service\VirtualAccount;

use DOKU\Service\MandiriVa;

use DOKU\Service\DokuVa;

use DOKU\Service\BcaVa;

use DOKU\Service\BsiVa;

use DOKU\Service\GetStatus;

class Client
{
    /**
     * @var array
     */
    private $config = array();

    public function isProduction($value)
    {
        $this->config['environment'] = env('DOKU_ENV', $value);
    }

    public function setClientID($clientID)
    {
        $this->config['client_id'] = env('DOKU_CLIENT', $clientID);
    }

    public function setSharedKey($key)
    {
        $this->config['shared_key'] = env('DOKU_SECRET', $key);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function generateMandiriVa($params)
    {
        $this->config = $this->getConfig();
        return MandiriVa::generated($this->config, $params);
    }

    public function generateDokuVa($params)
    {
        $this->config = $this->getConfig();
        return DokuVa::generated($this->config, $params);
    }

    public function generateBsiVa($params)
    {
        $this->config = $this->getConfig();
        return BsiVa::generated($this->config, $params);
    }

    public function generateBcaVa($params)
    {
        $this->config = $this->getConfig();
        return BcaVa::generated($this->config, $params);
    }

    public function checkStatus($request_id)
    {
        $this->config = $this->getConfig();
        return GetStatus::statused($this->config, $request_id);
    }
}
