<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = 'plantifyinvernadero@gmail.com';
    public string $fromName   = 'Plantify - Invernadero Inteligente';
    public $SMTPHost  = 'smtp.gmail.com';
    public $SMTPUser  = 'plantifyinvernadero@gmail.com';
    public $SMTPPass  = 'jezs umhe agvv gbyp';
    public $SMTPPort  = 587;
    public $SMTPCrypto = 'tls';
    public $mailType  = 'html';
}
