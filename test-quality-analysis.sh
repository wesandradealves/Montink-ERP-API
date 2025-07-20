#!/bin/bash

echo "🔍 ANÁLISE DE QUALIDADE E REDUNDÂNCIA"
echo "===================================="
echo ""

# Diretórios para análise
APP_DIR="app"
MODULES_DIR="app/Modules"

# Contadores
TOTAL_FILES=0
DUPLICATE_LOGIC=0
DRY_VIOLATIONS=0
QUALITY_ISSUES=0

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo "📊 1. ANÁLISE DE REDUNDÂNCIA E DRY"
echo "================================="

# 1.1 Verificar métodos duplicados em UseCases
echo -e "\n${BLUE}Verificando duplicação em UseCases...${NC}"
find $MODULES_DIR -name "*UseCase.php" -type f | while read file; do
    # Verificar métodos comuns que podem estar duplicados
    for method in "validateStock" "findOrFail" "executeInTransaction" "applyFilters"; do
        if grep -q "function $method" "$file"; then
            echo -e "${YELLOW}⚠${NC} Possível duplicação: $method em $(basename $file)"
            ((DRY_VIOLATIONS++))
        fi
    done
done

# 1.2 Verificar validações duplicadas
echo -e "\n${BLUE}Verificando validações duplicadas...${NC}"
find $MODULES_DIR -name "*Request.php" -type f | while read file; do
    # Contar regras de validação repetidas
    grep -E "'required'|'string'|'numeric'" "$file" | sort | uniq -c | while read count rule; do
        if [ $count -gt 3 ]; then
            echo -e "${YELLOW}⚠${NC} Validação repetida $count vezes em $(basename $file): $rule"
            ((DRY_VIOLATIONS++))
        fi
    done
done

# 1.3 Verificar uso de traits e base classes
echo -e "\n${BLUE}Verificando uso de classes base e traits...${NC}"
echo -n "Controllers usando BaseApiController: "
grep -l "extends BaseApiController" $MODULES_DIR/*/Api/Controllers/*.php 2>/dev/null | wc -l

echo -n "Requests usando BaseFormRequest: "
grep -l "extends BaseFormRequest" $MODULES_DIR/*/Api/Requests/*.php 2>/dev/null | wc -l

echo -n "Models usando BaseModel: "
grep -l "extends BaseModel" $MODULES_DIR/*/Models/*.php 2>/dev/null | wc -l

echo -n "DTOs usando BaseDTO: "
grep -l "extends BaseDTO" $MODULES_DIR/*/DTOs/*.php 2>/dev/null | wc -l

echo ""
echo "📏 2. ANÁLISE DE COMPLEXIDADE"
echo "============================"

# 2.1 Métodos muito longos
echo -e "\n${BLUE}Verificando métodos com mais de 50 linhas...${NC}"
find $APP_DIR -name "*.php" -type f | while read file; do
    awk '/function/ {start=NR} /^[[:space:]]*}/ {if(start && NR-start>50) print FILENAME":"start"-"NR" ("NR-start" lines)"; start=0}' "$file"
done | grep -v "vendor" | head -10

# 2.2 Classes muito grandes
echo -e "\n${BLUE}Verificando classes com mais de 300 linhas...${NC}"
find $APP_DIR -name "*.php" -type f -exec wc -l {} + | awk '$1 > 300 {print $2 " (" $1 " lines)"}' | grep -v "vendor" | head -10

echo ""
echo "🔒 3. ANÁLISE DE SEGURANÇA E BOAS PRÁTICAS"
echo "========================================="

# 3.1 Verificar uso direto de $_POST, $_GET
echo -e "\n${BLUE}Verificando uso direto de superglobals...${NC}"
grep -rn '\$_\(POST\|GET\|REQUEST\)' $APP_DIR --include="*.php" | grep -v "vendor" | wc -l

# 3.2 Verificar SQL raw sem bindings
echo -e "\n${BLUE}Verificando queries SQL sem bindings...${NC}"
grep -rn "DB::raw\|::raw(" $APP_DIR --include="*.php" | grep -v "bindings\|?" | wc -l

# 3.3 Verificar hardcoded secrets
echo -e "\n${BLUE}Verificando possíveis secrets hardcoded...${NC}"
grep -rn "password\s*=\s*['\"]" $APP_DIR --include="*.php" | grep -v "env\|config\|test" | wc -l

echo ""
echo "🧩 4. ANÁLISE DE PADRÕES ARQUITETURAIS"
echo "======================================"

# 4.1 Verificar separação de responsabilidades
echo -e "\n${BLUE}Verificando separação de responsabilidades...${NC}"

# Controllers fazendo lógica de negócio
echo -n "Controllers com lógica de negócio: "
grep -l "DB::\|->save()\|->create(" $MODULES_DIR/*/Api/Controllers/*.php 2>/dev/null | wc -l

# UseCases fazendo validação
echo -n "UseCases fazendo validação de request: "
grep -l "request->\|Request::" $MODULES_DIR/*/UseCases/*.php 2>/dev/null | wc -l

# Models com lógica complexa
echo -n "Models com métodos complexos (>20 linhas): "
find $MODULES_DIR/*/Models -name "*.php" -exec awk '/function/ {start=NR} /^[[:space:]]*}/ {if(start && NR-start>20) {found=1; exit}} END {if(found) print FILENAME}' {} \; | wc -l

echo ""
echo "📈 5. MÉTRICAS DE QUALIDADE"
echo "========================="

# 5.1 Contar arquivos
TOTAL_PHP_FILES=$(find $APP_DIR -name "*.php" -type f | grep -v "vendor" | wc -l)
TOTAL_TEST_FILES=$(find tests -name "*Test.php" -type f 2>/dev/null | wc -l)
TOTAL_TRAITS=$(find $APP_DIR -name "*Trait.php" -type f | wc -l)
TOTAL_INTERFACES=$(find $APP_DIR -name "*Interface.php" -type f | wc -l)

echo "📁 Arquivos PHP: $TOTAL_PHP_FILES"
echo "🧪 Arquivos de teste: $TOTAL_TEST_FILES"
echo "🔧 Traits: $TOTAL_TRAITS"
echo "📐 Interfaces: $TOTAL_INTERFACES"

# 5.2 Verificar cobertura de ResponseMessage
echo -e "\n${BLUE}Uso do sistema de mensageria:${NC}"
TOTAL_MESSAGES=$(grep -r "ResponseMessage::" $APP_DIR --include="*.php" | wc -l)
HARDCODED_MESSAGES=$(grep -rE "response.*['\"].*sucesso|erro|criado|atualizado|excluído" $APP_DIR --include="*.php" | grep -v "ResponseMessage" | wc -l)

echo "✅ Mensagens usando ResponseMessage: $TOTAL_MESSAGES"
echo "❌ Mensagens hardcoded: $HARDCODED_MESSAGES"

# 5.3 Análise de imports não utilizados
echo -e "\n${BLUE}Verificando imports não utilizados:${NC}"
find $APP_DIR -name "*.php" -type f | head -20 | while read file; do
    # Extrair uses
    uses=$(grep "^use " "$file" | sed 's/use \(.*\);/\1/' | sed 's/.*\\//')
    unused=0
    for use in $uses; do
        # Verificar se a classe é usada no arquivo
        if ! grep -q "$use" "$file" | grep -v "^use "; then
            ((unused++))
        fi
    done
    if [ $unused -gt 0 ]; then
        echo "$(basename $file): $unused imports possivelmente não utilizados"
    fi
done

echo ""
echo "🎯 6. RECOMENDAÇÕES DE MELHORIA"
echo "==============================="

# Análise final e recomendações
if [ $HARDCODED_MESSAGES -gt 10 ]; then
    echo -e "${YELLOW}⚠${NC} Migrar mensagens hardcoded para ResponseMessage enum"
fi

CONTROLLERS_WITHOUT_BASE=$(find $MODULES_DIR/*/Api/Controllers -name "*.php" -exec grep -L "extends BaseApiController" {} \; | wc -l)
if [ $CONTROLLERS_WITHOUT_BASE -gt 0 ]; then
    echo -e "${YELLOW}⚠${NC} $CONTROLLERS_WITHOUT_BASE controllers não estendem BaseApiController"
fi

MODELS_WITHOUT_BASE=$(find $MODULES_DIR/*/Models -name "*.php" -exec grep -L "extends BaseModel" {} \; | wc -l)
if [ $MODELS_WITHOUT_BASE -gt 0 ]; then
    echo -e "${YELLOW}⚠${NC} $MODELS_WITHOUT_BASE models não estendem BaseModel"
fi

# Score final
QUALITY_SCORE=$((100 - HARDCODED_MESSAGES - CONTROLLERS_WITHOUT_BASE - MODELS_WITHOUT_BASE))
if [ $QUALITY_SCORE -lt 0 ]; then QUALITY_SCORE=0; fi

echo ""
echo "📊 SCORE DE QUALIDADE"
echo "===================="
echo -e "Score DRY: ${GREEN}$((100 - DRY_VIOLATIONS * 5))%${NC}"
echo -e "Score Arquitetura: ${GREEN}$((100 - CONTROLLERS_WITHOUT_BASE * 10))%${NC}"
echo -e "Score Mensageria: ${GREEN}$((100 * TOTAL_MESSAGES / (TOTAL_MESSAGES + HARDCODED_MESSAGES)))%${NC}"
echo -e "\n${BLUE}Score Geral de Qualidade: $QUALITY_SCORE/100${NC}"

echo ""
echo "✅ Análise de qualidade concluída!"