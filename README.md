# Precifica Facil

SaaS em Laravel para precificacao de produtos alimenticios, focado em doceiras, confeiteiras, salgadeiras, marmiteiras e pequenos negocios de alimentacao.

## Visao geral

O Precifica Facil organiza a estrutura de custo e venda do produto em um fluxo pratico:

- cadastro de categorias, ingredientes, embalagens e produtos
- montagem de receitas com itens, custos extras e embalagem
- calculo de custo total, custo unitario e preco sugerido
- configuracao de canais de venda com taxas fixas e percentuais
- calculo e persistencia de preco especifico por produto/canal
- configuracoes globais da empresa, como margem padrao e casas decimais

## Stack

- PHP 8.2+
- Laravel 12
- Blade
- Laravel Breeze
- Tailwind CSS
- MySQL ou outro banco compativel com Laravel

## Arquitetura

O backend segue o padrao:

`Controller -> Service -> Repository -> Model`

Essa estrutura foi mantida em todos os modulos principais do sistema.

## Modulos implementados

- autenticacao web com cadastro multiempresa
- empresas, usuarios e configuracoes
- categorias
- ingredientes
- embalagens
- produtos
- receitas
- itens da receita
- custos extras
- canais de venda
- taxas por canal
- precos por produto/canal

## Canais de venda

O projeto possui um modulo escalavel para canais de venda.

Cada canal pode ter:

- taxas percentuais
- taxas fixas
- status ativo/inativo

Com isso, o sistema calcula o preco final do canal a partir do valor liquido desejado para o produto.

Exemplos:

- Loja / Balcao
- WhatsApp
- iFood Entrega
- iFood Balcao

## Requisitos

Antes de executar o projeto, tenha instalado:

- PHP 8.2 ou superior
- Composer
- Node.js 20+ e npm
- banco de dados configurado no `.env`

## Instalacao

1. Clone o repositorio.
2. Instale as dependencias PHP.
3. Instale as dependencias frontend.
4. Configure o arquivo `.env`.
5. Gere a chave da aplicacao.
6. Rode as migrations e seeders.
7. Compile os assets.

Comandos:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm run build
php artisan serve
```

No Windows PowerShell, se `cp` nao funcionar como esperado, use:

```powershell
Copy-Item .env.example .env
```

## Seeder padrao

O `DatabaseSeeder` popula a base inicial com:

- empresa padrao
- usuario owner
- configuracoes iniciais
- categorias
- ingredientes
- produtos
- receitas
- itens da receita
- canais de venda padrao

Usuario seed inicial:

- email: `admin@precificafacil.com`
- senha: `123456`

## Rodando em desenvolvimento

Para subir aplicacao e Vite em paralelo:

```bash
composer run dev
```

Ou, separadamente:

```bash
php artisan serve
npm run dev
```

## Principais rotas internas

Depois do login, os principais modulos ficam disponiveis em:

- `/dashboard`
- `/categories`
- `/ingredients`
- `/packagings`
- `/sales-channels`
- `/products`
- `/recipes`
- `/settings`

## Calculo de precificacao

O fluxo base do calculo hoje considera:

- custo unitario dos ingredientes
- custo dos itens da receita
- custos extras fixos e percentuais
- custo de embalagem
- margem do produto ou margem global da empresa
- precos especificos por canal de venda

## Publicacao no Git

Antes de subir para o repositorio remoto:

- confirme que o `.env` nao esta versionado
- confirme que `vendor/`, `node_modules/`, `public/build/` e `storage/` nao estao entrando no commit
- rode `php artisan optimize:clear` se quiser limpar cache local antes do push

## Status atual

O projeto ja possui base funcional para uso interno e demonstracao do fluxo principal de precificacao.

Passos recomendados de evolucao:

- testes de feature para os fluxos principais
- refinamento visual das telas restantes
- dashboards com metricas financeiras e operacionais
- exportacao e relatorios
- multiusuarios com permissoes mais detalhadas

## Licenca

Projeto privado.