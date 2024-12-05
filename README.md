# Banco Inter API - PHP

Uma implementação em PHP para integrar com a API do Banco Inter, permitindo a realização de operações financeiras como geração de boletos, consultas e transferências.

## 📋 Funcionalidades

- Suporte a autenticação via token.
- Geração, consulta e cancelamento de cobranças (Boleto/PIX).
- Geração, consulta e cancelamento de webhook.

### Referência Oficial da API

Para configurar cobranças via Boleto ou PIX, consulte a [documentação oficial do Banco Inter](https://developers.inter.co/references/cobranca-bolepix). Lá você encontrará os parâmetros necessários, exemplos de requisições e explicações sobre as respostas e erros. 

## 📦 Instalação

1. Clone o repositório:
   ```bash
   git clone https://github.com/diogodourado/banco-inter-api-php.git
   ```
2. Inclua o arquivo `BancoInter.class.php` no seu projeto.

## ⚙️ Configuração

Certifique-se de configurar os seguintes parâmetros antes de utilizar a classe:

- **Certificado digital (arquivo `.crt` e chave privada `.key`)**: Necessários para autenticação na API.
- **Credenciais de API**: Incluem `client_id` e `client_secret`, obtidos no painel de desenvolvedores do Banco Inter.

## 🚀 Exemplos de Uso
```php
require 'BancoInter.class.php';
$bancoInter = new BancoInter();
```

### Geração de cobrança (Boleto/PIX)
```php
$params = [
    "seuNumero" => "1",
    "valorNominal" => 2.5,
    "dataVencimento" => '2025-01-18',
    "numDiasAgenda" => 60,
    "pagador" => [
        "email" => "nome.sobrenome@xis-domain.com.br",
        "ddd" => "38",
        "telefone" => "999999999",
        "numero" => "3456",
        "complemento" => "apartamento 3 bloco 4",
        "cpfCnpj" => "11122233344",
        "tipoPessoa" => "FISICA",
        "nome" => "Diogo Dourado",
        "endereco" => "Avenida da Felicidad, 123456",
        "bairro" => "Centro",
        "cidade" => "Montes Claros",
        "uf" => "MG",
        "cep" => "39400000"
    ]
];
$codigoSolicitacao = $bancoInter->cobrancaSet($params);
print_r($codigoSolicitacao);
```

### Consulta cobrança (Boleto/PIX)
```php
$cobranca = $bancoInter->cobrancaGet($codigoSolicitacao);
print_r($$cobranca);
```

### Recuperando cobrança em PDF (Boleto/PIX)
```php
$pdf_base64 = $bancoInter->cobrancaGetPdf($codigoSolicitacao);
```

### Cancelando cobrança (Boleto/PIX)
```php
$result = $bancoInter->cobrancaCancel($codigoSolicitacao, 'Motivo do cancelamento aqui.');
print_r($result);
```

### Listar cobranças (Boleto/PIX)
```php
$params = [
    'dataInicial' => '2024-12-01',
    'dataFinal' => '2024-12-20',
    'filtrarDataPor' => 'EMISSAO',
    'situacao' => NULL,
    'pessoaPagadora' => NULL,
    'cpfCnpjPessoaPagadora' => NULL,
    'seuNumero' => NULL,
    'paginacao' => NULL,
    'ordenarPor' => NULL,
    'tipoOrdenacao' => NULL,
];
$codigoSolicitacao = $bancoInter->cobrancaList($params);
print_r($codigoSolicitacao);
```

## 📝 Licença

Este projeto está licenciado sob a [MIT License](LICENSE).


## 💰 Contribua com o Desenvolvimento

Se este código foi útil para você e deseja contribuir como forma de agradecimento, pode enviar qualquer valor para meu PIX: **diogo@dourado.net**. Toda contribuição é muito bem-vinda! 🎉
