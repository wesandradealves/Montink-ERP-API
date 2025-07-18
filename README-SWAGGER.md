# Configuração do Swagger

## Instalação dos Assets

Após clonar o repositório, execute os seguintes comandos para configurar os assets do Swagger:

```bash
# Instalar dependências
composer install

# Copiar assets do Swagger UI
mkdir -p public/vendor/swagger-api/swagger-ui
cp -r vendor/swagger-api/swagger-ui/dist public/vendor/swagger-api/swagger-ui/

# Gerar documentação
php artisan l5-swagger:generate
```

## URLs Disponíveis

- `http://localhost/docs` - Interface Swagger UI
- `http://localhost/docs.json` - Especificação OpenAPI JSON
- `http://localhost/` - Redireciona para `/docs`
- `http://localhost/api/` - Redireciona para `/docs`

## Comandos Úteis

```bash
# Regenerar documentação
php artisan l5-swagger:generate

# Limpar cache
php artisan cache:clear
```

## Estrutura

- `config/l5-swagger.php` - Configuração do Swagger
- `resources/views/vendor/l5-swagger/` - Views customizadas
- `storage/api-docs/` - Documentação JSON gerada
- `app/Http/Controllers/Schemas/` - Schemas reutilizáveis