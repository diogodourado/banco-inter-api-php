<?php

class BancoInter
{

    private $contaCorrente, $clientId, $clientSecret, $certPath, $keyPath, $tokenPath, $baseUrl, $scope, $cpfCnpj;

    public function __construct(string $type = '')
    {
        $pathFiles = '/home/account/keys/';

        $this->clientId = '5';
        $this->clientSecret = '';

        $this->certPath = $pathFiles . 'your-file.crt';
        $this->keyPath = $pathFiles . 'your-file.key';
        $this->tokenPath = $pathFiles . 'bearer.token';

        $this->cpfCnpj = '123.123.0001/09';
        $this->contaCorrente = '1234567';

        $this->baseUrl = 'https://cdpj.partners.bancointer.com.br';
        $this->scope = 'boleto-cobranca.read boleto-cobranca.write';
    }
}
