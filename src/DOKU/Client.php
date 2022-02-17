<?php

namespace DOKU;

use DOKU\Service\VirtualAccount;

use DOKU\Service\CreditCard;

use DOKU\Service\MandiriVa;

use DOKU\Service\PermataVa;

use DOKU\Service\AlfaO2O;

use DOKU\Service\IndomaretO2O;

use DOKU\Service\CimbVa;

use DOKU\Service\DokuVa;

use DOKU\Service\BcaVa;

use DOKU\Service\BsiVa;

use DOKU\Service\BriVa;

use DOKU\Service\BniVa;

use DOKU\Service\GetStatus;

use DOKU\Service\Cancel;

class Client
{
    /**
     * @var array
     */
    private $config = array();

    public function isProduction($value = null)
    {
        $this->config['environment'] = $value ?? env('DOKU_ENV', $value);
    }

    public function setClientID($clientID = null)
    {
        $this->config['client_id'] = $clientID ?? env('DOKU_CLIENT', $clientID);
    }

    public function setSharedKey($key = null)
    {
        $this->config['shared_key'] = $key ?? env('DOKU_SECRET', $key);
    }

    public function getConfig()
    {
        $this->config['environment'] = $this->config['environment'] ?? env('DOKU_ENV', '');
        $this->config['client_id'] = $this->config['client_id'] ?? env('DOKU_CLIENT', '');
        $this->config['shared_key'] = $this->config['shared_key'] ?? env('DOKU_SECRET', '');
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

    public function generateBriVa($params)
    {
        $this->config = $this->getConfig();
        return BriVa::generated($this->config, $params);
    }

    public function generateBniVa($params)
    {
        $this->config = $this->getConfig();
        return BniVa::generated($this->config, $params);
    }

    public function generateCimbVa($params)
    {
        $this->config = $this->getConfig();
        return CimbVa::generated($this->config, $params);
    }

    public function generatePermataVa($params)
    {
        $this->config = $this->getConfig();
        return PermataVa::generated($this->config, $params);
    }

    public function generateAlfaO2O($params)
    {
        $this->config = $this->getConfig();
        return AlfaO2O::generated($this->config, $params);
    }

    public function generateIndomaretO2O($params)
    {
        $this->config = $this->getConfig();
        return IndomaretO2O::generated($this->config, $params);
    }

    public function generateCreditCard($params)
    {
        $this->config = $this->getConfig();
        return CreditCard::generated($this->config, $params);
    }

    public function checkStatus($request_id)
    {
        $this->config = $this->getConfig();
        return GetStatus::statused($this->config, $request_id);
    }

    public function cancelPayment($params)
    {
        $this->config = $this->getConfig();
        return Cancel::cancelation($this->config, $params);
    }
}
