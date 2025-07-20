#!/bin/bash

echo "🔄 TESTE DE REGRESSÃO COMPLETO"
echo "=============================="
echo ""

# Executar os testes unitários do Laravel
echo "🧪 1. EXECUTANDO TESTES UNITÁRIOS"
echo "================================"

# Verificar se estamos no container ou fora
if command -v php &> /dev/null; then
    php artisan test
else
    docker-compose exec app php artisan test
fi

echo ""
echo "🔍 2. VERIFICANDO FUNCIONALIDADES CRÍTICAS"
echo "========================================"

BASE_URL="http://localhost/api"
COOKIE_JAR="regression_cookies.txt"
rm -f $COOKIE_JAR

# Função simplificada para teste de regressão
regression_test() {
    local method=$1
    local endpoint=$2
    local expected=$3
    local description=$4
    
    response=$(curl -s -w '%{http_code}' -X $method \
        -H 'Content-Type: application/json' \
        -H 'Accept: application/json' \
        -b $COOKIE_JAR -c $COOKIE_JAR \
        "$BASE_URL$endpoint" -o /dev/null)
    
    if [ "$response" = "$expected" ]; then
        echo "✅ $description"
        return 0
    else
        echo "❌ $description (Expected: $expected, Got: $response)"
        return 1
    fi
}

# Testes de endpoints críticos
echo -e "\nEndpoints básicos:"
regression_test "GET" "/health" "200" "Health check"
regression_test "GET" "/products" "200" "Listar produtos"
regression_test "GET" "/cart" "200" "Ver carrinho"
regression_test "GET" "/orders" "200" "Listar pedidos"
regression_test "GET" "/coupons" "200" "Listar cupons"

echo -e "\nEndpoints de erro:"
regression_test "GET" "/products/99999" "404" "Produto inexistente retorna 404"
regression_test "GET" "/orders/99999" "404" "Pedido inexistente retorna 404"
regression_test "GET" "/address/cep/00000000" "404" "CEP inválido retorna 404"

echo -e "\nAutenticação:"
regression_test "GET" "/auth/me" "401" "Rota protegida sem token retorna 401"
regression_test "POST" "/auth/login" "422" "Login sem dados retorna 422"

echo ""
echo "🔐 3. TESTE DE AUTENTICAÇÃO JWT"
echo "==============================="

# Testar fluxo completo de autenticação
EMAIL="regression_test_$(date +%s)@test.com"
PASSWORD="Test123456"

# Registro
echo -n "Testando registro... "
REGISTER_RESPONSE=$(curl -s -X POST \
    -H 'Content-Type: application/json' \
    -H 'Accept: application/json' \
    -b $COOKIE_JAR -c $COOKIE_JAR \
    -d "{\"name\":\"Regression Test\",\"email\":\"$EMAIL\",\"password\":\"$PASSWORD\",\"password_confirmation\":\"$PASSWORD\"}" \
    "$BASE_URL/auth/register")

if echo "$REGISTER_RESPONSE" | grep -q "accessToken"; then
    echo "✅ OK"
    ACCESS_TOKEN=$(echo "$REGISTER_RESPONSE" | grep -o '"accessToken":"[^"]*' | cut -d'"' -f4)
else
    echo "❌ FALHOU"
fi

# Login
echo -n "Testando login... "
LOGIN_RESPONSE=$(curl -s -X POST \
    -H 'Content-Type: application/json' \
    -H 'Accept: application/json' \
    -b $COOKIE_JAR -c $COOKIE_JAR \
    -d "{\"email\":\"$EMAIL\",\"password\":\"$PASSWORD\"}" \
    "$BASE_URL/auth/login")

if echo "$LOGIN_RESPONSE" | grep -q "accessToken"; then
    echo "✅ OK"
    if [ -z "$ACCESS_TOKEN" ]; then
        ACCESS_TOKEN=$(echo "$LOGIN_RESPONSE" | grep -o '"accessToken":"[^"]*' | cut -d'"' -f4)
    fi
else
    echo "❌ FALHOU"
fi

# Me endpoint com token
if [ ! -z "$ACCESS_TOKEN" ]; then
    echo -n "Testando endpoint autenticado... "
    ME_RESPONSE=$(curl -s -w '%{http_code}' \
        -H "Authorization: Bearer $ACCESS_TOKEN" \
        -H 'Accept: application/json' \
        -b $COOKIE_JAR -c $COOKIE_JAR \
        "$BASE_URL/auth/me" -o /dev/null)
    
    if [ "$ME_RESPONSE" = "200" ]; then
        echo "✅ OK"
    else
        echo "❌ FALHOU (Status: $ME_RESPONSE)"
    fi
fi

echo ""
echo "🛒 4. TESTE DE CARRINHO E SESSÃO"
echo "==============================="

# Adicionar ao carrinho
echo -n "Adicionando produto ao carrinho... "
CART_ADD=$(curl -s -w '%{http_code}' -X POST \
    -H 'Content-Type: application/json' \
    -b $COOKIE_JAR -c $COOKIE_JAR \
    -d '{"product_id":1,"quantity":1}' \
    "$BASE_URL/cart" -o /dev/null)

if [ "$CART_ADD" = "201" ]; then
    echo "✅ OK"
else
    echo "❌ FALHOU (Status: $CART_ADD)"
fi

# Verificar persistência
echo -n "Verificando persistência do carrinho... "
CART_VIEW=$(curl -s -X GET \
    -H 'Accept: application/json' \
    -b $COOKIE_JAR -c $COOKIE_JAR \
    "$BASE_URL/cart")

if echo "$CART_VIEW" | grep -q "totalItems"; then
    ITEMS=$(echo "$CART_VIEW" | grep -o '"totalItems":[0-9]*' | cut -d':' -f2)
    if [ "$ITEMS" -gt "0" ]; then
        echo "✅ OK ($ITEMS items)"
    else
        echo "❌ Carrinho vazio"
    fi
else
    echo "❌ Resposta inválida"
fi

echo ""
echo "📊 5. TESTE DE INTEGRIDADE DO BANCO"
echo "=================================="

# Verificar conexão com banco
echo -n "Verificando conexão com MySQL... "
if docker-compose exec mysql mysql -u montink -ppassword -e "SELECT 1" montink_erp &>/dev/null; then
    echo "✅ OK"
else
    echo "❌ FALHOU"
fi

# Verificar tabelas essenciais
echo -e "\nVerificando tabelas essenciais:"
for table in products stock orders order_items coupons users refresh_tokens; do
    echo -n "  - Tabela $table... "
    if docker-compose exec mysql mysql -u montink -ppassword -e "SHOW TABLES LIKE '$table'" montink_erp 2>/dev/null | grep -q "$table"; then
        echo "✅ existe"
    else
        echo "❌ não encontrada"
    fi
done

echo ""
echo "🔍 6. VERIFICAÇÃO DE MIGRATIONS"
echo "=============================="

echo "Verificando status das migrations:"
if command -v php &> /dev/null; then
    php artisan migrate:status | tail -20
else
    docker-compose exec app php artisan migrate:status | tail -20
fi

echo ""
echo "✅ 7. TESTE DE EDGE CASES"
echo "========================"

# Produto inexistente no carrinho
echo -n "Produto inexistente no carrinho... "
EDGE1=$(curl -s -w '%{http_code}' -X POST \
    -H 'Content-Type: application/json' \
    -b $COOKIE_JAR -c $COOKIE_JAR \
    -d '{"product_id":99999,"quantity":1}' \
    "$BASE_URL/cart" -o /dev/null)
[ "$EDGE1" = "404" ] && echo "✅ OK (404)" || echo "❌ FALHOU (Status: $EDGE1)"

# Pedido com carrinho vazio
echo -n "Pedido com carrinho vazio... "
# Primeiro limpar o carrinho
curl -s -X DELETE -b $COOKIE_JAR -c $COOKIE_JAR "$BASE_URL/cart" -o /dev/null

EDGE2=$(curl -s -w '%{http_code}' -X POST \
    -H 'Content-Type: application/json' \
    -b $COOKIE_JAR -c $COOKIE_JAR \
    -d '{"customer_name":"Test","customer_email":"test@test.com","customer_phone":"11999999999","customer_cpf":"12345678900","customer_cep":"01310100","customer_address":"Test","customer_neighborhood":"Test","customer_city":"SP","customer_state":"SP"}' \
    "$BASE_URL/orders" -o /dev/null)
[ "$EDGE2" = "422" ] && echo "✅ OK (422)" || echo "❌ FALHOU (Status: $EDGE2)"

# Cancelar pedido enviado
echo -n "Tentativa de cancelar pedido shipped... "
# Este teste depende de ter um pedido com status shipped
echo "⏭️  SKIP (requer pedido específico)"

# SKU duplicado
echo -n "Produto com SKU duplicado... "
# Este teste requer permissão de admin
echo "⏭️  SKIP (requer permissão admin)"

echo ""
echo "🔄 8. TESTE DE CONCORRÊNCIA"
echo "=========================="

echo "Testando requisições simultâneas ao carrinho..."

# Adicionar produto ao carrinho 10 vezes em paralelo
for i in {1..10}; do
    curl -s -X POST \
        -H 'Content-Type: application/json' \
        -b $COOKIE_JAR -c $COOKIE_JAR \
        -d '{"product_id":1,"quantity":1}' \
        "$BASE_URL/cart" -o /dev/null &
done

wait

# Verificar resultado
FINAL_CART=$(curl -s -X GET \
    -H 'Accept: application/json' \
    -b $COOKIE_JAR -c $COOKIE_JAR \
    "$BASE_URL/cart")

if echo "$FINAL_CART" | grep -q "totalItems"; then
    TOTAL=$(echo "$FINAL_CART" | grep -o '"totalItems":[0-9]*' | cut -d':' -f2)
    echo "✅ Carrinho após concorrência: $TOTAL items"
else
    echo "❌ Erro ao verificar carrinho"
fi

# Limpar arquivos temporários
rm -f $COOKIE_JAR

echo ""
echo "📊 RESUMO DA REGRESSÃO"
echo "===================="
echo "✅ Testes de regressão concluídos!"
echo ""
echo "⚠️  IMPORTANTE: Verifique os logs acima para garantir que:"
echo "  1. Todos os testes unitários passaram"
echo "  2. Endpoints críticos estão funcionando"
echo "  3. Autenticação JWT está operacional"
echo "  4. Carrinho mantém sessão corretamente"
echo "  5. Banco de dados está íntegro"
echo "  6. Não há regressões nos edge cases"