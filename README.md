# Swoole PHP - Programação Assíncrona em PHP

Este projeto foi desenvolvido para explorar e praticar o uso do **OpenSwoole** em PHP, demonstrando conceitos fundamentais de programação assíncrona, corrotinas, hooks e desenvolvimento de aplicações web de alta performance.

## 📚 Índice

- [O que é Swoole?](#o-que-é-swoole)
- [Por que usar Swoole?](#por-que-usar-swoole)
- [Conceitos Fundamentais](#conceitos-fundamentais)
  - [Programação Assíncrona](#programação-assíncrona)
  - [Corrotinas (Coroutines)](#corrotinas-coroutines)
  - [Channels](#channels)
  - [Hooks](#hooks)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Instalação e Configuração](#instalação-e-configuração)
- [Exemplos Práticos](#exemplos-práticos)
- [Como Executar](#como-executar)

## O que é Swoole?

**Swoole** (agora conhecido como **OpenSwoole**) é uma extensão assíncrona e orientada a eventos para PHP que permite a criação de aplicações de alta performance. Desenvolvido em C++, o Swoole traz funcionalidades avançadas para o PHP tradicionalmente síncronо, incluindo:

- **Servidor HTTP assíncrono**
- **Corrotinas (Coroutines)**
- **Clientes HTTP/TCP/UDP assíncronos**
- **WebSockets**
- **Programação concorrente**

## Por que usar Swoole?

### Problemas do PHP Tradicional

No PHP tradicional (síncrono), cada requisição bloqueia a execução até sua conclusão:

```php
// PHP Tradicional - Bloqueante
$data1 = file_get_contents('http://api1.com/data'); // Espera 2 segundos
$data2 = file_get_contents('http://api2.com/data'); // Espera mais 3 segundos
// Total: 5 segundos para completar ambas as operações
```

### Vantagens do Swoole

Com Swoole, você pode executar operações de forma concorrente:

```php
// Com Swoole - Não bloqueante
go(function () {
    $data1 = file_get_contents('http://api1.com/data'); // 2 segundos
});

go(function () {
    $data2 = file_get_contents('http://api2.com/data'); // 3 segundos
});
// Total: 3 segundos (execução em paralelo)
```

## Conceitos Fundamentais

### Programação Assíncrona

A programação assíncrona permite que múltiplas operações sejam executadas sem bloquear o thread principal. Em vez de esperar uma operação I/O (como leitura de arquivo ou requisição HTTP) terminar, o programa pode continuar executando outras tarefas.

**Exemplo prático do projeto:**

```php
// arquivo: coroutines.php
<?php

require_once __DIR__ . '/vendor/autoload.php';

co::run(function () {
    go(function () {
        co::sleep(2);
        echo 'Show after 2 seconds' . PHP_EOL;
    });

    go(function () {
        co::sleep(1);
        echo 'Show after 1 second' . PHP_EOL;
    });
});
```

**Resultado:**
```
Show after 1 second
Show after 2 seconds
```

Note que a segunda corrotina (1 segundo) executa antes da primeira (2 segundos), mesmo sendo definida depois.

### Corrotinas (Coroutines)

Corrotinas são funções que podem ser pausadas e retomadas. No Swoole, elas são a base da programação assíncrona.

**Características das Corrotinas:**
- **Leves**: Milhares podem ser criadas sem overhead significativo
- **Não-bloqueantes**: Uma corrotina pode pausar sem bloquear outras
- **Cooperativas**: Cedem controle voluntariamente

**Exemplo de criação de corrotinas:**

```php
// Usando go() para criar uma corrotina
go(function () {
    echo "Esta é uma corrotina\n";
    co::sleep(1); // Pausa por 1 segundo sem bloquear
    echo "Corrotina resumida\n";
});

// Usando co::run() para executar um bloco de corrotinas
co::run(function () {
    for ($i = 0; $i < 3; $i++) {
        go(function () use ($i) {
            co::sleep($i);
            echo "Corrotina $i finalizada\n";
        });
    }
});
```

### Channels

Channels são estruturas de dados que permitem comunicação entre corrotinas de forma segura.

**Exemplo do projeto (`http-server.php`):**

```php
$server->on('request', function (Request $request, Response $response) {
    $channelSize = 2;
    $channel     = new chan($channelSize);

    // Primeira corrotina - faz requisição HTTP
    go(function () use ($channel) {
        $client = new Client('localhost', 8001);
        $client->get('/server.php');
        $body = $client->getBody();
        $channel->push($body); // Envia dados para o channel
    });

    // Segunda corrotina - lê arquivo
    go(function () use ($channel) {
        $content = file_get_contents(__DIR__ . '/file.txt');
        $channel->push($content); // Envia dados para o channel
    });

    // Terceira corrotina - coleta resultados
    go(function () use ($channel, &$response) {
       $firstResponse  = $channel->pop();  // Recebe primeiro resultado
       $secondResponse = $channel->pop();  // Recebe segundo resultado

       $response->end("$firstResponse $secondResponse");
    });
});
```

### Hooks

Hooks permitem que funções síncronas do PHP sejam automaticamente convertidas em versões assíncronas.

**Sem Hooks (síncronо):**
```php
$data = file_get_contents('http://api.com'); // Bloqueia a execução
```

**Com Hooks (assíncrono):**
```php
Co::set(['hook_flags' => OpenSwoole\Runtime::HOOK_ALL]);

go(function () {
    $data = file_get_contents('http://api.com'); // Não bloqueia!
});
```

**Exemplo do projeto (`server-with-hooks.php`):**

```php
<?php

use OpenSwoole\Http\Server;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;

// Habilita todos os hooks do Swoole
Co::set(['hook_flags' => OpenSwoole\Runtime::HOOK_ALL]);

$server = new Server('0.0.0.0', 8080);

$server->on('request', function (Request $request, Response $response) {
    $channel = new chan(2);

    go(function () use ($channel) {
        // cURL agora é assíncrono graças aos hooks
        $curl = curl_init('http://localhost:8001/server.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $body = curl_exec($curl);
        $channel->push($body);
    });

    go(function () use ($channel) {
        // file_get_contents também é assíncrono
        $content = file_get_contents(__DIR__ . '/file.txt');
        $channel->push($content);
    });

    go(function () use ($channel, &$response) {
        $firstResponse  = $channel->pop();
        $secondResponse = $channel->pop();
        $response->end("$firstResponse $secondResponse");
    });
});

$server->start();
```

**Tipos de Hooks disponíveis:**
- `HOOK_FILE`: Operações de arquivo
- `HOOK_CURL`: Requisições HTTP com cURL
- `HOOK_STREAM_FUNCTION`: Funções de stream
- `HOOK_SLEEP`: Função sleep()
- `HOOK_ALL`: Todos os hooks disponíveis

## Estrutura do Projeto

```
swoole-php/
├── public/
│   └── swoole.php              # Servidor HTTP principal com MVC
├── src/
│   ├── Controller/             # Controllers da aplicação
│   ├── Entity/                 # Entidades Doctrine
│   ├── Helper/                 # Helpers e traits
│   └── Infra/                  # Infraestrutura (Entity Manager)
├── config/
│   ├── dependencias.php        # Container DI
│   └── rotas.php              # Definição de rotas
├── coroutines.php             # Exemplo básico de corrotinas
├── http-server.php            # Servidor HTTP com channels
├── server-with-hooks.php      # Servidor com hooks habilitados
├── server.php                 # Servidor auxiliar para testes
└── file.txt                   # Arquivo de exemplo
```

## Instalação e Configuração

### Pré-requisitos

1. **PHP 8.4+**
2. **Extensão OpenSwoole**

### Instalação da Extensão OpenSwoole

```bash
# Via PECL
pecl install openswoole

# Adicionar ao php.ini
echo "extension=openswoole" >> /etc/php/8.4/cli/php.ini
```

### Instalação das Dependências

```bash
composer install
```

### Configuração do Banco de Dados

```bash
# Criar tabelas do banco
php vendor/bin/doctrine orm:schema-tool:create

# Criar usuário inicial
php bin/create-user.php
```

## Exemplos Práticos

### 1. Corrotinas Básicas

Execute o exemplo de corrotinas:

```bash
php coroutines.php
```

Este exemplo demonstra como múltiplas corrotinas executam concorrentemente.

### 2. Servidor HTTP com Channels

```bash
# Terminal 1 - Servidor auxiliar
php -S localhost:8001 server.php

# Terminal 2 - Servidor Swoole
php http-server.php
```

Acesse `http://localhost:8080` para ver as corrotinas trabalhando com channels.

### 3. Servidor com Hooks

```bash
# Terminal 1 - Servidor auxiliar  
php -S localhost:8001 server.php

# Terminal 2 - Servidor com hooks
php server-with-hooks.php
```

### 4. Aplicação Web Completa

```bash
composer dev
```

ou

```bash
php public/swoole.php
```

Acesse `http://localhost:8080` para a aplicação web completa com autenticação e CRUD.

## Performance e Benchmarks

### Comparação: PHP Tradicional vs Swoole

**PHP Tradicional (Apache/Nginx + PHP-FPM):**
- ~1.000 requisições/segundo
- Alto consumo de memória por requisição
- Cada requisição cria um novo processo/thread

**Swoole:**
- ~10.000+ requisições/segundo
- Baixo consumo de memória
- Processo persistente com corrotinas leves

### Exemplo de Teste de Performance

```php
// Testando 1000 requisições concorrentes
co::run(function () {
    for ($i = 0; $i < 1000; $i++) {
        go(function () use ($i) {
            $client = new OpenSwoole\Coroutine\Http\Client('localhost', 8080);
            $client->get('/');
            echo "Requisição $i concluída\n";
        });
    }
});
```

## Melhores Práticas

### 1. Use Hooks para Compatibilidade

```php
// Sempre habilite hooks para tornar código legado assíncrono
Co::set(['hook_flags' => OpenSwoole\Runtime::HOOK_ALL]);
```

### 2. Gerencie Recursos Adequadamente

```php
// Feche conexões adequadamente
go(function () {
    $client = new OpenSwoole\Coroutine\Http\Client('api.com', 443, true);
    $client->get('/data');
    $client->close(); // Importante!
});
```

### 3. Use Channels para Comunicação

```php
// Use channels para comunicação segura entre corrotinas
$channel = new chan(10);

go(function () use ($channel) {
    $channel->push("dados");
});

go(function () use ($channel) {
    $data = $channel->pop();
    // processar dados
});
```

### 4. Trate Exceções Adequadamente

```php
go(function () {
    try {
        $data = file_get_contents('http://api.com/data');
    } catch (Exception $e) {
        echo "Erro na corrotina: " . $e->getMessage();
    }
});
```

## Limitações e Considerações

### 1. Compatibilidade

- Nem todas as extensões PHP são compatíveis
- Algumas bibliotecas podem não funcionar corretamente
- Sempre teste bibliotecas third-party

### 2. Debugging

- Debugging pode ser mais complexo devido à natureza assíncrona
- Use ferramentas específicas para Swoole

### 3. Curva de Aprendizado

- Requer mudança de paradigma (síncrono → assíncrono)
- Conceitos como corrotinas e channels são novos para muitos desenvolvedores

## Recursos Adicionais

- [Documentação Oficial OpenSwoole](https://openswoole.com/)
- [Swoole vs Node.js vs Go Benchmarks](https://github.com/openswoole/benchmark)
- [Curso PHP com Swoole - Alura](https://www.alura.com.br/)

## Contribuição

Sinta-se livre para contribuir com exemplos, correções ou melhorias neste projeto de estudos.

## Licença

Este projeto é destinado apenas para fins educacionais.
