# Projeto Montink - Entendimento do Briefing

## 📋 Sobre a Empresa

### Montink - Print on Demand
- **Startup líder** em Print on Demand
- **10+ anos** de mercado
- **300 mil lojas** atendidas
- **Missão**: Permitir que empreendedores criem, personalizem e vendam produtos online
- **Lema**: "Crie sua loja, divulgue, lucre e seja feliz!"

**Links importantes:**
- Site: https://montink.com/
- Instagram: https://www.instagram.com/soumontink/
- YouTube: https://www.youtube.com/@montinkoficial

## 💼 Vaga: Desenvolvedor Back End PHP

### Informações da Vaga
- **Modalidade**: Remota
- **Horário**: Flexível dentro do período comercial (segunda a sexta)
- **Bônus de Indicação**: R$10.000,00 por indicação efetivada (90 dias)

### Benefícios
- 💵 Bônus anual de participação nos resultados
- 🌴 Férias remuneradas
- 🥳 Day-off no mês do aniversário
- 🚘 Ajuda de custo e deslocamento
- 🏠 Bônus por tempo de casa
- 🧳 Permissão para criar loja própria na infraestrutura

## 🎯 Responsabilidades Principais

1. **Integrações com Marketplaces**
   - Mercado Livre
   - Shopee
   - Amazon

2. **Desenvolvimento de APIs Próprias**
   - Conectar sistemas internos
   - Facilitar operações dos lojistas

3. **Implementação e Manutenção**
   - Otimizar sistemas existentes
   - Realizar melhorias contínuas

4. **Exportação de Produtos**
   - Facilitar exportação para marketplaces
   - Garantir funcionamento sem problemas

5. **Colaboração com SAC**
   - Assegurar integrações funcionando perfeitamente
   - Suporte técnico quando necessário

## 🛠️ Requisitos Técnicos

### Obrigatórios
- **PHP** sólido
- **Frameworks**: CodeIgniter e Laravel
- **Frontend básico**: HTML, CSS, JS, jQuery
- **APIs REST** + OAuth2
- **MySQL**: modelagem e otimização
- **Integrações de sistemas** (e-commerce)
- **Autenticação**: usuário/senha e tokens

## 📝 TESTE TÉCNICO - Mini ERP

### Objetivo
Criar um mini ERP para controle de:
- **Pedidos**
- **Produtos** 
- **Cupons**
- **Estoque**

### Tecnologias
- **Banco**: MySQL obrigatório
- **Frontend**: Bootstrap recomendado
- **Backend**: 
  - **Preferência**: PHP Puro ou CodeIgniter 3 (pontos extras)
  - **Aceito**: Laravel

### Estrutura do Banco
**4 Tabelas obrigatórias:**
1. `pedidos`
2. `produtos` 
3. `cupons`
4. `estoque`

## 🚀 Funcionalidades Obrigatórias

### 1. Gestão de Produtos
**Tela única para:**
- **Cadastro** com campos:
  - Nome
  - Preço
  - Variações
  - Estoque
- **Atualização** de dados do produto e estoque
- **Associações** automáticas entre tabelas produtos/estoque

### 2. Sistema de Carrinho
**Botão "Comprar" deve:**
- Gerenciar carrinho em sessão
- Controlar estoque disponível
- Calcular valores do pedido

### 3. Regras de Frete
- **R$52,00 a R$166,59**: Frete R$15,00
- **Acima de R$200,00**: Frete grátis
- **Outros valores**: Frete R$20,00

### 4. Integração CEP
- **API**: https://viacep.com.br/
- Verificação de endereço

## ⭐ Funcionalidades Bônus

### 1. Sistema de Cupons
- **Gestão**: Tela de administração ou migration
- **Validação**: Data de validade
- **Regras**: Valores mínimos baseados no subtotal

### 2. Variações de Produtos
- **Cadastro**: Múltiplas variações por produto
- **Controle**: Estoque individual por variação

### 3. Email de Confirmação
- **Envio**: Script automático ao finalizar pedido
- **Conteúdo**: Dados do pedido + endereço do cliente

### 4. Webhook de Status
- **Recebe**: ID do pedido + status
- **Ações**:
  - Status "cancelado" → Remove pedido
  - Outros status → Atualiza status no sistema

## 📐 Critérios de Avaliação

### Técnicos
- **MVC**: Arquitetura bem estruturada
- **Código limpo**: Boas práticas de desenvolvimento
- **Simplicidade**: Evitar overengineering
- **Manutenibilidade**: Fácil de entender e manter
- **Prevenção**: Pensar em situações corriqueiras

### Visuais
- **Não eliminatório**: Visual não reprova
- **Pontos extras**: Boa apresentação visual conta positivamente

## 📤 Entrega

### Requisitos
- **Repositório**: GitHub PÚBLICO
- **SQL**: Código para gerar banco de dados incluído
- **Formulário**: https://forms.gle/zvELdT4xfeitVZA36

### Processo Seletivo
1. **Inscrição/Teste**: Formulário + teste técnico
2. **Entrevista Inicial**: Conversa sobre experiência
3. **Desafio Técnico**: Avaliação prática
4. **Entrevista Final**: Alinhamento com liderança
5. **Feedback**: Resposta rápida + integração

## 🎯 Estratégia de Desenvolvimento

### Foco Principal
1. **Funcionalidades obrigatórias** primeiro
2. **Clean Architecture** adaptada para CodeIgniter/Laravel
3. **Código limpo** sem comentários
4. **Regras de negócio** bem implementadas

### Tecnologia Escolhida
- **Laravel** com Clean Architecture
- **MySQL** conforme especificado
- **Bootstrap** para frontend
- **Docker** para ambiente

### Estrutura do Projeto
- Seguir padrões do guia Laravel Clean Architecture
- Módulos por funcionalidade (Produtos, Pedidos, Cupons, Estoque)
- APIs REST bem documentadas
- Testes automatizados

## 📊 Cronograma Sugerido

### Fase 1: Setup (1 dia)
- Configurar ambiente Docker
- Estrutura base Laravel
- Banco de dados e migrations

### Fase 2: Core (2-3 dias)
- Módulo Produtos (CRUD + variações)
- Módulo Estoque (controle automático)
- Sistema de carrinho

### Fase 3: Regras de Negócio (1-2 dias)
- Cálculo de frete
- Integração ViaCEP
- Finalização de pedidos

### Fase 4: Bônus (1-2 dias)
- Sistema de cupons
- Email de confirmação
- Webhook de status

### Fase 5: Polimento (1 dia)
- Interface Bootstrap
- Documentação
- Testes finais

## ⚠️ Pontos de Atenção

1. **Não usar comentários** no código
2. **Commits em português** sem menção a IA
3. **Evitar overengineering** - simplicidade é key
4. **Pensar em edge cases** durante desenvolvimento
5. **Documentar regras de negócio** claramente
6. **Testar todas as funcionalidades** antes da entrega

## 🚀 Objetivo Final

Criar um **mini ERP funcional** que demonstre:
- **Competência técnica** em PHP/Laravel
- **Conhecimento de e-commerce** (carrinho, frete, cupons)
- **Integração de APIs** (ViaCEP, webhook)
- **Boas práticas** de desenvolvimento
- **Pensamento prático** para resolução de problemas

---

**Meta**: Impressionar a Montink com um código limpo, funcional e bem estruturado que mostre capacidade para trabalhar com integrações de e-commerce em ambientes reais.