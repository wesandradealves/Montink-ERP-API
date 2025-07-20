#!/bin/bash

echo "🔍 ANÁLISE DE QUALIDADE MELHORADA"
echo "================================="
echo ""

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo "📊 1. USO CORRETO DO SISTEMA DE MENSAGERIA"
echo "=========================================="

# Contar uso de ResponseMessage
RESPONSE_MESSAGE_COUNT=$(grep -r "ResponseMessage::" app --include="*.php" | wc -l)
echo -e "✅ Chamadas para ResponseMessage: ${GREEN}$RESPONSE_MESSAGE_COUNT${NC}"

# Procurar por mensagens hardcoded reais (excluindo comentários e Swagger)
echo -e "\n${BLUE}Verificando mensagens hardcoded reais...${NC}"
HARDCODED_MESSAGES=$(grep -rn "return.*response.*['\"].*['\"]" app --include="*.php" | grep -v "ResponseMessage\|@OA\|/\*\*\|//" | wc -l)
echo -e "Mensagens hardcoded em returns: ${GREEN}$HARDCODED_MESSAGES${NC}"

# Verificar exceções com mensagens hardcoded
HARDCODED_EXCEPTIONS=$(grep -rn "throw new.*Exception(['\"]" app --include="*.php" | grep -v "ResponseMessage" | wc -l)
echo -e "Exceções com mensagens hardcoded: ${GREEN}$HARDCODED_EXCEPTIONS${NC}"

echo ""
echo "📈 2. COBERTURA DE PADRÕES ARQUITETURAIS"
echo "========================================"

# Controllers
TOTAL_CONTROLLERS=$(find app/Modules/*/Api/Controllers -name "*Controller.php" 2>/dev/null | wc -l)
CONTROLLERS_WITH_BASE=$(grep -l "extends BaseApiController" app/Modules/*/Api/Controllers/*.php 2>/dev/null | wc -l)
echo -e "Controllers usando BaseApiController: ${GREEN}$CONTROLLERS_WITH_BASE/$TOTAL_CONTROLLERS${NC}"

# Requests
TOTAL_REQUESTS=$(find app/Modules/*/Api/Requests -name "*Request.php" 2>/dev/null | wc -l)
REQUESTS_WITH_BASE=$(grep -l "extends BaseFormRequest" app/Modules/*/Api/Requests/*.php 2>/dev/null | wc -l)
echo -e "Requests usando BaseFormRequest: ${GREEN}$REQUESTS_WITH_BASE/$TOTAL_REQUESTS${NC}"

# Models
TOTAL_MODELS=$(find app/Modules/*/Models -name "*.php" 2>/dev/null | wc -l)
MODELS_WITH_BASE=$(grep -l "extends BaseModel" app/Modules/*/Models/*.php 2>/dev/null | wc -l)
echo -e "Models usando BaseModel: ${GREEN}$MODELS_WITH_BASE/$TOTAL_MODELS${NC}"

# DTOs
TOTAL_DTOS=$(find app/Modules/*/DTOs -name "*DTO.php" 2>/dev/null | wc -l)
DTOS_WITH_BASE=$(grep -l "extends BaseDTO" app/Modules/*/DTOs/*.php 2>/dev/null | wc -l)
echo -e "DTOs usando BaseDTO: ${GREEN}$DTOS_WITH_BASE/$TOTAL_DTOS${NC}"

echo ""
echo "🔒 3. SEGURANÇA E BOAS PRÁTICAS"
echo "==============================="

# SQL Injection
SQL_RAW=$(grep -r "DB::raw\|->raw(" app --include="*.php" | grep -v "?" | wc -l)
echo -e "Queries SQL potencialmente inseguras: ${GREEN}$SQL_RAW${NC}"

# Hardcoded passwords
HARDCODED_PASS=$(grep -rn "password.*=.*['\"]" app --include="*.php" | grep -v "env\|config\|test\|fake" | wc -l)
echo -e "Possíveis senhas hardcoded: ${GREEN}$HARDCODED_PASS${NC}"

# Direct superglobal access
SUPERGLOBALS=$(grep -r '\$_\(GET\|POST\|REQUEST\|SERVER\|COOKIE\|SESSION\)' app --include="*.php" | wc -l)
echo -e "Uso direto de superglobals: ${GREEN}$SUPERGLOBALS${NC}"

echo ""
echo "✅ 4. PRINCÍPIOS DRY"
echo "==================="

# Verificar métodos duplicados
echo -e "\n${BLUE}Verificando possíveis duplicações...${NC}"

# Contar ocorrências de métodos comuns
for method in "find" "create" "update" "delete" "validate"; do
    count=$(grep -r "function $method" app/Modules --include="*.php" | wc -l)
    if [ $count -gt 5 ]; then
        echo -e "${YELLOW}⚠${NC} Método '$method' aparece $count vezes (possível candidato para trait/base class)"
    fi
done

echo ""
echo "📊 5. MÉTRICAS DE QUALIDADE FINAL"
echo "================================"

# Calcular scores
ARCHITECTURE_SCORE=$((100 * (CONTROLLERS_WITH_BASE + REQUESTS_WITH_BASE + MODELS_WITH_BASE + DTOS_WITH_BASE) / (TOTAL_CONTROLLERS + TOTAL_REQUESTS + TOTAL_MODELS + TOTAL_DTOS)))
SECURITY_SCORE=$((100 - SQL_RAW * 10 - HARDCODED_PASS * 20 - SUPERGLOBALS * 5))
if [ $SECURITY_SCORE -lt 0 ]; then SECURITY_SCORE=0; fi

MESSAGE_SCORE=$((100 * RESPONSE_MESSAGE_COUNT / (RESPONSE_MESSAGE_COUNT + HARDCODED_MESSAGES + HARDCODED_EXCEPTIONS + 1)))

echo -e "\n${BLUE}SCORES DE QUALIDADE:${NC}"
echo -e "📐 Arquitetura: ${GREEN}$ARCHITECTURE_SCORE%${NC}"
echo -e "🔒 Segurança: ${GREEN}$SECURITY_SCORE%${NC}"
echo -e "💬 Sistema de Mensagens: ${GREEN}$MESSAGE_SCORE%${NC}"

OVERALL_SCORE=$(((ARCHITECTURE_SCORE + SECURITY_SCORE + MESSAGE_SCORE) / 3))
echo -e "\n${BLUE}⭐ SCORE GERAL: $OVERALL_SCORE/100${NC}"

echo ""
echo "🎯 RECOMENDAÇÕES:"
echo "================"

if [ $HARDCODED_MESSAGES -gt 0 ] || [ $HARDCODED_EXCEPTIONS -gt 0 ]; then
    echo -e "${YELLOW}⚠${NC} Migrar as $((HARDCODED_MESSAGES + HARDCODED_EXCEPTIONS)) mensagens hardcoded restantes para ResponseMessage"
fi

if [ $SQL_RAW -gt 0 ]; then
    echo -e "${YELLOW}⚠${NC} Revisar queries SQL raw para usar bindings"
fi

if [ $ARCHITECTURE_SCORE -lt 100 ]; then
    echo -e "${YELLOW}⚠${NC} Garantir que todas as classes estendam suas respectivas classes base"
fi

echo -e "\n${GREEN}✅ Análise concluída!${NC}"