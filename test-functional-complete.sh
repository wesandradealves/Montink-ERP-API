#!/bin/bash

echo "🧪 TESTE FUNCIONAL COMPLETO - FLUXO E2E"
echo "======================================="
echo ""

BASE_URL="http://localhost/api"
ERRORS=0
SUCCESS=0

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Cookie jar para manter sessão
COOKIE_JAR="cookies.txt"
rm -f $COOKIE_JAR

# Função para testar endpoint
test_endpoint() {
    local method=$1
    local endpoint=$2
    local data=$3
    local expected_status=$4
    local description=$5
    local use_auth=$6
    
    echo -n "Testing: $description... "
    
    # Headers base
    local headers="-H 'Content-Type: application/json' -H 'Accept: application/json'"
    
    # Adicionar autenticação se necessário
    if [ "$use_auth" = "auth" ] && [ ! -z "$ACCESS_TOKEN" ]; then
        headers="$headers -H \"Authorization: Bearer $ACCESS_TOKEN\""
    fi
    
    # Construir comando curl com cookie jar
    local curl_cmd="curl -s -w '\n%{http_code}' -X $method $headers -b $COOKIE_JAR -c $COOKIE_JAR"
    
    if [ "$method" = "GET" ] || [ "$method" = "DELETE" ]; then
        response=$(eval "$curl_cmd '$BASE_URL$endpoint'")
    else
        response=$(eval "$curl_cmd -d '$data' '$BASE_URL$endpoint'")
    fi
    
    http_code=$(echo "$response" | tail -n1)
    body=$(echo "$response" | sed '$d')
    
    if [ "$http_code" = "$expected_status" ]; then
        echo -e "${GREEN}✓ OK${NC} (Status: $http_code)"
        ((SUCCESS++))
        echo "$body" > last_response.json
        return 0
    else
        echo -e "${RED}✗ FAIL${NC} (Expected: $expected_status, Got: $http_code)"
        echo "Response: $body"
        ((ERRORS++))
        return 1
    fi
}

echo "🔐 1. TESTE DE AUTENTICAÇÃO"
echo "=========================="

# 1.1 Registro de usuário
USER_EMAIL="test_$(date +%s)@example.com"
USER_PASS="Test123456"

test_endpoint "POST" "/auth/register" \
    "{\"name\":\"Test User\",\"email\":\"$USER_EMAIL\",\"password\":\"$USER_PASS\",\"password_confirmation\":\"$USER_PASS\"}" \
    "201" \
    "Registro de novo usuário"

# Extrair tokens usando jq ou grep
if [ -f last_response.json ]; then
    export ACCESS_TOKEN=$(cat last_response.json | grep -o '"access_token":"[^"]*' | cut -d'"' -f4)
    export REFRESH_TOKEN=$(cat last_response.json | grep -o '"refresh_token":"[^"]*' | cut -d'"' -f4)
    echo "Token obtido: ${ACCESS_TOKEN:0:20}..."
fi

# 1.2 Login
test_endpoint "POST" "/auth/login" \
    "{\"email\":\"$USER_EMAIL\",\"password\":\"$USER_PASS\"}" \
    "200" \
    "Login com credenciais válidas"

# Atualizar tokens do login
if [ -f last_response.json ]; then
    export ACCESS_TOKEN=$(cat last_response.json | grep -o '"access_token":"[^"]*' | cut -d'"' -f4)
    export REFRESH_TOKEN=$(cat last_response.json | grep -o '"refresh_token":"[^"]*' | cut -d'"' -f4)
fi

# 1.3 Login com credenciais inválidas
test_endpoint "POST" "/auth/login" \
    "{\"email\":\"$USER_EMAIL\",\"password\":\"wrongpassword\"}" \
    "401" \
    "Login com senha incorreta (deve retornar 401)"

# 1.4 Obter dados do usuário autenticado
test_endpoint "GET" "/auth/me" "" "200" "Obter dados do usuário" "auth"

echo ""
echo "📦 2. TESTE DE PRODUTOS"
echo "======================"

# 2.1 Listar produtos
test_endpoint "GET" "/products" "" "200" "Listar todos os produtos"

# Verificar se há produtos no banco
if [ -f last_response.json ]; then
    PRODUCT_COUNT=$(cat last_response.json | grep -o '"id"' | wc -l)
    echo "Produtos encontrados: $PRODUCT_COUNT"
fi

# 2.2 Buscar produto específico (assumindo que existe produto ID 1)
test_endpoint "GET" "/products/1" "" "200" "Buscar produto ID 1"

# 2.3 Produto inexistente
test_endpoint "GET" "/products/99999" "" "404" "Buscar produto inexistente"

# 2.4 Filtrar produtos por preço
test_endpoint "GET" "/products?min_price=50&max_price=200" "" "200" "Filtrar produtos por preço"

echo ""
echo "🛒 3. TESTE DE CARRINHO"
echo "======================"

# 3.1 Ver carrinho vazio
test_endpoint "GET" "/cart" "" "200" "Ver carrinho vazio"

# 3.2 Adicionar produto ao carrinho
test_endpoint "POST" "/cart" \
    "{\"product_id\":1,\"quantity\":2}" \
    "201" \
    "Adicionar produto ao carrinho"

# 3.3 Ver carrinho com itens
test_endpoint "GET" "/cart" "" "200" "Ver carrinho com itens"

# Extrair ID do item do carrinho
if [ -f last_response.json ]; then
    CART_ITEM_ID=$(cat last_response.json | grep -o '"id":[0-9]*' | head -1 | cut -d':' -f2)
    echo "Cart item ID: $CART_ITEM_ID"
fi

# 3.4 Atualizar quantidade
if [ ! -z "$CART_ITEM_ID" ]; then
    test_endpoint "PATCH" "/cart/$CART_ITEM_ID" \
        "{\"quantity\":3}" \
        "200" \
        "Atualizar quantidade do item"
fi

# 3.5 Adicionar produto inexistente
test_endpoint "POST" "/cart" \
    "{\"product_id\":99999,\"quantity\":1}" \
    "422" \
    "Adicionar produto inexistente ao carrinho"

echo ""
echo "📍 4. TESTE DE ENDEREÇO"
echo "======================"

# 4.1 Buscar CEP válido
test_endpoint "GET" "/address/cep/01310100" "" "200" "Buscar CEP válido (Av Paulista)"

# 4.2 Buscar CEP inválido
test_endpoint "GET" "/address/cep/00000000" "" "404" "Buscar CEP inválido"

# 4.3 Validar CEP
test_endpoint "POST" "/address/validate-cep" \
    "{\"cep\":\"01310-100\"}" \
    "200" \
    "Validar CEP existente"

echo ""
echo "🎟️ 5. TESTE DE CUPONS"
echo "===================="

# 5.1 Criar cupom de teste (se tivermos permissão)
COUPON_CODE="TEST$(date +%s)"
test_endpoint "POST" "/coupons" \
    "{\"code\":\"$COUPON_CODE\",\"type\":\"percentage\",\"value\":10,\"active\":true}" \
    "201" \
    "Criar cupom de teste" \
    "auth"

# 5.2 Listar cupons
test_endpoint "GET" "/coupons" "" "200" "Listar cupons disponíveis"

# 5.3 Validar cupom criado
if [ -f last_response.json ] && grep -q "$COUPON_CODE" last_response.json; then
    test_endpoint "POST" "/coupons/validate" \
        "{\"code\":\"$COUPON_CODE\",\"value\":100}" \
        "200" \
        "Validar cupom criado"
fi

# 5.4 Validar cupom inválido
test_endpoint "POST" "/coupons/validate" \
    "{\"code\":\"INVALIDO99999\",\"value\":100}" \
    "404" \
    "Validar cupom inexistente"

echo ""
echo "📋 6. TESTE DE PEDIDOS"
echo "===================="

# 6.1 Criar pedido (finalizar compra)
test_endpoint "POST" "/orders" \
    "{\"customer_name\":\"João Silva\",\"customer_email\":\"joao@test.com\",\"customer_phone\":\"11999999999\",\"customer_cpf\":\"12345678900\",\"customer_cep\":\"01310100\",\"customer_address\":\"Av Paulista 1000\",\"customer_neighborhood\":\"Bela Vista\",\"customer_city\":\"São Paulo\",\"customer_state\":\"SP\"}" \
    "201" \
    "Criar pedido finalizando carrinho"

# Extrair dados do pedido
if [ -f last_response.json ]; then
    ORDER_ID=$(cat last_response.json | grep -o '"id":[0-9]*' | head -1 | cut -d':' -f2)
    ORDER_NUMBER=$(cat last_response.json | grep -o '"order_number":"[^"]*' | cut -d'"' -f4)
    echo "Pedido criado: ID=$ORDER_ID, Número=$ORDER_NUMBER"
fi

# 6.2 Buscar pedido criado
if [ ! -z "$ORDER_ID" ]; then
    test_endpoint "GET" "/orders/$ORDER_ID" "" "200" "Buscar pedido por ID"
fi

# 6.3 Buscar pedido por número
if [ ! -z "$ORDER_NUMBER" ]; then
    test_endpoint "GET" "/orders/number/$ORDER_NUMBER" "" "200" "Buscar pedido por número"
fi

# 6.4 Listar pedidos
test_endpoint "GET" "/orders" "" "200" "Listar todos os pedidos"

# 6.5 Atualizar status do pedido
if [ ! -z "$ORDER_ID" ]; then
    test_endpoint "PATCH" "/orders/$ORDER_ID/status" \
        "{\"status\":\"processing\"}" \
        "200" \
        "Atualizar status para processing"
fi

# 6.6 Tentar criar pedido com carrinho vazio
test_endpoint "POST" "/orders" \
    "{\"customer_name\":\"João Silva\",\"customer_email\":\"joao@test.com\",\"customer_phone\":\"11999999999\",\"customer_cpf\":\"12345678900\",\"customer_cep\":\"01310100\",\"customer_address\":\"Av Paulista 1000\",\"customer_neighborhood\":\"Bela Vista\",\"customer_city\":\"São Paulo\",\"customer_state\":\"SP\"}" \
    "422" \
    "Criar pedido com carrinho vazio (deve falhar)"

echo ""
echo "🔄 7. TESTE DE REFRESH TOKEN E LOGOUT"
echo "===================================="

# 7.1 Refresh token
if [ ! -z "$REFRESH_TOKEN" ]; then
    test_endpoint "POST" "/auth/refresh" \
        "{\"refresh_token\":\"$REFRESH_TOKEN\"}" \
        "200" \
        "Renovar access token"
    
    # Atualizar tokens
    if [ -f last_response.json ]; then
        NEW_ACCESS_TOKEN=$(cat last_response.json | grep -o '"access_token":"[^"]*' | cut -d'"' -f4)
        if [ ! -z "$NEW_ACCESS_TOKEN" ]; then
            ACCESS_TOKEN=$NEW_ACCESS_TOKEN
        fi
    fi
fi

# 7.2 Logout
if [ ! -z "$REFRESH_TOKEN" ]; then
    test_endpoint "POST" "/auth/logout" \
        "{\"refresh_token\":\"$REFRESH_TOKEN\"}" \
        "200" \
        "Fazer logout" \
        "auth"
fi

# 7.3 Tentar usar token após logout
test_endpoint "GET" "/auth/me" "" "401" "Acessar com token revogado" "auth"

echo ""
echo "🚀 8. TESTE DE WEBHOOK"
echo "===================="

# 8.1 Webhook de atualização de status
if [ ! -z "$ORDER_ID" ]; then
    test_endpoint "POST" "/webhooks/order-status" \
        "{\"order_id\":$ORDER_ID,\"status\":\"shipped\",\"timestamp\":\"$(date -u +%Y-%m-%dT%H:%M:%SZ)\"}" \
        "200" \
        "Webhook - atualizar para shipped"
fi

# 8.2 Webhook com pedido inexistente
test_endpoint "POST" "/webhooks/order-status" \
    "{\"order_id\":99999,\"status\":\"shipped\",\"timestamp\":\"$(date -u +%Y-%m-%dT%H:%M:%SZ)\"}" \
    "422" \
    "Webhook - pedido inexistente"

echo ""
echo "📊 RESUMO DOS TESTES FUNCIONAIS"
echo "==============================="
echo -e "✅ Sucessos: ${GREEN}$SUCCESS${NC}"
echo -e "❌ Falhas: ${RED}$ERRORS${NC}"
echo -e "📊 Total: $((SUCCESS + ERRORS)) testes"
echo -e "📈 Taxa de sucesso: $(( (SUCCESS * 100) / (SUCCESS + ERRORS) ))%"
echo ""

# Limpar arquivos temporários
rm -f $COOKIE_JAR last_response.json

if [ $ERRORS -eq 0 ]; then
    echo -e "${GREEN}🎉 TODOS OS TESTES FUNCIONAIS PASSARAM!${NC}"
    exit 0
else
    echo -e "${YELLOW}⚠️  ALGUNS TESTES FALHARAM${NC}"
    echo "Verifique os logs acima para detalhes dos erros."
    exit 1
fi