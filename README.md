# Montink ERP API

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-blue.svg)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-Ready-green.svg)](https://docker.com)
[![Clean Architecture](https://img.shields.io/badge/Architecture-Clean%20%2B%20DDD-yellow.svg)](#arquitetura)

Sistema Mini ERP desenvolvido em Laravel seguindo princípios de Clean Architecture e Domain-Driven Design (DDD). Focado em gestão de produtos, pedidos, cupons e estoque com API REST completa e documentação Swagger interativa.

## 🚀 Funcionalidades

### ✅ Implementado
- **API Products** - CRUD completo de produtos com validações
- **Documentação Swagger** - Interface interativa para testes
- **Health Check** - Monitoramento da saúde da API
- **Validações** - Sistema robusto de validação de dados
- **Responses Padronizadas** - Estrutura JSON consistente

### 📋 Planejado
- **Orders** - Sistema completo de pedidos
- **Coupons** - Gestão de cupons e descontos
- **Stock** - Controle de estoque em tempo real
- **Authentication** - Sistema de autenticação JWT
- **Integration ViaCEP** - Busca automática de endereços

## 📖 Documentação da API

### 🌐 Acesso à Documentação
A documentação Swagger está disponível em:
- **Interface Principal**: `http://localhost/docs`
- **JSON Spec**: `http://localhost/docs.json`
- **Redirecionamentos**: `/` e `/api/` → `/docs`

### 🛠️ Endpoints Disponíveis

#### Products
```http
GET    /api/products          # Listar produtos com filtros
GET    /api/products/{id}     # Buscar produto específico  
POST   /api/products          # Criar novo produto
PUT    /api/products/{id}     # Atualizar produto
DELETE /api/products/{id}     # Excluir produto
```

#### Health Check
```http
GET    /api/health            # Verificar saúde da API
```

### 📝 Exemplos de Uso

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

## 🏗️ Arquitetura

### Clean Architecture + DDD
O projeto segue rigorosamente os princípios de Clean Architecture e Domain-Driven Design:

```
app/
├── Domain/              # Camada de Domínio - Regras de negócio
├── Infrastructure/      # Camada de Infraestrutura - Integrações
├── Modules/            # Módulos de Funcionalidades
│   └── Products/       # Módulo Products
│       ├── Api/        # Controllers, Requests, Resources
│       ├── UseCases/   # Casos de uso de negócio
│       ├── DTOs/       # Data Transfer Objects
│       ├── Models/     # Models Eloquent
│       └── Providers/  # Service Providers
├── Http/              # Camada de Apresentação HTTP
├── Functions/         # Jobs, Events, Processors
└── Common/           # Código compartilhado
```

### Princípios Aplicados
- **Separação de Interesses** - Cada camada tem responsabilidade única
- **Inversão de Dependências** - Dependências apontam para o domínio
- **DRY (Don't Repeat Yourself)** - Código reutilizável e centralizado
- **Repository Pattern** - Abstração da persistência de dados
- **Use Cases** - Lógica de negócio isolada e testável

## 🐳 Instalação e Configuração

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

## 🏃‍♂️ Comandos Disponíveis

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

## 🏭 Ambiente Docker

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

## 🧪 Testes

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

## 🔧 Configuração Avançada

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

## 📚 Documentação Adicional

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

## 🤝 Contribuição

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

## 📋 Roadmap

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

## 📄 Licença

Este projeto está licenciado sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para detalhes.

## 🆘 Suporte

### Problemas Comuns
- **Swagger em branco**: Execute os comandos de setup do Swagger
- **Erro de conexão BD**: Verifique se os containers estão rodando
- **Permissões**: Ajuste permissões das pastas `storage/` e `bootstrap/cache/`

### Contato
- **Issues**: Use o sistema de issues do repositório
- **Documentação**: Consulte os arquivos `.md` no projeto

---

**Montink ERP** - Sistema Mini ERP moderno com Clean Architecture 🚀