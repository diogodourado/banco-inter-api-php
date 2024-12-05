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

    function getBearerToken()
    {
        if (!file_exists($this->tokenPath) || file_exists($this->tokenPath) && (time() - filemtime($this->tokenPath) > 3000))
            return $this->generateBearerToken();

        return file_get_contents($this->tokenPath);
    }

    function generateBearerToken()
    {

        $response = $this->request('POST', 'oauth/v2/token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope' => $this->scope,
            'grant_type' => 'client_credentials',
        ], true);

        file_put_contents($this->tokenPath, $response['access_token']);

        return $response['access_token'];
    }

    function request(string $method, string $endpoint, array $data = [], bool $noAuth = false)
    {
        if ($noAuth === false)
            $BearerToken = $this->getBearerToken();

        $ch = curl_init();
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $method = strtoupper($method);

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_SSLCERT => $this->certPath,
            CURLOPT_SSLKEY => $this->keyPath,
            CURLOPT_RETURNTRANSFER => true,
        ]);


        $headers = $noAuth === true ? array('Content-Type: application/x-www-form-urlencoded') : array('Authorization: Bearer ' . $BearerToken, 'x-conta-corrente: ' . $this->contaCorrente, 'Content-Type: application/json');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $noAuth === true ? http_build_query($data) : json_encode($data));
        } elseif (in_array($method, ['PUT', 'DELETE'], true)) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $method === 'DELETE' ? http_build_query($data) : json_encode($data));
        } elseif ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
            curl_setopt($ch, CURLOPT_URL, $url);
        }

        $serverResponse = curl_exec($ch);

        if ($serverResponse === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("Erro cURL: $error");
        }

        curl_close($ch);

        $response = json_decode($serverResponse, true);

        if ($serverResponse != '' && (json_last_error() !== JSON_ERROR_NONE)) {
            $serverResponseJson = json_encode($serverResponse);
            throw new Exception("Erro ao decodificar JSON: " . json_last_error_msg() . " - Mensagem recebida:" . $serverResponseJson);
        }

        return $response;
    }

    function webhookGet()
    {
        $result = $this->request('GET', 'cobranca/v3/cobrancas/webhook');

        if ($result !== NULL && !isset($result['webhookUrl']))
            throw new Exception("Erro ao recuperar webhook cadastrado: " . json_encode($result));

        return $result;
    }

    function webhookSet($url)
    {
        $result = $this->request('PUT', 'cobranca/v3/cobrancas/webhook', ['webhookUrl' => $url]);

        if (is_array($result))
            throw new Exception("Erro ao cadastrar webhook: " . json_encode($result));

        return true;
    }

    function webhookDelete()
    {
        $result = $this->request('DELETE', 'cobranca/v3/cobrancas/webhook');

        if (is_array($result))
            throw new Exception("Erro ao deletar webhook: " . json_encode($result));

        return true;
    }


    function webhookCallbacks($params)
    {
        $result = $this->request('GET', 'cobranca/v3/cobrancas/webhook/callbacks', $params);

        if (!isset($result['totalElementos']))
            throw new Exception("Erro ao recuperar webhook cadastrado: " . json_encode($result));

        return $result;
    }
}
