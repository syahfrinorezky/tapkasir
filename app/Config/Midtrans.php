<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Midtrans extends BaseConfig
{
    public $serverKey;
    public $clientKey;
    public $isProduction = false;
    public $isSanitized  = true;
    public $is3ds        = true;

    public function __construct()
    {
        parent::__construct();
        $this->serverKey    = getenv('MIDTRANS_SERVER_KEY');
        $this->clientKey    = getenv('MIDTRANS_CLIENT_KEY');
        $this->isProduction = filter_var(getenv('MIDTRANS_IS_PRODUCTION'), FILTER_VALIDATE_BOOLEAN);
    }
}
