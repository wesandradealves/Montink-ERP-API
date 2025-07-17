.PHONY: help build up down restart logs shell test migrate seed fresh install

help: ## Exibe esta ajuda
	@echo "Comandos disponíveis:"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

build: ## Constrói as imagens Docker
	docker compose build

up: ## Inicia os containers
	docker compose up -d

down: ## Para os containers
	docker compose down

restart: ## Reinicia os containers
	docker compose restart

logs: ## Exibe os logs dos containers
	docker compose logs -f

logs-app: ## Exibe os logs do container da aplicação
	docker compose logs -f app

shell: ## Acessa o shell do container da aplicação
	docker compose exec app bash

shell-mysql: ## Acessa o shell do MySQL
	docker compose exec mysql mysql -u montink -p montink_erp

install: ## Instala as dependências do projeto
	docker compose exec app composer install

update: ## Atualiza as dependências
	docker compose exec app composer update

migrate: ## Executa as migrations
	docker compose exec app php artisan migrate

migrate-fresh: ## Recria o banco com migrations
	docker compose exec app php artisan migrate:fresh

seed: ## Executa os seeders
	docker compose exec app php artisan db:seed

fresh: ## Recria banco e executa seeders
	docker compose exec app php artisan migrate:fresh --seed

test: ## Executa os testes
	docker compose exec app php artisan test

test-coverage: ## Executa testes com coverage
	docker compose exec app php artisan test --coverage

artisan: ## Executa comando artisan (use: make artisan CMD="comando")
	docker compose exec app php artisan $(CMD)

composer: ## Executa comando composer (use: make composer CMD="comando")
	docker compose exec app composer $(CMD)

cache-clear: ## Limpa todos os caches
	docker compose exec app php artisan cache:clear
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan route:clear
	docker compose exec app php artisan view:clear

optimize: ## Otimiza a aplicação para produção
	docker compose exec app php artisan config:cache
	docker compose exec app php artisan route:cache
	docker compose exec app php artisan view:cache

queue-work: ## Executa o worker de filas
	docker compose exec app php artisan queue:work

queue-restart: ## Reinicia os workers de fila
	docker compose exec app php artisan queue:restart

setup: build up install migrate seed ## Setup completo do projeto

check-commits: ## Verifica se há menções de IA nos commits
	@echo "Verificando commits por menções de IA..."
	@if git log --oneline | grep -i -E "(claude|ai|artificial|inteligencia|gpt|chatgpt|openai|anthropic)" > /dev/null 2>&1; then \
		echo "❌ ERRO: Encontradas menções de IA nos commits!"; \
		echo "Commits problemáticos:"; \
		git log --oneline | grep -i -E "(claude|ai|artificial|inteligencia|gpt|chatgpt|openai|anthropic)"; \
		exit 1; \
	else \
		echo "✅ Nenhuma menção de IA encontrada nos commits"; \
	fi

check-code: ## Verifica se há comentários no código
	@echo "Verificando código por comentários..."
	@if find app -name "*.php" -exec grep -l "\/\/" {} \; -o -name "*.php" -exec grep -l "\/\*" {} \; | head -1 > /dev/null; then \
		echo "❌ AVISO: Comentários encontrados no código!"; \
		find app -name "*.php" -exec grep -l "\/\/" {} \; -o -name "*.php" -exec grep -l "\/\*" {} \; | head -5; \
	else \
		echo "✅ Nenhum comentário encontrado no código"; \
	fi

commit-check: check-commits check-code ## Executa todas as verificações antes do commit