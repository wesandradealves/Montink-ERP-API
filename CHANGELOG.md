# Changelog

Todas as mudanças notáveis deste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

## [0.7.0] - 2025-07-17

### Adicionado
- **Sistema de Cupons de Desconto Completo**
  - Módulo Coupons com Clean Architecture
  - Suporte a cupons de valor fixo e porcentagem
  - Validação de valor mínimo para aplicação
  - Controle de limite de uso e contagem
  - Validação de datas de validade (valid_from/valid_until)
  - Status ativo/inativo para cupons
  - Integração completa com sistema de pedidos

- **Endpoints de Cupons**
  - POST /api/coupons - Criar novo cupom
  - GET /api/coupons - Listar cupons com filtros
  - GET /api/coupons/{id} - Buscar cupom por ID
  - GET /api/coupons/code/{code} - Buscar por código
  - PATCH /api/coupons/{id} - Atualizar cupom
  - DELETE /api/coupons/{id} - Excluir cupom
  - POST /api/coupons/validate - Validar cupom

- **Validações de Cupom**
  - Código único obrigatório
  - Validação de tipo (fixed/percentage)
  - Verificação de valor mínimo do pedido
  - Controle de limite de uso
  - Validação de período de validade
  - Mensagens de erro específicas em português

### Funcionalidades
- **Aplicação de Descontos**
  - Cálculo automático no checkout
  - Desconto fixo ou percentual
  - Incremento automático de uso
  - Validação em tempo real

- **Integração com Pedidos**
  - Campo coupon_code no pedido
  - Aplicação automática do desconto
  - Registro do cupom usado (coupon_id)
  - Cálculo correto do total final

### Técnico
- **CouponsUseCase**
  - Validação completa de regras
  - Método applyCoupon com transação
  - Formatação de valores monetários
  - Controle de concorrência com lockForUpdate

- **Model Coupon**
  - Scopes para consultas (valid, byCode)
  - Métodos de validação (isValid, canBeUsedWithValue)
  - Cálculo de desconto automático
  - Formatação de valores para exibição

- **Documentação Swagger**
  - Todos endpoints documentados
  - Schema Coupon completo
  - Exemplos de uso
  - Códigos de resposta detalhados

---

**Meta da v0.7.0**: Sistema de cupons de desconto funcional permitindo criar promoções com regras flexíveis e integração completa com o sistema de pedidos.

## [0.6.0] - 2025-07-17

### Adicionado
- **Sistema de Pedidos Completo (Orders)**
  - Módulo Orders com Clean Architecture
  - Finalização de carrinho em pedido
  - Armazenamento completo de dados do cliente
  - Gerenciamento de status (pending, processing, shipped, delivered, cancelled)
  - Número de pedido único e sequencial
  - Relacionamento com itens do pedido

- **Endpoints de Pedidos**
  - POST /api/orders - Criar pedido finalizando carrinho
  - GET /api/orders - Listar pedidos com filtros
  - GET /api/orders/{id} - Buscar pedido por ID
  - GET /api/orders/number/{orderNumber} - Buscar por número
  - PATCH /api/orders/{id}/status - Atualizar status
  - DELETE /api/orders/{id} - Cancelar pedido

- **Validações de Pedido**
  - Dados completos do cliente obrigatórios
  - Formatação automática de CEP e CPF
  - Validação de carrinho não vazio
  - Controle de cancelamento por status

### Funcionalidades
- **Finalização de Compra**
  - Conversão automática de carrinho em pedido
  - Cálculo de totais com frete
  - Limpeza do carrinho após finalização
  - Registro de itens com snapshot de preços

- **Gestão de Status**
  - Fluxo de status bem definido
  - Restrições de cancelamento
  - Histórico de mudanças via timestamps

### Técnico
- **OrdersUseCase**
  - Transações para integridade
  - Geração de número de pedido único
  - Relacionamento automático com itens
  - Preparação para cupons de desconto

- **Models Order e OrderItem**
  - Relacionamentos Eloquent configurados
  - Scopes para consultas otimizadas
  - Métodos auxiliares de status

- **Validações Avançadas**
  - CreateOrderRequest com formatação automática
  - UpdateOrderStatusRequest com enum validation
  - Mensagens em português centralizadas

---

**Meta da v0.6.0**: Sistema de pedidos funcional permitindo finalização completa de compras com gestão de status e dados do cliente.

## [0.5.0] - 2025-07-17

### Adicionado
- **Integração Completa com API ViaCEP**
  - Módulo Address com Clean Architecture
  - Serviço ViaCepService para consultas de CEP
  - Endpoints para busca e validação de CEP
  - Tratamento de erros e timeouts
  - Respostas padronizadas em português

- **Endpoints de Endereço**
  - GET /api/address/cep/{cep} - Buscar endereço completo
  - POST /api/address/validate-cep - Validar se CEP existe
  - DTOs específicos para endereços
  - Documentação Swagger completa

- **Infraestrutura DRY**
  - BaseFormRequest para eliminar authorize() duplicado
  - ValidationMessagesTrait para mensagens padronizadas
  - ResourceNotFoundException para erros consistentes
  - StockValidationService para lógica centralizada

### Melhorado
- **Documentação Swagger Completa**
  - Todos os módulos agora aparecem no Swagger
  - Tags adicionadas para Cart e Address
  - Schema Address definido
  - Rotas de Address registradas
  - Geração automática funcionando

- **Refatoração DRY Aplicada**
  - Product estende BaseModel (elimina casts duplicados)
  - CreateProductDTO estende BaseDTO (remove toArray duplicado)
  - CartController usa handleUseCaseExecution (elimina try-catch)
  - Requests usam ValidationMessagesTrait (mensagens centralizadas)
  - Validação de estoque centralizada em serviço

- **Métodos HTTP Melhorados**
  - Todos endpoints de atualização mudados de PUT para PATCH
  - UpdateProductRequest usa 'sometimes' para atualizações parciais
  - Prática RESTful adequada para modificações parciais
  - Documentação Swagger atualizada com PATCH

### Corrigido
- **Consistência da API**
  - Todos os endpoints aparecem no Swagger
  - Respostas padronizadas em todos os módulos
  - Tratamento de erros uniformizado
  - Mensagens de validação em português

### Técnico
- **Fluxo de Qualidade Obrigatório**
  - Teste de todos endpoints após implementação
  - Verificação de consistência no Swagger
  - Análise de redundâncias DRY
  - Testes de regressão
  - Documentação sempre atualizada

- **Padrões Estabelecidos**
  - Um UseCase por responsabilidade de módulo
  - Validação única no Request
  - Métodos privados para lógica compartilhada
  - Traits para comportamentos comuns
  - Exceptions customizadas para erros específicos

---

**Meta da v0.5.0**: Sistema com integração ViaCEP funcional e código 100% DRY, estabelecendo padrões de qualidade e consistência para toda a aplicação.

## [0.4.0] - 2025-07-17

### Adicionado
- **Sistema de Carrinho Completo**
  - Módulo Cart com Clean Architecture
  - Gerenciamento via sessão PHP
  - Validação automática de estoque
  - CRUD completo para itens do carrinho
  - Cálculo automático de subtotais

- **Cálculo de Frete Inteligente**
  - Regras de frete conforme briefing Montink
  - R$ 52,00 a R$ 166,59: Frete R$ 15,00
  - Acima de R$ 200,00: Frete grátis
  - Outros valores: Frete R$ 20,00
  - Cálculo automático integrado ao carrinho

- **Endpoints do Carrinho**
  - GET /api/cart - Obter carrinho atual
  - POST /api/cart - Adicionar produto ao carrinho
  - PUT /api/cart/{id} - Atualizar quantidade
  - DELETE /api/cart/{id} - Remover item
  - DELETE /api/cart - Limpar carrinho

- **Modelo Stock**
  - Controle de estoque com quantidade disponível
  - Validação automática no carrinho
  - Suporte a variações de produtos
  - Cálculo de disponibilidade (quantidade - reservado)

### Funcionalidades
- **Carrinho de Sessão**
  - Persistência durante navegação
  - Validação de estoque em tempo real
  - Cálculo automático de totais
  - Suporte a variações de produtos

- **Sistema de Frete**
  - Aplicação automática de regras
  - Descrições amigáveis (ex: "Frete grátis")
  - Integração transparente com carrinho
  - Cálculo total final com frete

- **Validações Robustas**
  - Verificação de estoque disponível
  - Validação de produtos existentes
  - Tratamento de erros específicos
  - Mensagens em português

### Técnico
- **CartUseCase**
  - Lógica de negócio centralizada
  - Validação de estoque integrada
  - Cálculo de frete automático
  - Gerenciamento de sessão

- **ShippingService**
  - Serviço dedicado para cálculo de frete
  - Regras de negócio isoladas
  - Métodos reutilizáveis
  - Formatação consistente

- **DTOs Específicos**
  - CartDTO com informações completas
  - CartItemDTO para itens individuais
  - AddToCartDTO para requisições
  - Tipagem forte e consistente

- **Documentação Swagger**
  - Schemas Cart e CartItem
  - Exemplos práticos de uso
  - Documentação completa de endpoints
  - Integração com schemas existentes

### Melhorias
- **ApiResponseTrait**
  - Trait para respostas padronizadas
  - Métodos successResponse e errorResponse
  - Consistência em toda aplicação
  - Eliminação de duplicação de código

- **BaseModel**
  - Classe base para todos os models
  - Configurações padrão centralizadas
  - Scope active reutilizável
  - Estrutura consistente

---

**Meta da v0.4.0**: Sistema de carrinho e frete completamente funcional seguindo regras de negócio específicas do briefing Montink, com validações robustas e experiência de usuário otimizada.

## [0.3.0] - 2025-07-17

### Adicionado
- **Documentação Swagger/OpenAPI Completa**
  - Interface Swagger UI interativa acessível em `/docs`
  - Pacote L5-Swagger instalado e configurado
  - ViewServiceProvider adicionado para suporte completo
  - Documentação JSON disponível em `/docs.json`
  - Redirecionamentos automáticos de `/` e `/api/` para `/docs`

- **Endpoints Documentados**
  - GET /api/products - Listar produtos com filtros
  - GET /api/products/{id} - Buscar produto específico
  - POST /api/products - Criar novo produto
  - PUT /api/products/{id} - Atualizar produto
  - DELETE /api/products/{id} - Excluir produto
  - GET /api/health - Verificação de saúde da API

- **Schemas Reutilizáveis**
  - Schema Product com todas as propriedades
  - Schema ApiResponse para respostas padronizadas
  - Schema ApiListResponse para listagens
  - Schema ApiErrorResponse para erros
  - Schema ValidationError para validações
  - Schemas preparados para Order, Coupon e Stock

- **HealthController**
  - Controller dedicado para endpoint de saúde
  - Documentação Swagger integrada
  - Resposta padronizada com status, timestamp e versão

### Funcionalidades
- **Interface Swagger UI**
  - Tela padrão do Swagger/OpenAPI
  - Possibilidade de testar endpoints diretamente
  - Filtros e parâmetros organizados
  - Códigos de status HTTP documentados
  - Exemplos práticos em português

- **Documentação Interativa**
  - Formulários para teste de requests
  - Schemas de resposta detalhados
  - Exemplos de payloads JSON
  - Validações documentadas
  - Descrições em português

### Configurações
- **L5-Swagger**
  - Configuração completa em config/l5-swagger.php
  - URLs customizadas para documentação
  - Assets do Swagger UI integrados
  - Views customizadas para paths corretos

- **Redirecionamentos**
  - Raiz do site (/) redireciona para /docs
  - Rota /api/ redireciona para /docs  
  - Acesso direto à documentação facilitado

### Técnico
- **Annotations Swagger**
  - Controller base com informações da API
  - Todos os endpoints do Products documentados
  - Tags organizadas por módulo
  - Security schemes preparados para JWT

- **Assets e Views**
  - Views customizadas do L5-Swagger
  - Assets do Swagger UI copiados para public/vendor
  - Configuração de paths corrigida
  - Suporte completo a CSS e JavaScript

---

**Meta da v0.3.0**: Documentação completa e interativa da API com Swagger/OpenAPI, facilitando o desenvolvimento e uso da API Montink ERP.

## [0.2.0] - 2025-01-17

### Adicionado
- **Módulo Products Completo com CRUD**
  - API REST completa para gestão de produtos
  - Model Product com casts e validações automáticas
  - ProductRepository implementando interface com padrão DRY
  - ProductsUseCase consolidando todas operações de negócio
  - ProductController com responses JSON padronizadas
  - CreateProductRequest e UpdateProductRequest com validações Laravel
  - CreateProductDTO e UpdateProductDTO para transferência de dados
  - ProductsServiceProvider para injeção de dependência

- **ApiResponseTrait para Padronização DRY**
  - Trait reutilizável para respostas JSON consistentes
  - Métodos successResponse, successListResponse e errorResponse
  - Eliminação completa de duplicação de código de resposta
  - Padrão aplicado em todos os controllers da aplicação

- **Configuração API-Only Otimizada**
  - Laravel configurado exclusivamente para API sem frontend
  - RouteServiceProvider customizado para rotas API
  - Remoção de providers desnecessários (View, Session)
  - HTTP Kernel simplificado apenas com essentials
  - Health check endpoint funcional em /api/health

### Funcionalidades
- **Endpoints Products API**
  - `GET /api/products` - Listagem com filtros (ativo, busca)
  - `GET /api/products/{id}` - Busca individual
  - `POST /api/products` - Criação com validação completa
  - `PUT /api/products/{id}` - Atualização parcial
  - `DELETE /api/products/{id}` - Exclusão com verificação

- **Validações Implementadas**
  - Nome obrigatório (máx. 255 caracteres)
  - SKU único no sistema
  - Preço numérico obrigatório (mín. 0)
  - Descrição opcional
  - Status ativo (boolean, padrão true)
  - Variações em formato JSON opcional

- **Responses Padronizadas**
  - Estrutura consistente com data/message/meta
  - Códigos HTTP apropriados (200, 201, 404, 422)
  - Mensagens de erro descritivas em português
  - Metadata com contagem total em listagens

### Removido
- **Limpeza Rigorosa DRY**
  - 40+ diretórios vazios removidos
  - Arquivos frontend eliminados (CSS, JS, Views)
  - Views compiladas removidas do storage
  - Middleware não utilizados excluídos
  - Migrações órfãs de módulos não implementados
  - Configurações desnecessárias (view.php, broadcasting.php)
  - 2 imports não utilizados identificados e removidos

### Refatorado
- **Princípios DRY Aplicados**
  - Método buildQuery() privado no Repository eliminando duplicação
  - Validação única no Request (removida duplicação no UseCase)
  - 5 arquivos UseCase consolidados em 1 ProductsUseCase
  - Código de resposta JSON centralizado em trait
  - Imports limpos em todos os arquivos

### Técnico
- **Arquitetura Clean Implementada**
  - Repository Pattern com interface abstrata
  - Use Case único concentrando regras de negócio
  - DTOs para isolamento de dados entre camadas
  - Service Provider gerenciando injeção de dependência
  - Controller fino apenas delegando para Use Case

- **Banco de Dados Configurado**
  - Migration products executada com sucesso
  - Conexão MySQL via Docker funcional
  - Índices criados para performance (SKU, active+name)
  - Timestamps automáticos configurados

- **Docker Environment**
  - Ambiente completamente funcional
  - Containers: MySQL, Redis, Nginx, Mailpit, PHP-FPM
  - Volumes persistentes configurados
  - Network interna para comunicação entre serviços

### Melhorias de Qualidade
- **Processo de Desenvolvimento Otimizado**
  - Testes funcionais integrados ao fluxo
  - Padrão de commits consistente
  - Versionamento semântico aplicado
  - Controle de qualidade automatizado

---

**Meta da v0.2.0**: Módulo Products completamente funcional seguindo Clean Architecture e princípios DRY, estabelecendo padrão ouro para todos os próximos módulos do sistema.

## [0.1.0] - 2025-01-16

### Adicionado
- **Configuração Completa do Ambiente Docker**
  - Docker Compose com MySQL 8.0, Redis 7, Nginx Alpine e Mailpit
  - Dockerfile multi-stage para desenvolvimento e produção
  - Configurações otimizadas PHP 8.3 com extensões necessárias
  - Suporte a XDebug para desenvolvimento
  - Nginx configurado para servir aplicação Laravel
  - MySQL com configurações de performance e charset UTF-8
  - Mailpit para testes de email em desenvolvimento
  - Queue worker configurado para processamento assíncrono

- **Estrutura Base Laravel com Clean Architecture**
  - Projeto Laravel configurado com composer.json completo
  - Estrutura de diretórios seguindo Clean Architecture e DDD
  - Camada Domain com interfaces, enums e contratos
  - Camada Infrastructure para integrações externas
  - Camada Modules organizada por funcionalidades
  - Script artisan para comandos CLI
  - Configuração .env.example com todas as variáveis necessárias

- **Schema de Banco Completo para Mini ERP**
  - Migration products: nome, preço, SKU, variações JSON, status ativo
  - Migration stock: controle de quantidade, reserva e disponibilidade calculada
  - Migration coupons: códigos, tipos (fixo/percentual), validações temporais
  - Migration orders: dados completos do cliente, endereço, totais, status
  - Migration order_items: itens do pedido com variações e preços
  - Relacionamentos e índices otimizados para performance
  - Suporte completo a variações de produtos
  - Sistema de cupons com regras de negócio

- **Fundamentos da Arquitetura Clean**
  - BaseRepositoryInterface com métodos CRUD padrão
  - BaseUseCaseInterface para casos de uso
  - OrderStatus enum com labels e métodos auxiliares
  - Separação clara de responsabilidades por camada
  - Inversão de dependências configurada

- **Ferramentas de Desenvolvimento**
  - Makefile com comandos Docker padronizados (up, down, shell, logs)
  - Scripts para migrations, testes e otimização
  - Comandos de verificação automática de commits e código
  - Verificação de regras anti-IA nos commits
  - Comandos para acesso shell e debug do ambiente

### Técnico
- **Arquitetura de Módulos**
  - Estrutura preparada para módulos: Products, Orders, Coupons, Stock
  - Cada módulo com API Controllers, Requests, Resources, DTOs
  - Use Cases por módulo seguindo padrões DDD
  - Service Providers para injeção de dependência
  - Models Eloquent específicos por módulo

- **Configurações de Ambiente**
  - PHP 8.3 com extensões: PDO MySQL, mbstring, zip, GD, BCMath
  - MySQL configurado com InnoDB e UTF-8 colation
  - Redis para cache e sessões
  - Timezone configurado para America/Sao_Paulo
  - Limites de upload e memória otimizados

- **Integração com Briefing Montink**
  - Estrutura completa para mini ERP conforme especificado
  - Tabelas obrigatórias: produtos, pedidos, cupons, estoque
  - Suporte a todas as funcionalidades do briefing
  - Regras de frete implementáveis
  - Preparação para integração ViaCEP
  - Base para sistema de carrinho e checkout

### Documentação
- **Guia Completo de Clean Architecture**
  - Documentação detalhada da arquitetura implementada
  - Padrões adaptados do projeto Dourado para Laravel
  - Exemplos de implementação por camada
  - Regras de desenvolvimento e commit rigorosamente definidas
  - Templates para novos módulos e funcionalidades

- **Análise do Briefing Montink**
  - Entendimento estruturado dos requisitos do projeto
  - Estratégia de desenvolvimento com cronograma
  - Critérios de avaliação mapeados
  - Funcionalidades obrigatórias e bônus organizadas
  - Meta de impressionar com código limpo e funcional

### Regras de Desenvolvimento Estabelecidas
- **Commits em português** com formato `[PROJ-XXX] tipo: descrição`
- **ZERO menções a IA, Claude, ChatGPT** ou assistentes nos commits
- **Código limpo** sem comentários desnecessários
- **Verificações automáticas** antes de cada commit
- **Divisão por responsabilidades** em commits granulares
- **Clean Architecture** rigorosamente seguida
- **Testes obrigatórios** antes de finalizar funcionalidades

---

**Meta da v0.1.0**: Estabelecer fundação sólida e profissional para desenvolvimento do mini ERP Montink, seguindo as melhores práticas de Clean Architecture e atendendo 100% dos requisitos do briefing técnico.