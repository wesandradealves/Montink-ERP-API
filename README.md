# Montink ERP API

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-blue.svg)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-Ready-green.svg)](https://docker.com)
[![Clean Architecture](https://img.shields.io/badge/Architecture-Clean%20%2B%20DDD-yellow.svg)](#arquitetura)

Sistema Mini ERP desenvolvido em Laravel seguindo princípios de Clean Architecture e Domain-Driven Design (DDD). Focado em gestão de produtos, pedidos, cupons e estoque com API REST completa e documentação Swagger interativa.

## Funcionalidades

### Implementado (v0.8.0)
- **API Products** - CRUD completo de produtos com validações
- **Sistema de Carrinho** - Gestão completa via sessão com cálculo de frete
- **Integração ViaCEP** - Busca e validação automática de endereços
- **Controle de Estoque** - Validação em tempo real com reservas
- **Sistema de Pedidos** - Finalização de compra com gestão de status
- **Sistema de Cupons** - Descontos fixos e percentuais com validações
- **Email de Confirmação** - Envio automático ao finalizar pedido via Mailpit
- **Documentação Swagger** - Interface interativa para todos os módulos
- **Health Check** - Monitoramento da saúde da API
- **Validações** - Sistema robusto com mensagens em português
- **Responses Padronizadas** - Estrutura JSON consistente com ApiResponseTrait
- **Arquitetura DRY** - BaseModels, BaseDTOs, Traits reutilizáveis

### Em Desenvolvimento
- **Webhook** - Atualização de status via webhook
- **Authentication** - Sistema de autenticação JWT
- **Testes Automatizados** - Cobertura completa da aplicação

## Documentação da API

### Acesso à Documentação
A documentação Swagger está disponível em:
- **Interface Principal**: `http://localhost/docs`
- **JSON Spec**: `http://localhost/docs.json`
- **Redirecionamentos**: `/` e `/api/` → `/docs`

### Endpoints Disponíveis

#### Products
```http
GET    /api/products          # Listar produtos com filtros
GET    /api/products/{id}     # Buscar produto específico  
POST   /api/products          # Criar novo produto
PATCH  /api/products/{id}     # Atualizar produto (parcial)
DELETE /api/products/{id}     # Excluir produto
```

#### Cart (Carrinho)
```http
GET    /api/cart              # Visualizar carrinho atual
POST   /api/cart              # Adicionar produto ao carrinho
PATCH  /api/cart/{id}         # Atualizar quantidade do item
DELETE /api/cart/{id}         # Remover item do carrinho
DELETE /api/cart              # Limpar carrinho completo
```

#### Address (Endereços)
```http
GET    /api/address/cep/{cep} # Buscar endereço por CEP
POST   /api/address/validate-cep # Validar se CEP existe
```

#### Orders (Pedidos)
```http
GET    /api/orders             # Listar pedidos com filtros
POST   /api/orders             # Criar pedido (finalizar carrinho + enviar email)
GET    /api/orders/{id}        # Buscar pedido por ID
GET    /api/orders/number/{n}  # Buscar pedido por número
PATCH  /api/orders/{id}/status # Atualizar status do pedido
DELETE /api/orders/{id}        # Cancelar pedido
```

#### Coupons (Cupons)
```http
GET    /api/coupons            # Listar cupons com filtros
POST   /api/coupons            # Criar novo cupom
GET    /api/coupons/{id}       # Buscar cupom por ID
GET    /api/coupons/code/{code} # Buscar cupom por código
PATCH  /api/coupons/{id}       # Atualizar cupom
DELETE /api/coupons/{id}       # Excluir cupom
POST   /api/coupons/validate   # Validar cupom
```

#### Health Check
```http
GET    /api/health            # Verificar saúde da API
```

### Exemplos de Uso

#### Criar Produto
```bash
curl -X POST http://localhost/api/products \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Notebook Dell",
    "description": "Notebook para desenvolvimento",
    "price": 2999.90,
    "sku": "NB-DELL-001"
  }'
```

#### Listar Produtos
```bash
curl -X GET "http://localhost/api/products?only_active=true&search=notebook"
```

#### Atualizar Produto (PATCH)
```bash
curl -X PATCH http://localhost/api/products/1 \
  -H "Content-Type: application/json" \
  -d '{
    "price": 2799.90,
    "description": "Atualização parcial do produto"
  }'
```

#### Adicionar ao Carrinho
```bash
curl -X POST http://localhost/api/cart \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "quantity": 2,
    "variations": {"cor": "preto", "memoria": "16GB"}
  }'
```

#### Buscar CEP
```bash
curl -X GET http://localhost/api/address/cep/01310100
```

#### Criar Pedido
```bash
curl -X POST http://localhost/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "João Silva",
    "customer_email": "joao@example.com",
    "customer_phone": "(11) 98765-4321",
    "customer_cpf": "123.456.789-00",
    "customer_cep": "01310-100",
    "customer_address": "Avenida Paulista, 1000",
    "customer_complement": "Apto 101",
    "customer_neighborhood": "Bela Vista",
    "customer_city": "São Paulo",
    "customer_state": "SP"
  }'
```

#### Resposta Padrão
```json
{
  "data": {
    "id": 1,
    "name": "Notebook Dell",
    "description": "Notebook para desenvolvimento",
    "price": 2999.90,
    "sku": "NB-DELL-001",
    "active": true,
    "created_at": "2025-07-17T10:30:00Z"
  },
  "message": "Produto criado com sucesso",
  "meta": {
    "timestamp": "2025-07-17T10:30:00Z",
    "version": "0.3.0"
  }
}
```

## Arquitetura

### Clean Architecture + DDD
O projeto segue rigorosamente os princípios de Clean Architecture e Domain-Driven Design:

```
app/
├── Common/                    # Código compartilhado e base
│   ├── Base/                 # Classes base (BaseModel, BaseDTO, BaseFormRequest)
│   ├── Traits/               # Traits reutilizáveis (ApiResponseTrait, ValidationMessagesTrait)
│   ├── Exceptions/           # Exceptions customizadas (ResourceNotFoundException)
│   ├── Rules/                # Regras de validação (QuantityRule)
│   └── Services/             # Serviços compartilhados (SessionService)
├── Domain/                   # Camada de Domínio - Regras de negócio
│   ├── Commons/              # Elementos compartilhados do domínio
│   ├── Entities/             # Entidades base
│   ├── Interfaces/           # Contratos do domínio
│   └── Repositories/         # Interfaces de repositórios
├── Infrastructure/           # Camada de Infraestrutura - Integrações
│   ├── Providers/            # Service Providers
│   └── External/             # Integrações externas
├── Modules/                  # Módulos de Funcionalidades
│   ├── Products/             # Módulo de Produtos
│   │   ├── Api/              # Controllers, Requests, Resources
│   │   ├── UseCases/         # ProductsUseCase consolidado
│   │   ├── DTOs/             # CreateProductDTO, UpdateProductDTO
│   │   ├── Models/           # Product extends BaseModel
│   │   └── Providers/        # ProductsServiceProvider
│   ├── Cart/                 # Módulo de Carrinho
│   │   ├── Api/              # CartController extends BaseApiController
│   │   ├── UseCases/         # CartUseCase com lógica de sessão
│   │   ├── DTOs/             # CartDTO, CartItemDTO, AddToCartDTO
│   │   ├── Models/           # CartItem
│   │   ├── Services/         # ShippingService (cálculo de frete)
│   │   └── Providers/        # CartServiceProvider
│   ├── Address/              # Módulo de Endereços
│   │   ├── Api/              # AddressController
│   │   ├── DTOs/             # AddressDTO extends BaseDTO
│   │   └── Services/         # ViaCepService (integração)
│   └── Stock/                # Módulo de Estoque
│       ├── Models/           # Stock
│       └── Services/         # StockValidationService
└── Http/                     # Camada de Apresentação HTTP
    ├── Controllers/          # Controller base com Swagger tags
    └── Schemas/              # SwaggerSchemas com definições
```

### Princípios Aplicados
- **Separação de Interesses** - Cada camada tem responsabilidade única
- **Inversão de Dependências** - Dependências apontam para o domínio
- **DRY (Don't Repeat Yourself)** - Código reutilizável e centralizado
- **Repository Pattern** - Abstração da persistência de dados
- **Use Cases** - Lógica de negócio consolidada por módulo
- **Single Responsibility** - Classes e métodos com propósito único
- **RESTful Best Practices** - Uso correto de verbos HTTP (GET, POST, PATCH, DELETE)

## Instalação e Configuração

### Pré-requisitos
- Docker & Docker Compose
- Git

### 1. Clone o Repositório
```bash
git clone <repository-url>
cd Montink
```

### 2. Configuração do Ambiente
```bash
# Copiar arquivo de configuração
cp .env.example .env

# Subir containers Docker
docker-compose up -d

# Acessar container da aplicação
docker-compose exec app bash
```

### 3. Configuração da Aplicação
```bash
# Dentro do container
composer install
php artisan key:generate
php artisan migrate
```

### 4. Configuração do Swagger
```bash
# Copiar assets do Swagger UI
mkdir -p public/vendor/swagger-api/swagger-ui
cp -r vendor/swagger-api/swagger-ui/dist public/vendor/swagger-api/swagger-ui/

# Gerar documentação
php artisan l5-swagger:generate
```

### 5. Verificação
```bash
# Verificar saúde da API
curl http://localhost/api/health

# Acessar documentação
# Abrir http://localhost/docs no navegador
```

## Comandos Disponíveis

### Docker
```bash
# Subir ambiente
docker-compose up -d

# Ver logs
docker-compose logs -f

# Parar ambiente  
docker-compose down

# Acessar container
docker-compose exec app bash
```

### Laravel
```bash
# Instalar dependências
composer install

# Executar migrações
php artisan migrate

# Limpar cache
php artisan cache:clear

# Regenerar documentação Swagger
php artisan l5-swagger:generate
```

### Desenvolvimento
```bash
# Executar testes
php artisan test

# Verificar código (Pint)
./vendor/bin/pint

# Acessar Tinker
php artisan tinker
```

## Ambiente Docker

### Containers Disponíveis
- **app** - Aplicação Laravel (PHP 8.3-FPM)
- **nginx** - Servidor web (porta 80)
- **mysql** - Banco de dados MySQL 8.0
- **redis** - Cache e sessões
- **mailpit** - Servidor de email para desenvolvimento

### Portas
- **80** - Aplicação web
- **3306** - MySQL (acessível externamente)
- **6379** - Redis (interno)
- **8025** - Mailpit interface (se configurado)

### Volumes
- Código fonte montado em `/var/www`
- Dados MySQL persistentes
- Configurações customizadas PHP e Nginx

## Testes

### Executar Testes
```bash
# Todos os testes
php artisan test

# Testes específicos
php artisan test --filter ProductTest

# Com coverage
php artisan test --coverage
```

### Estrutura de Testes
```
tests/
├── Feature/        # Testes de integração
│   └── Products/   # Testes da API Products
├── Unit/          # Testes unitários
│   └── Domain/    # Testes de domínio
└── TestCase.php   # Base para testes
```

## Configuração Avançada

### Variáveis de Ambiente
```env
# Aplicação
APP_NAME="Montink ERP"
APP_URL=http://localhost

# Banco de Dados
DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=montink_erp
DB_USERNAME=montink
DB_PASSWORD=password

# Cache e Sessões
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=redis

# Email (Desenvolvimento)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

### Configurações de Performance
- **Redis** - Cache e sessões
- **Índices** - Otimizados para queries principais
- **Connection Pooling** - MySQL configurado
- **Opcache** - PHP otimizado para produção

## Documentação Adicional

### Changelog
Veja `CHANGELOG.md` para:
- Histórico de versões
- Funcionalidades implementadas
- Mudanças técnicas
- Roadmap de desenvolvimento

### Configuração Swagger
Consulte `README-SWAGGER.md` para:
- Setup detalhado do Swagger
- Comandos de manutenção
- Troubleshooting da documentação

## Contribuição

### Padrões de Desenvolvimento
- **Commits em português** - `feat: adicionar endpoint de produtos`
- **Clean Architecture** - Seguir estrutura de camadas
- **DRY Principles** - Evitar duplicação de código
- **Testes obrigatórios** - Cobertura mínima esperada
- **Documentação Swagger** - Endpoints sempre documentados

### Fluxo de Desenvolvimento
1. **Criar branch** - `feature/nova-funcionalidade`
2. **Implementar** - Seguindo padrões da arquitetura
3. **Testar** - Testes unitários e de integração
4. **Documentar** - Swagger e comentários
5. **Commit** - Mensagens descritivas em português
6. **Pull Request** - Review obrigatório

## Roadmap

### v0.4.0 - Sistema de Pedidos
- [ ] Módulo Orders completo
- [ ] Relacionamento Products ↔ Orders
- [ ] Cálculo de totais automático
- [ ] Estados de pedido (pending, confirmed, shipped, delivered)

### v0.5.0 - Sistema de Cupons
- [ ] Módulo Coupons
- [ ] Tipos de desconto (fixo, percentual)
- [ ] Validações de uso e prazo
- [ ] Integração com Orders

### v0.6.0 - Controle de Estoque
- [ ] Módulo Stock
- [ ] Movimentações de entrada/saída
- [ ] Reservas temporárias
- [ ] Alertas de estoque baixo

### v1.0.0 - Sistema Completo
- [ ] Autenticação JWT
- [ ] Integração ViaCEP
- [ ] Dashboard administrativo
- [ ] Relatórios e métricas

## Licença

Este projeto está licenciado sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para detalhes.

## Suporte

### Problemas Comuns
- **Swagger em branco**: Execute os comandos de setup do Swagger
- **Erro de conexão BD**: Verifique se os containers estão rodando
- **Permissões**: Ajuste permissões das pastas `storage/` e `bootstrap/cache/`

### Contato
- **Issues**: Use o sistema de issues do repositório
- **Documentação**: Consulte os arquivos `.md` no projeto

---

**Montink ERP** - Sistema Mini ERP moderno com Clean Architecture