# Guia de Implementação - Clean Architecture + DDD em Laravel

Este documento apresenta como implementar uma arquitetura limpa e orientada a domínio em Laravel, baseada nos padrões observados e adaptada para um novo projeto.

## 🏗️ Fundamentos da Arquitetura

### Princípios Core
- **Separação de Interesses**: Cada camada tem responsabilidade única
- **Inversão de Dependências**: Dependências apontam para o centro (domínio)
- **Independência de Framework**: Lógica de negócio não depende do Laravel
- **Testabilidade**: Código facilmente testável em isolamento

### Fluxo de Dependências
```
Apresentação → Aplicação → Domínio ← Infraestrutura
     ↓             ↓          ↑            ↓
Controllers → Use Cases → Entidades ← Repositories
```

## 📁 Estrutura de Diretórios Proposta (Baseada no Dourado Dashboard)

### Estrutura Principal
```
app/
├── Domain/                          # Camada de Domínio (Clean Architecture)
├── Infrastructure/                  # Camada de Infraestrutura  
├── Modules/                         # Módulos de Funcionalidades
├── Http/                           # Camada de Apresentação HTTP
├── Functions/                      # Processamento assíncrono (Jobs/Queues)
├── Common/                         # Código compartilhado
└── Tools/                          # Ferramentas e templates
```

### Detalhamento das Camadas

#### Domain/ - Camada de Domínio
```
Domain/
├── Commons/                        # Elementos compartilhados do domínio
│   ├── Enums/                     # Enumerações de negócio
│   ├── Interfaces/                # Interfaces globais
│   ├── ValueObjects/              # Objetos de valor base
│   └── Exceptions/                # Exceções de domínio
├── Entities/                      # Entidades base (Models Eloquent)
├── Interfaces/                    # Contratos principais
│   ├── Auth/                      # Contratos de autenticação
│   ├── Repositories/              # Interfaces de repositórios
│   ├── External/                  # Serviços externos
│   ├── UseCases/                  # Contratos de casos de uso
│   └── Services/                  # Serviços de domínio
├── Repositories/                  # Repositórios específicos
├── Types/                         # Definições de tipos
└── Utils/                         # Utilitários de domínio
```

#### Infrastructure/ - Camada de Infraestrutura
```
Infrastructure/
├── Config/                        # Configurações da aplicação
├── Database/                      # Banco de dados
│   ├── Migrations/               # Migrações Laravel
│   └── Factories/                # Factories para testes
├── External/                      # Integrações externas
│   ├── Http/                     # Clientes HTTP
│   ├── Payment/                  # Gateways de pagamento
│   ├── Storage/                  # Serviços de armazenamento
│   └── Email/                    # Serviços de email
├── Providers/                     # Service Providers
├── Repositories/                  # Implementações de repositórios
├── Auth/                         # Implementações de autenticação
└── Utils/                        # Utilitários de infraestrutura
```

#### Modules/ - Módulos de Funcionalidades
```
Modules/
└── [ModuleName]/                  # Ex: User, Product, Order
    ├── Api/                       # Interface API do módulo
    │   ├── Controllers/           # Controllers REST específicos
    │   ├── Requests/              # Form Requests
    │   ├── Resources/             # API Resources
    │   └── Middleware/            # Middlewares específicos
    ├── UseCases/                  # Casos de uso do módulo
    ├── Services/                  # Serviços específicos (se necessário)
    ├── DTOs/                      # DTOs específicos do módulo
    ├── Models/                    # Models Eloquent específicos
    └── Providers/                 # Service Provider do módulo
```

#### Http/ - Camada de Apresentação
```
Http/
├── Controllers/                   # Controllers globais/base
├── Middleware/                    # Middlewares globais
├── Requests/                      # Form Requests globais
├── Resources/                     # API Resources globais
└── Kernel.php                     # HTTP Kernel
```

#### Functions/ - Processamento Assíncrono
```
Functions/
├── Jobs/                          # Jobs do Laravel Queue
├── Processors/                    # Processadores de arquivos/dados
├── Events/                        # Event handlers
└── Listeners/                     # Event listeners
```

#### Common/ - Código Compartilhado
```
Common/
├── Base/                          # Classes base
│   ├── BaseModel.php             # Model base
│   ├── BaseController.php        # Controller base
│   ├── BaseRequest.php           # Request base
│   └── BaseResource.php          # Resource base
├── Traits/                        # Traits reutilizáveis
├── Helpers/                       # Helper functions
└── Constants/                     # Constantes globais
```

## 🏗️ Padrões Observados no Dourado Dashboard

### Organização por Módulos
O projeto Dourado organiza funcionalidades em módulos independentes, cada um com sua própria estrutura:

#### Exemplos de Módulos Reais:
- **Auth/** - Autenticação com 2FA
- **User/** - Gestão de usuários  
- **Tokens/** - Gestão de tokens
- **Transactions/** - Transações financeiras
- **Wallet/** - Carteiras digitais
- **Upload/** - Upload de arquivos
- **Categories/** - Gestão de categorias

#### Estrutura Padrão de Módulo:
```
auth/
├── api/
│   ├── controller/
│   │   └── auth.controller.ts
│   └── dto/
│       ├── sign-in.dto.ts
│       ├── sign-up.dto.ts
│       └── refresh-token.dto.ts
├── use-cases/
│   ├── sign-in.use-case.ts
│   ├── sign-up.use-case.ts
│   └── refresh-token.use-case.ts
└── auth.module.ts
```

### Nomenclatura Consistente
- **Arquivos:** kebab-case (`sign-in.use-case.ts`)
- **Classes:** PascalCase (`SignInUseCase`)
- **Pastas:** kebab-case (`categories-list/`)
- **DTOs:** Sufixo `.dto.ts`
- **Use Cases:** Sufixo `.use-case.ts`
- **Controllers:** Sufixo `.controller.ts`

### Camada de Domínio Bem Estruturada
```
domain/
├── entities/           # BaseEntity + entidades específicas
├── interfaces/
│   ├── auth/          # Contratos de autenticação
│   ├── repositories/  # Interfaces de repositórios
│   ├── use-cases/     # Contratos de casos de uso
│   └── external/      # Serviços externos
├── commons/
│   ├── enum/          # Enumerações (USER_ROLES, STATUS, etc.)
│   └── interfaces/    # Interfaces compartilhadas
└── types/             # Tipos TypeScript customizados
```

### Infraestrutura Organizada
```
infrastructure/
├── config/            # Configurações (database, aws, etc.)
├── database/          # Migrações e configuração TypeORM
├── external/          # Integrações (BDM, Core Backend)
├── providers/         # AWS services (Cognito, S3, SES)
└── repositories/      # Implementações concretas
```

### Padrões de Autenticação Multi-Layer
- **Guards:** JWT + 2FA guards
- **Strategies:** Passport strategies
- **Roles:** Sistema de perfis hierárquicos
- **MFA:** Two-Factor Authentication integrado

## 🎯 Implementação Prática

### 1. Domain Layer - A Base de Tudo

#### Entity (Entidade de Domínio)
```php
<?php

namespace App\Domain\Product\Entities;

use App\Domain\Product\ValueObjects\ProductId;
use App\Domain\Product\ValueObjects\Money;
use App\Domain\Product\Rules\PriceRule;
use App\Domain\Product\Events\ProductCreated;
use App\Domain\Product\Events\PriceChanged;

class Product
{
    private array $events = [];
    
    public function __construct(
        private ProductId $id,
        private string $name,
        private string $description,
        private Money $price,
        private bool $active = true,
        private ?\DateTimeImmutable $createdAt = null,
    ) {
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
        $this->raise(new ProductCreated($this));
    }
    
    public static function create(
        string $name,
        string $description,
        Money $price
    ): self {
        PriceRule::validate($price);
        
        return new self(
            id: ProductId::generate(),
            name: $name,
            description: $description,
            price: $price
        );
    }
    
    public function changePrice(Money $newPrice): void
    {
        PriceRule::validate($newPrice);
        
        if (!$this->price->equals($newPrice)) {
            $oldPrice = $this->price;
            $this->price = $newPrice;
            $this->raise(new PriceChanged($this, $oldPrice, $newPrice));
        }
    }
    
    public function deactivate(): void
    {
        $this->active = false;
    }
    
    // Getters
    public function getId(): ProductId { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getPrice(): Money { return $this->price; }
    public function isActive(): bool { return $this->active; }
    
    // Event handling
    private function raise($event): void
    {
        $this->events[] = $event;
    }
    
    public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }
}
```

#### Value Object
```php
<?php

namespace App\Domain\Product\ValueObjects;

class Money
{
    public function __construct(
        private float $amount,
        private string $currency = 'BRL'
    ) {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
    }
    
    public static function fromFloat(float $amount, string $currency = 'BRL'): self
    {
        return new self($amount, $currency);
    }
    
    public function getAmount(): float
    {
        return $this->amount;
    }
    
    public function getCurrency(): string
    {
        return $this->currency;
    }
    
    public function equals(Money $other): bool
    {
        return $this->amount === $other->amount 
            && $this->currency === $other->currency;
    }
    
    public function add(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException('Cannot add different currencies');
        }
        
        return new self($this->amount + $other->amount, $this->currency);
    }
    
    public function format(): string
    {
        return number_format($this->amount, 2, ',', '.') . ' ' . $this->currency;
    }
}
```

#### Domain Contract
```php
<?php

namespace App\Domain\Product\Contracts;

use App\Domain\Product\Entities\Product;
use App\Domain\Product\ValueObjects\ProductId;

interface ProductRepositoryInterface
{
    public function findById(ProductId $id): ?Product;
    public function save(Product $product): void;
    public function delete(Product $product): void;
}
```

### 2. Application Layer - Casos de Uso

#### Use Case
```php
<?php

namespace App\Application\Product\UseCases;

use App\Domain\Product\Contracts\ProductRepositoryInterface;
use App\Domain\Product\Entities\Product;
use App\Domain\Product\ValueObjects\Money;
use App\Application\Product\DTOs\CreateProductDTO;
use App\Application\Product\DTOs\ProductDTO;
use App\Application\Contracts\TransactionManagerInterface;
use App\Application\Contracts\EventDispatcherInterface;

class CreateProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $repository,
        private TransactionManagerInterface $transaction,
        private EventDispatcherInterface $eventDispatcher,
    ) {}
    
    public function execute(CreateProductDTO $dto): ProductDTO
    {
        return $this->transaction->execute(function () use ($dto) {
            // Criar entidade de domínio
            $product = Product::create(
                name: $dto->name,
                description: $dto->description,
                price: Money::fromFloat($dto->price, $dto->currency)
            );
            
            // Persistir
            $this->repository->save($product);
            
            // Disparar eventos
            foreach ($product->pullEvents() as $event) {
                $this->eventDispatcher->dispatch($event);
            }
            
            // Retornar DTO
            return ProductDTO::fromEntity($product);
        });
    }
}
```

#### DTO
```php
<?php

namespace App\Application\Product\DTOs;

use App\Domain\Product\Entities\Product;

class ProductDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly float $price,
        public readonly string $currency,
        public readonly bool $active,
        public readonly string $createdAt,
    ) {}
    
    public static function fromEntity(Product $product): self
    {
        return new self(
            id: $product->getId()->toString(),
            name: $product->getName(),
            description: $product->getDescription(),
            price: $product->getPrice()->getAmount(),
            currency: $product->getPrice()->getCurrency(),
            active: $product->isActive(),
            createdAt: $product->getCreatedAt()->format('Y-m-d H:i:s'),
        );
    }
}
```

### 3. Infrastructure Layer - Implementações

#### Eloquent Model
```php
<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProductModel extends Model
{
    use HasUuids;
    
    protected $table = 'products';
    
    protected $fillable = [
        'id',
        'name',
        'description',
        'price_amount',
        'price_currency',
        'active',
    ];
    
    protected $casts = [
        'price_amount' => 'decimal:2',
        'active' => 'boolean',
    ];
}
```

#### Repository Implementation
```php
<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Product\Contracts\ProductRepositoryInterface;
use App\Domain\Product\Entities\Product;
use App\Domain\Product\ValueObjects\ProductId;
use App\Domain\Product\ValueObjects\Money;
use App\Infrastructure\Persistence\Eloquent\Models\ProductModel;

class ProductRepository implements ProductRepositoryInterface
{
    public function findById(ProductId $id): ?Product
    {
        $model = ProductModel::find($id->toString());
        
        if (!$model) {
            return null;
        }
        
        return $this->toDomain($model);
    }
    
    public function save(Product $product): void
    {
        $model = ProductModel::updateOrCreate(
            ['id' => $product->getId()->toString()],
            $this->toPersistence($product)
        );
    }
    
    public function delete(Product $product): void
    {
        ProductModel::destroy($product->getId()->toString());
    }
    
    private function toDomain(ProductModel $model): Product
    {
        return new Product(
            id: ProductId::fromString($model->id),
            name: $model->name,
            description: $model->description,
            price: Money::fromFloat($model->price_amount, $model->price_currency),
            active: $model->active,
            createdAt: new \DateTimeImmutable($model->created_at)
        );
    }
    
    private function toPersistence(Product $product): array
    {
        return [
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price_amount' => $product->getPrice()->getAmount(),
            'price_currency' => $product->getPrice()->getCurrency(),
            'active' => $product->isActive(),
        ];
    }
}
```

#### Transaction Manager
```php
<?php

namespace App\Infrastructure\Persistence;

use App\Application\Contracts\TransactionManagerInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseTransactionManager implements TransactionManagerInterface
{
    public function execute(callable $operation)
    {
        return DB::transaction(function () use ($operation) {
            $savepointId = 'SP_' . Str::upper(Str::random(8));
            
            DB::statement("SAVEPOINT {$savepointId}");
            
            try {
                return $operation();
            } catch (\Exception $e) {
                DB::statement("ROLLBACK TO SAVEPOINT {$savepointId}");
                throw $e;
            }
        });
    }
}
```

### 4. Módulo Completo - Padrão Dourado Adaptado

#### Estrutura do Módulo Auth
```
app/Modules/Auth/
├── Api/
│   ├── Controllers/
│   │   └── AuthController.php
│   ├── Requests/
│   │   ├── SignInRequest.php
│   │   ├── SignUpRequest.php
│   │   └── RefreshTokenRequest.php
│   └── Resources/
│       ├── UserResource.php
│       └── TokenResource.php
├── UseCases/
│   ├── SignInUseCase.php
│   ├── SignUpUseCase.php
│   └── RefreshTokenUseCase.php
├── DTOs/
│   ├── SignInDTO.php
│   ├── SignUpDTO.php
│   └── AuthResponseDTO.php
├── Models/
│   ├── User.php
│   └── AuthMfa.php
└── Providers/
    └── AuthServiceProvider.php
```

#### Controller (Padrão Dourado)
```php
<?php

namespace App\Modules\Auth\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Api\Requests\SignInRequest;
use App\Modules\Auth\Api\Resources\TokenResource;
use App\Modules\Auth\UseCases\SignInUseCase;
use App\Modules\Auth\DTOs\SignInDTO;

class AuthController extends Controller
{
    public function __construct(
        private SignInUseCase $signInUseCase,
    ) {}
    
    /**
     * @OA\Post(
     *     path="/api/auth/sign-in",
     *     summary="User authentication",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SignInRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authentication successful",
     *         @OA\JsonContent(ref="#/components/schemas/TokenResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function signIn(SignInRequest $request): TokenResource
    {
        $dto = SignInDTO::fromRequest($request);
        
        $result = $this->signInUseCase->execute($dto);
        
        return new TokenResource($result);
    }
}
```

#### Use Case (Padrão Dourado)
```php
<?php

namespace App\Modules\Auth\UseCases;

use App\Domain\Interfaces\Auth\SignInUseCaseInterface;
use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Modules\Auth\DTOs\SignInDTO;
use App\Modules\Auth\DTOs\AuthResponseDTO;
use App\Infrastructure\Auth\JwtTokenService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SignInUseCase implements SignInUseCaseInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private JwtTokenService $tokenService,
    ) {}
    
    public function execute(SignInDTO $dto): AuthResponseDTO
    {
        // Buscar usuário
        $user = $this->userRepository->findByEmail($dto->email);
        
        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        
        // Verificar se usuário está ativo
        if (!$user->isActive()) {
            throw ValidationException::withMessages([
                'email' => ['Your account is inactive.'],
            ]);
        }
        
        // Gerar tokens
        $accessToken = $this->tokenService->generateAccessToken($user);
        $refreshToken = $this->tokenService->generateRefreshToken($user);
        
        // Registrar login
        $user->recordLogin($dto->ipAddress, $dto->userAgent);
        $this->userRepository->save($user);
        
        return new AuthResponseDTO(
            user: $user,
            accessToken: $accessToken,
            refreshToken: $refreshToken,
            expiresIn: config('auth.jwt.ttl')
        );
    }
}
```

#### DTO (Padrão Dourado)
```php
<?php

namespace App\Modules\Auth\DTOs;

use App\Modules\Auth\Api\Requests\SignInRequest;

class SignInDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $ipAddress = null,
        public readonly ?string $userAgent = null,
    ) {}
    
    public static function fromRequest(SignInRequest $request): self
    {
        return new self(
            email: $request->input('email'),
            password: $request->input('password'),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );
    }
}
```

#### Model (Baseado no Dourado)
```php
<?php

namespace App\Modules\Auth\Models;

use App\Domain\Entities\BaseModel;
use App\Domain\Commons\Enums\UserRoleEnum;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends BaseModel implements AuthenticatableContract
{
    use Authenticatable;
    
    protected $fillable = [
        'email',
        'password',
        'name',
        'role',
        'is_active',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $casts = [
        'role' => UserRoleEnum::class,
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];
    
    public function isActive(): bool
    {
        return $this->is_active;
    }
    
    public function isAdmin(): bool
    {
        return $this->role === UserRoleEnum::ADMIN;
    }
    
    public function recordLogin(string $ipAddress, string $userAgent): void
    {
        $this->last_login_at = now();
        $this->last_login_ip = $ipAddress;
        $this->last_login_user_agent = $userAgent;
    }
    
    public function mfaSettings(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AuthMfa::class);
    }
}
```

#### Form Request
```php
<?php

namespace App\Presentation\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do produto é obrigatório',
            'price.min' => 'O preço não pode ser negativo',
        ];
    }
}
```

### 5. Service Provider - Configurando as Dependências

```php
<?php

namespace App\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Domain Repositories
        $this->app->bind(
            \App\Domain\Product\Contracts\ProductRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\ProductRepository::class
        );
        
        // Application Services
        $this->app->bind(
            \App\Application\Contracts\TransactionManagerInterface::class,
            \App\Infrastructure\Persistence\DatabaseTransactionManager::class
        );
        
        $this->app->bind(
            \App\Application\Contracts\EventDispatcherInterface::class,
            \App\Infrastructure\Events\LaravelEventDispatcher::class
        );
    }
}
```

## 🧪 Testabilidade

### Teste de Domínio (Unit Test)
```php
<?php

namespace Tests\Unit\Domain\Product;

use Tests\TestCase;
use App\Domain\Product\Entities\Product;
use App\Domain\Product\ValueObjects\Money;

class ProductTest extends TestCase
{
    public function test_can_create_product(): void
    {
        $product = Product::create(
            name: 'Test Product',
            description: 'A test product',
            price: Money::fromFloat(100.00)
        );
        
        $this->assertEquals('Test Product', $product->getName());
        $this->assertEquals(100.00, $product->getPrice()->getAmount());
        $this->assertTrue($product->isActive());
    }
    
    public function test_cannot_create_product_with_negative_price(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        Product::create(
            name: 'Test Product',
            description: 'A test product',
            price: Money::fromFloat(-10.00)
        );
    }
}
```

### Teste de Use Case (Integration Test)
```php
<?php

namespace Tests\Feature\Application\Product;

use Tests\TestCase;
use App\Application\Product\UseCases\CreateProductUseCase;
use App\Application\Product\DTOs\CreateProductDTO;
use App\Domain\Product\Contracts\ProductRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateProductUseCaseTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_can_create_product_via_use_case(): void
    {
        $useCase = app(CreateProductUseCase::class);
        
        $dto = new CreateProductDTO(
            name: 'Test Product',
            description: 'Description',
            price: 99.99,
            currency: 'BRL'
        );
        
        $result = $useCase->execute($dto);
        
        $this->assertEquals('Test Product', $result->name);
        $this->assertEquals(99.99, $result->price);
        
        // Verificar se foi salvo
        $repository = app(ProductRepositoryInterface::class);
        $product = $repository->findById(ProductId::fromString($result->id));
        
        $this->assertNotNull($product);
    }
}
```

## 🔍 Descobertas Específicas do Dourado Dashboard

### Estrutura Real Encontrada
Baseando-se na análise completa do projeto Dourado Dashboard, foram identificados os seguintes padrões que devemos adaptar:

#### 1. Organização Modular Avançada
```
src/modules/
├── auth/                     # Autenticação completa com 2FA
├── user/                     # Gestão de usuários
├── tokens/                   # Tokens de investimento
├── wallet/                   # Carteiras digitais
├── transactions/             # Transações financeiras
├── upload-file/              # Upload e processamento
├── categories-list/          # BFF para categorias
└── prefix-investment/        # Investimentos específicos
```

#### 2. Padrão de Integração Externa
O projeto demonstra um excelente padrão para integrações:
```
infrastructure/external/
├── core-backend/            # Cliente para API principal
├── bdm/                     # Sistema externo BDM
└── blockchain/              # APIs blockchain
```

#### 3. Processamento Assíncrono
```
functions/
├── dashboard-file-processor/      # Processamento de arquivos
├── dashboard-transfer-assets/     # Transferências
└── dashboard-transfer-status-check/ # Verificação de status
```

#### 4. Autenticação Robusta
- **JWT + 2FA** implementado
- **Multiple Role System** (USER, ADMIN, GESTOR, REPRESENTANTE)
- **AWS Cognito Integration**
- **Session Management** com refresh tokens

#### 5. Estrutura de Entidades Base
```php
// Baseado no BaseEntity do Dourado
abstract class BaseModel extends Model
{
    protected $guarded = ['id'];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

#### 6. Padrão BFF (Backend for Frontend)
O módulo `categories-list` exemplifica um excelente BFF:
- **Endpoint público** sem autenticação
- **Otimização** específica para frontend
- **Transformação** de dados do core backend
- **Cache layer** para performance

#### 7. Processamento de Arquivos
Sistema robusto para upload e processamento:
- **CSV Processing** para transferências
- **S3 Integration** para armazenamento
- **Async Processing** com SQS
- **Progress Tracking** em tempo real

### Adaptações Recomendadas para Laravel

#### 1. Service Provider Modular
```php
// app/Modules/Auth/Providers/AuthServiceProvider.php
class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerUseCases();
        $this->registerRepositories();
        $this->registerServices();
    }
    
    private function registerUseCases(): void
    {
        $this->app->bind(SignInUseCaseInterface::class, SignInUseCase::class);
        $this->app->bind(SignUpUseCaseInterface::class, SignUpUseCase::class);
    }
}
```

#### 2. Multi-Repository Pattern
```php
// Domain layer
interface UserRepositoryInterface {}
interface AuthMfaRepositoryInterface {}

// Infrastructure layer  
class UserRepository implements UserRepositoryInterface {}
class AuthMfaRepository implements AuthMfaRepositoryInterface {}
```

#### 3. Event-Driven Architecture
```php
// Baseado nos events do Dourado
class UserLoggedIn
{
    public function __construct(
        public User $user,
        public string $ipAddress,
        public Carbon $loginAt
    ) {}
}

class RecordLoginActivity
{
    public function handle(UserLoggedIn $event): void
    {
        // Log activity
    }
}
```

#### 4. External Service Pattern
```php
// app/Infrastructure/External/CoreBackend/CoreBackendClient.php
class CoreBackendClient
{
    public function __construct(
        private HttpClient $client,
        private string $baseUrl,
        private TokenService $tokenService
    ) {}
    
    public function getCategories(array $filters = []): array
    {
        return $this->client
            ->withToken($this->tokenService->getCoreToken())
            ->get("{$this->baseUrl}/categories", $filters)
            ->json();
    }
}
```

#### 5. Queue Job Pattern
```php
// app/Functions/Jobs/ProcessFileUpload.php
class ProcessFileUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public function handle(FileProcessorService $processor): void
    {
        $processor->process($this->filePath, $this->userId);
    }
}
```

## 🔑 Conceitos Importantes

### 1. Agregados e Raízes de Agregado
- Agrupe entidades relacionadas
- Acesse apenas pela raiz do agregado
- Mantenha consistência dentro do agregado

### 2. Domain Events
- Capture mudanças importantes no domínio
- Permita reação a eventos de forma desacoplada
- Facilite integração entre contextos

### 3. Specification Pattern
```php
interface Specification
{
    public function isSatisfiedBy($candidate): bool;
}

class ActiveProductSpecification implements Specification
{
    public function isSatisfiedBy($product): bool
    {
        return $product->isActive();
    }
}
```

### 4. Repository Pattern Avançado
```php
interface ProductRepositoryInterface
{
    public function findById(ProductId $id): ?Product;
    public function findBySpecification(Specification $spec): array;
    public function save(Product $product): void;
    public function nextIdentity(): ProductId;
}
```

## 📚 Boas Práticas

1. **Imutabilidade**: Value Objects sempre imutáveis
2. **Validação no Domínio**: Regras de negócio nas entidades
3. **Sem Anemic Domain**: Entidades com comportamento
4. **Use Cases Focados**: Um caso de uso, uma responsabilidade
5. **DTOs para Fronteira**: Isole o domínio do mundo externo
6. **Testes em Camadas**: Unit para domínio, Integration para aplicação

## 🚀 Benefícios da Arquitetura

1. **Manutenibilidade**: Código organizado e previsível
2. **Testabilidade**: Fácil testar cada camada isoladamente
3. **Flexibilidade**: Troque implementações sem afetar o domínio
4. **Escalabilidade**: Adicione features sem bagunçar o código
5. **Compreensibilidade**: Estrutura clara e intuitiva

Esta arquitetura permite construir aplicações robustas, mantendo o código limpo e as regras de negócio protegidas no centro da aplicação.

## 🐳 Configuração Docker Completa

### Estrutura Docker do Projeto
```
projeto/
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   ├── php/
│   │   └── Dockerfile
│   └── mysql/
│       └── init.sql
├── docker-compose.yml
├── docker-compose.override.yml
└── .env.docker
```

### docker-compose.yml Principal
```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: docker/php/Dockerfile
      target: development
    container_name: laravel_app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
      - ./docker/php/local.ini:/usr/local/etc/php/conf.d/local.ini
    networks:
      - laravel
    depends_on:
      - database
      - redis
    environment:
      - APP_ENV=local
      - CONTAINER_ROLE=app

  webserver:
    image: nginx:alpine
    container_name: laravel_webserver
    restart: unless-stopped
    ports:
      - "8080:80"
      - "8443:443"
    volumes:
      - ./:/var/www
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    networks:
      - laravel
    depends_on:
      - app

  database:
    image: postgres:15-alpine
    container_name: laravel_database
    restart: unless-stopped
    ports:
      - "5432:5432"
    environment:
      POSTGRES_DATABASE: ${DB_DATABASE}
      POSTGRES_USER: ${DB_USERNAME}
      POSTGRES_PASSWORD: ${DB_PASSWORD}
      POSTGRES_ROOT_PASSWORD: ${DB_PASSWORD}
    volumes:
      - postgres_data:/var/lib/postgresql/data
      - ./docker/postgres/init.sql:/docker-entrypoint-initdb.d/init.sql
    networks:
      - laravel

  redis:
    image: redis:7-alpine
    container_name: laravel_redis
    restart: unless-stopped
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data
    networks:
      - laravel

  queue:
    build:
      context: .
      dockerfile: docker/php/Dockerfile
      target: development
    container_name: laravel_queue
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
    networks:
      - laravel
    depends_on:
      - database
      - redis
    environment:
      - CONTAINER_ROLE=queue
    command: php artisan queue:work --sleep=3 --tries=3

  scheduler:
    build:
      context: .
      dockerfile: docker/php/Dockerfile
      target: development
    container_name: laravel_scheduler
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
    networks:
      - laravel
    depends_on:
      - database
      - redis
    environment:
      - CONTAINER_ROLE=scheduler
    command: >
      sh -c "while true; do
        php artisan schedule:run --verbose --no-interaction &
        sleep 60
      done"

  mailpit:
    image: axllent/mailpit
    container_name: laravel_mailpit
    restart: unless-stopped
    ports:
      - "8025:8025"
      - "1025:1025"
    networks:
      - laravel

volumes:
  postgres_data:
    driver: local
  redis_data:
    driver: local

networks:
  laravel:
    driver: bridge
```

### Dockerfile para PHP
```dockerfile
# docker/php/Dockerfile
FROM php:8.3-fpm-alpine as base

# Instalar dependências do sistema
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-dev \
    oniguruma-dev \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    supervisor

# Instalar extensões PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    xml \
    soap \
    intl

# Instalar Redis extension
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar usuário
RUN addgroup -g 1000 www && \
    adduser -u 1000 -G www -s /bin/sh -D www

# Configurar diretório de trabalho
WORKDIR /var/www

# Copiar configurações
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini
COPY docker/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Development stage
FROM base as development

# Instalar Xdebug para desenvolvimento
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

USER www

# Production stage
FROM base as production

# Copiar código fonte
COPY --chown=www:www . /var/www

# Instalar dependências
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Otimizações para produção
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

USER www

EXPOSE 9000

CMD ["php-fpm"]
```

### Configuração Nginx
```nginx
# docker/nginx/default.conf
server {
    listen 80;
    listen [::]:80;
    server_name localhost;
    root /var/www/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    # Logs
    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;

    # Handle API routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Handle .php files
    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }

    # Static files optimization
    location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Security headers
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy "strict-origin-when-cross-origin";
}
```

### Scripts de Desenvolvimento
```bash
#!/bin/bash
# scripts/dev.sh

echo "🐳 Iniciando ambiente de desenvolvimento..."

# Construir e iniciar containers
docker-compose up -d --build

# Aguardar banco de dados
echo "⏳ Aguardando banco de dados..."
sleep 10

# Instalar dependências
echo "📦 Instalando dependências..."
docker-compose exec app composer install

# Executar migrações
echo "🗄️ Executando migrações..."
docker-compose exec app php artisan migrate:fresh --seed

# Gerar chave da aplicação
echo "🔑 Gerando chave da aplicação..."
docker-compose exec app php artisan key:generate

# Limpar cache
echo "🧹 Limpando cache..."
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear

# Gerar documentação Swagger
echo "📚 Gerando documentação Swagger..."
docker-compose exec app php artisan l5-swagger:generate

echo "✅ Ambiente pronto!"
echo "📖 API: http://localhost:8080"
echo "📖 Swagger: http://localhost:8080/api/documentation"
echo "📧 Mailpit: http://localhost:8025"
```

### Makefile para Comandos
```makefile
# Makefile
.PHONY: help build up down restart logs shell test migrate seed swagger

help: ## Mostra esta ajuda
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

build: ## Constrói os containers
	docker-compose build

up: ## Inicia os containers
	docker-compose up -d

down: ## Para os containers
	docker-compose down

restart: ## Reinicia os containers
	docker-compose restart

logs: ## Mostra logs dos containers
	docker-compose logs -f

shell: ## Acessa shell do container app
	docker-compose exec app sh

test: ## Executa testes
	docker-compose exec app php artisan test

migrate: ## Executa migrações
	docker-compose exec app php artisan migrate

migrate-fresh: ## Recria banco com seeds
	docker-compose exec app php artisan migrate:fresh --seed

seed: ## Executa seeders
	docker-compose exec app php artisan db:seed

swagger: ## Gera documentação Swagger
	docker-compose exec app php artisan l5-swagger:generate

check-commit: ## Verifica se commit não contém palavras proibidas
	@echo "🔍 Verificando último commit..."
	@COMMIT_MSG=$$(git log -1 --pretty=%B) && \
	if echo "$$COMMIT_MSG" | grep -i "claude\|chatgpt\|ia\|ai\|artificial\|generated\|assistente\|ajuda.*ia\|co-authored.*ai" > /dev/null; then \
		echo "🚨 ERRO: Commit contém palavras proibidas!"; \
		echo "Mensagem: $$COMMIT_MSG"; \
		echo "❌ Corrija antes de fazer push!"; \
		exit 1; \
	else \
		echo "✅ Commit aprovado - sem menções proibidas"; \
	fi

install: ## Instalação inicial completa
	make build
	make up
	sleep 10
	docker-compose exec app composer install
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan migrate:fresh --seed
	docker-compose exec app php artisan l5-swagger:generate
	@echo "✅ Projeto instalado com sucesso!"
	@echo "📖 API: http://localhost:8080"
	@echo "📖 Swagger: http://localhost:8080/api/documentation"
	@echo ""
	@echo "🚨 LEMBRE-SE: Nunca mencionar IA nos commits!"
	@echo "📝 Use: make check-commit antes de push"
```

## 📚 Configuração Swagger Completa

### Instalação e Configuração
```bash
# Instalar pacote Swagger
composer require darkaonline/l5-swagger

# Publicar configurações
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

### Configuração config/l5-swagger.php
```php
<?php

return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            'api' => [
                'title' => env('APP_NAME', 'Laravel API'),
                'version' => '1.0.0',
                'description' => 'API Documentation for ' . env('APP_NAME'),
            ],
            'routes' => [
                'api' => 'api/documentation',
                'docs' => 'docs',
            ],
            'paths' => [
                'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', true),
                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',
                'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),
                'annotations' => [
                    base_path('app'),
                ],
            ],
        ],
    ],
    'defaults' => [
        'routes' => [
            'docs' => 'docs',
            'oauth2_callback' => 'api/oauth2-callback',
            'middleware' => [
                'api' => [],
                'asset' => [],
                'docs' => [],
                'oauth2_callback' => [],
            ],
            'group_options' => [],
        ],
        'paths' => [
            'docs' => storage_path('api-docs'),
            'views' => base_path('resources/views/vendor/l5-swagger'),
            'base' => env('L5_SWAGGER_BASE_PATH', null),
            'swagger_ui_assets_path' => env('L5_SWAGGER_UI_ASSETS_PATH', 'vendor/swagger-api/swagger-ui/dist/'),
            'excludes' => [],
        ],
        'scanOptions' => [
            'analyser' => null,
            'analysis' => null,
            'processors' => [],
            'pattern' => null,
            'exclude' => [],
        ],
        'securityDefinitions' => [
            'securitySchemes' => [
                'bearerAuth' => [
                    'type' => 'http',
                    'scheme' => 'bearer',
                    'bearerFormat' => 'JWT',
                ],
            ],
            'security' => [
                ['bearerAuth' => []]
            ],
        ],
        'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),
        'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', false),
        'proxy' => false,
        'additional_config_url' => null,
        'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),
        'validator_url' => null,
        'ui' => [
            'display' => [
                'dark_mode' => env('L5_SWAGGER_UI_DARK_MODE', false),
                'doc_expansion' => env('L5_SWAGGER_UI_DOC_EXPANSION', 'none'),
                'filter' => env('L5_SWAGGER_UI_FILTERS', true),
            ],
        ],
        'constants' => [
            'L5_SWAGGER_CONST_HOST' => env('L5_SWAGGER_CONST_HOST', 'http://localhost:8080'),
        ],
    ],
];
```

### Controller Base com Swagger
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Laravel Clean Architecture API",
 *     description="API documentation for Laravel Clean Architecture project",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Development Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="Authentication endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Users",
 *     description="User management endpoints"
 * )
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
```

### Exemplo Controller com Swagger Completo
```php
<?php

namespace App\Modules\Auth\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Api\Requests\SignInRequest;
use App\Modules\Auth\Api\Resources\TokenResource;
use App\Modules\Auth\UseCases\SignInUseCase;

class AuthController extends Controller
{
    public function __construct(
        private SignInUseCase $signInUseCase,
    ) {}
    
    /**
     * @OA\Post(
     *     path="/api/auth/sign-in",
     *     summary="User authentication",
     *     description="Authenticate user with email and password",
     *     operationId="signIn",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User credentials",
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="remember_me", type="boolean", example=false, description="Remember user session")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authentication successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Authentication successful"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *                 @OA\Property(property="refresh_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=3600),
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="string", example="uuid-here"),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="admin@example.com"),
     *                     @OA\Property(property="role", type="string", example="ADMIN"),
     *                     @OA\Property(property="is_active", type="boolean", example=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid credentials"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The provided credentials are incorrect."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email field is required.")),
     *                 @OA\Property(property="password", type="array", @OA\Items(type="string", example="The password field is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function signIn(SignInRequest $request): TokenResource
    {
        $dto = SignInDTO::fromRequest($request);
        $result = $this->signInUseCase->execute($dto);
        return new TokenResource($result);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="User logout",
     *     description="Logout current user and invalidate token",
     *     operationId="logout",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function logout(): JsonResponse
    {
        // Implementação do logout
    }
}
```

### Schema Definitions para Reutilização
```php
<?php

namespace App\Http\Controllers\Schemas;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="User model",
 *     @OA\Property(property="id", type="string", format="uuid", example="uuid-here"),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="role", type="string", enum={"ADMIN", "USER", "MANAGER"}, example="USER"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00Z")
 * )
 * 
 * @OA\Schema(
 *     schema="ApiResponse",
 *     type="object",
 *     title="API Response",
 *     description="Standard API response format",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Operation successful"),
 *     @OA\Property(property="data", type="object", description="Response data")
 * )
 * 
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     title="Validation Error",
 *     description="Validation error response",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Validation failed"),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         description="Validation errors by field",
 *         additionalProperties={"type": "array", "items": {"type": "string"}}
 *     )
 * )
 */
class SwaggerSchemas
{
    // Esta classe existe apenas para definir schemas
}
```

### Comando Artisan para Documentação
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateSwaggerDocs extends Command
{
    protected $signature = 'docs:generate {--force : Force regeneration}';
    protected $description = 'Generate Swagger API documentation';

    public function handle()
    {
        $this->info('🔄 Generating Swagger documentation...');
        
        // Limpar cache
        $this->call('config:clear');
        $this->call('cache:clear');
        
        // Gerar documentação
        $this->call('l5-swagger:generate');
        
        // Copiar assets se necessário
        if ($this->option('force')) {
            $this->call('vendor:publish', [
                '--provider' => 'L5Swagger\L5SwaggerServiceProvider',
                '--tag' => 'swagger-ui-assets',
                '--force' => true
            ]);
        }
        
        $this->info('✅ Swagger documentation generated successfully!');
        $this->line('📖 Available at: ' . url('/api/documentation'));
    }
}
```

### .env.docker para Docker
```bash
# .env.docker
APP_NAME="Laravel Clean Architecture"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=database
DB_PORT=5432
DB_DATABASE=laravel_clean
DB_USERNAME=laravel
DB_PASSWORD=password

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Swagger
L5_SWAGGER_GENERATE_ALWAYS=true
L5_SWAGGER_CONST_HOST=http://localhost:8080
```

Esta configuração completa fornece:

1. **🐳 Docker Setup Completo**: Multi-stage builds, otimizações para dev/prod
2. **📚 Swagger Documentação**: Configuração completa com exemplos práticos
3. **🛠️ Scripts de Desenvolvimento**: Makefile e scripts bash para facilitar o uso
4. **📧 Mailpit**: Para testar emails localmente
5. **🔄 Queue Workers**: Para processamento assíncrono
6. **📊 Redis**: Para cache e sessões
7. **🗄️ PostgreSQL**: Banco de dados robusto
8. **🔍 Documentação Rica**: Swagger com schemas reutilizáveis

## 📋 Regras e Boas Práticas de Desenvolvimento

### 🚨 **ALERTA CRÍTICO - REGRA FUNDAMENTAL**

```
⚠️  NUNCA MENCIONAR IA, CLAUDE, CHATGPT OU ASSISTENTES NOS COMMITS  ⚠️

Esta é uma VIOLAÇÃO CRÍTICA que pode comprometer o profissionalismo 
do projeto. SEMPRE verificar commits antes de push.

Usar apenas: [TICKET] tipo: descrição em português da funcionalidade
```

### 🎯 **REGRAS FUNDAMENTAIS DO PROJETO**

#### 🌐 **Idioma Obrigatório**
- **TODAS as respostas devem ser em PORTUGUÊS (pt-BR)**
- Documentação, comentários e comunicações sempre em português
- Commits obrigatoriamente em português
- Mensagens de erro e validação em português
- Variáveis e métodos podem ser em inglês (padrão do framework)

#### 🚫 **REGRA ABSOLUTA: Nunca Alterar Código de Outros Devs**
- **VIOLAÇÃO CRÍTICA - NUNCA FAZER:**
  - ❌ Modificar arquivos de outros fluxos/funcionalidades
  - ❌ Tocar em código que não foi desenvolvido por você
  - ❌ Modificar CREATE quando trabalhando em UPDATE/LIST/DELETE
  - ❌ Fazer "melhorias" em código existente de outros

- **Se precisar algo do código existente:**
  - ✅ Ler e entender o padrão
  - ✅ Copiar o padrão para seu código
  - ❌ NUNCA modificar o original

### 📦 **PADRÕES DE COMMIT**

#### ✅ **Formato Obrigatório**
```bash
[TICKET] tipo: descrição concisa em português

Detalhamento opcional do que foi implementado.
Foco na funcionalidade, não na ferramenta.
```

#### 🔧 **Tipos Permitidos**
- `feat`: Nova funcionalidade
- `fix`: Correção de bug
- `refactor`: Refatoração sem mudança funcional
- `chore`: Tarefas de manutenção
- `docs`: Documentação
- `test`: Adição ou correção de testes

#### **Exemplos de Commits Corretos:**
```bash
[PROJ-123] feat: implementa autenticação JWT com refresh token
[PROJ-124] fix: corrige validação de email no formulário de cadastro
[PROJ-125] refactor: simplifica lógica de cálculo de preços
[PROJ-126] chore: atualiza dependências do projeto
```

#### ❌ **PROIBIÇÕES ABSOLUTAS NOS COMMITS**

**🚫 NUNCA MENCIONAR:**
- **Claude, ChatGPT, IA, AI, Artificial Intelligence**
- **Assistentes virtuais, bots, ferramentas de IA**
- **"Generated by", "Co-Authored-By" com IA**
- **"Com ajuda de", "Assistido por", "Gerado por"**

**❌ EXEMPLOS PROIBIDOS:**
```bash
❌ [PROJ-123] feat: implementa login com ajuda do Claude
❌ [PROJ-124] fix: corrige bug (gerado por IA)
❌ [PROJ-125] feat: adiciona validação - Generated by ChatGPT
❌ [PROJ-126] refactor: melhora código usando AI

Co-Authored-By: Claude <noreply@anthropic.com>
Co-Authored-By: ChatGPT <ai@openai.com>
```

**✅ EXEMPLOS CORRETOS:**
```bash
✅ [PROJ-123] feat: implementa autenticação JWT com refresh token
✅ [PROJ-124] fix: corrige validação de email no cadastro
✅ [PROJ-125] feat: adiciona validação de CPF nos formulários
✅ [PROJ-126] refactor: melhora performance das queries de usuário
```

**🔍 OUTRAS PROIBIÇÕES:**
- Commits em inglês
- Commits sem ticket de referência
- Commits genéricos como "fix", "update", "changes"
- Menções a qualquer ferramenta externa de automação
- Referências a "assistentes" ou "ajuda automatizada"

### 🚫 **CÓDIGO LIMPO - PROIBIÇÕES ABSOLUTAS**

#### ❌ **Comentários Totalmente Proibidos**
```php
// ❌ PROIBIDO - Qualquer tipo de comentário
// Este método cria um usuário
/* Comentário de bloco */
/** Comentário de documentação */

// ✅ APROVADO - Código autoexplicativo
class CreateUserUseCase
{
    public function execute(CreateUserDTO $dto): UserDTO
    {
        $user = $this->validateAndCreateUser($dto);
        $this->sendWelcomeEmail($user);
        return UserDTO::fromModel($user);
    }
}
```

#### 📛 **Regras de Arquivos Locais**
**NUNCA commitar:**
- Arquivos de documentação interna (`.md` locais)
- `composer.lock` em ambiente de desenvolvimento
- `.env` com dados reais
- Arquivos de configuração IDE

### 🎨 **VALIDAÇÃO E ESTRUTURA DE CÓDIGO**

#### 🔤 **Constantes Descritivas**
```php
// ❌ Evitar
if (!$url || !$token || !$active) {
    throw new Exception('Dados incompletos');
}

// ✅ Preferir
$hasRequiredData = $url && $token && $active;
if (!$hasRequiredData) {
    throw new ValidationException('Dados obrigatórios não fornecidos');
}
```

#### ⚡ **Early Return Pattern**
```php
// ❌ Evitar aninhamentos excessivos
public function processUser(User $user): void
{
    if ($user->isActive()) {
        if ($user->hasPermission()) {
            if ($user->isVerified()) {
                // código longo
            }
        }
    }
}

// ✅ Preferir retornos antecipados
public function processUser(User $user): void
{
    if (!$user->isActive()) {
        return;
    }
    
    if (!$user->hasPermission()) {
        throw new UnauthorizedException('Usuário sem permissão');
    }
    
    if (!$user->isVerified()) {
        throw new UnverifiedUserException('Usuário não verificado');
    }
    
    // código principal
}
```

### 🛡️ **DEFENSIVE PROGRAMMING**

#### ✅ **Validação de Nullity**
```php
// ❌ Evitar
$value = $object->property->subProperty;

// ✅ Preferir
$value = $object?->property?->subProperty;
// ou
if ($object && $object->property) {
    $value = $object->property->subProperty;
}
```

#### 🔄 **Validação em Cascata**
```php
// ✅ Recomendado
public function processOrder(OrderDTO $dto): OrderResult
{
    // 1. Verificar campos básicos
    if (!$this->hasBasicFields($dto)) {
        throw new ValidationException('Campos obrigatórios ausentes');
    }
    
    // 2. Validar dados de negócio
    $customer = $this->customerRepository->findById($dto->customerId);
    if (!$customer) {
        throw new NotFoundException('Cliente não encontrado');
    }
    
    // 3. Verificar regras de negócio
    if (!$this->canProcessOrder($customer, $dto)) {
        throw new BusinessRuleException('Pedido não pode ser processado');
    }
    
    // 4. Processar pedido
    return $this->createOrder($customer, $dto);
}
```

### 📊 **LOGGING E TRATAMENTO DE ERROS**

#### 📋 **Níveis de Log Apropriados**
```php
// Usar logs contextualizados
Log::error("Erro ao processar pedido {$orderId}: {$exception->getMessage()}", [
    'order_id' => $orderId,
    'user_id' => $userId,
    'exception' => $exception
]);

Log::warning("Tentativa de acesso negada para usuário {$userId}", [
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent()
]);

Log::info("Pedido {$orderId} processado com sucesso", [
    'order_id' => $orderId,
    'total' => $order->total,
    'items_count' => $order->items->count()
]);
```

#### 🔧 **Try-Catch em Métodos Específicos**
```php
// ✅ Recomendado - Tratamento específico
public function execute(CreateUserDTO $dto): UserDTO
{
    try {
        return $this->processUser($dto);
    } catch (ValidationException $e) {
        Log::warning("Validação falhou para criação de usuário: {$e->getMessage()}");
        throw $e;
    } catch (Exception $e) {
        Log::error("Erro inesperado ao criar usuário: {$e->getMessage()}");
        throw new UserCreationException('Não foi possível criar o usuário');
    }
}

private function processUser(CreateUserDTO $dto): UserDTO
{
    try {
        $user = $this->userRepository->create($dto->toArray());
        $this->sendWelcomeEmail($user);
        return UserDTO::fromModel($user);
    } catch (DatabaseException $e) {
        Log::error("Erro de banco ao criar usuário: {$e->getMessage()}");
        throw $e;
    }
}
```

### 🏢 **ORGANIZAÇÃO DE CÓDIGO**

#### 👤 **Responsabilidade Única**
```php
// ❌ Evitar - Método fazendo tudo
public function processOrder(): void
{
    // validar dados
    // calcular preços
    // salvar no banco
    // enviar email
    // gerar relatório
}

// ✅ Preferir - Métodos específicos
public function processOrder(OrderDTO $dto): OrderResult
{
    $validatedData = $this->validateOrderData($dto);
    $calculatedOrder = $this->calculateOrderTotals($validatedData);
    $savedOrder = $this->saveOrder($calculatedOrder);
    $this->sendOrderConfirmation($savedOrder);
    $this->generateOrderReport($savedOrder);
    
    return OrderResult::fromOrder($savedOrder);
}
```

#### 🔢 **Constants e Enums**
```php
// ✅ Usar enums para constantes
enum OrderStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
}

// ✅ Usar em validações
public function canCancelOrder(Order $order): bool
{
    return in_array($order->status, [
        OrderStatus::PENDING,
        OrderStatus::CONFIRMED
    ]);
}
```

### 💾 **BANCO DE DADOS E TRANSAÇÕES**

#### 🔄 **Padrão de Transação com Savepoints**
```php
// Adapter para Laravel baseado no padrão Dourado
public function execute(CreateOrderDTO $dto): OrderDTO
{
    return DB::transaction(function () use ($dto) {
        $savepointName = 'SP_CREATE_ORDER_' . Str::random(8);
        
        DB::statement("SAVEPOINT {$savepointName}");
        
        try {
            $order = $this->createOrder($dto);
            $this->createOrderItems($order, $dto->items);
            $this->updateInventory($dto->items);
            
            return OrderDTO::fromModel($order);
            
        } catch (Exception $e) {
            DB::statement("ROLLBACK TO SAVEPOINT {$savepointName}");
            throw $e;
        }
    });
}
```

#### 🔍 **Validação de Existência Obrigatória**
```php
// ✅ Sempre validar antes de operações
public function updateUser(int $userId, UpdateUserDTO $dto): UserDTO
{
    $user = User::find($userId);
    
    if (!$user) {
        throw new NotFoundException("Usuário com ID {$userId} não encontrado");
    }
    
    if (!$user->is_active) {
        throw new BusinessRuleException('Usuário inativo não pode ser atualizado');
    }
    
    $user->update($dto->toArray());
    
    return UserDTO::fromModel($user->fresh());
}
```

### 📚 **DOCUMENTAÇÃO E SWAGGER**

#### 🎯 **Controller com Documentação Completa**
```php
/**
 * @OA\Post(
 *     path="/api/users",
 *     summary="Criar novo usuário",
 *     description="Cria um novo usuário no sistema com validação completa",
 *     operationId="createUser",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Dados do usuário",
 *         @OA\JsonContent(
 *             required={"name","email","password"},
 *             @OA\Property(property="name", type="string", example="João Silva"),
 *             @OA\Property(property="email", type="string", format="email", example="joao@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="senha123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Usuário criado com sucesso",
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Erro de validação",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
 *     )
 * )
 */
public function store(CreateUserRequest $request): UserResource
{
    $dto = CreateUserDTO::fromRequest($request);
    $user = $this->createUserUseCase->execute($dto);
    return new UserResource($user);
}
```

### 📝 **TEMPLATE DE DOCUMENTAÇÃO PARA TAREFAS**

**Após implementar endpoint, SEMPRE gerar documentação:**

```markdown
## 📌 Endpoint POST /api/users

### Descrição
Cria um novo usuário no sistema com validação completa de dados e envio de email de boas-vindas.

### URL
```
POST /api/users
```

### Headers
- `Content-Type: application/json`
- `Accept: application/json`

### Request Body
```json
{
  "name": "João Silva",
  "email": "joao@example.com",
  "password": "senha123",
  "role": "USER"
}
```

### Exemplos de Requisição
```bash
curl -X POST "http://localhost:8080/api/users" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "João Silva",
    "email": "joao@example.com",
    "password": "senha123"
  }'
```

### Resposta de Sucesso (201)
```json
{
  "success": true,
  "message": "Usuário criado com sucesso",
  "data": {
    "id": "uuid-here",
    "name": "João Silva",
    "email": "joao@example.com",
    "role": "USER",
    "is_active": true,
    "created_at": "2024-01-01T00:00:00Z"
  }
}
```

### Respostas de Erro
- `400 Bad Request`: Dados inválidos
- `422 Unprocessable Entity`: Validação falhou
- `500 Internal Server Error`: Erro do servidor

### Validações
- Nome: obrigatório, mínimo 2 caracteres
- Email: obrigatório, formato válido, único no sistema
- Senha: obrigatório, mínimo 8 caracteres
- Role: opcional, default "USER"

### Casos de Uso
1. Registro de novo cliente
2. Criação de usuário administrativo
3. Importação de dados de usuários
```

### ✅ **CHECKLIST DE IMPLEMENTAÇÃO**

#### **Antes de Implementar:**
- [ ] Ler documentação e padrões estabelecidos
- [ ] Verificar se não há comentários no código
- [ ] Confirmar que não vai modificar código de outros devs
- [ ] Validar estrutura de módulos seguindo padrão

#### **Durante Implementação:**
- [ ] Código sem comentários
- [ ] Seguir padrões Clean Architecture
- [ ] Usar early returns
- [ ] Constantes descritivas
- [ ] Validações defensivas
- [ ] Logs contextualizados
- [ ] Transações com savepoints quando necessário

#### **Antes de Commit:**
- [ ] Executar `composer test` (se houver testes)
- [ ] Executar `php artisan route:list` para verificar rotas
- [ ] Testar no Swagger/Postman
- [ ] Verificar que build passa: `composer install --no-dev`
- [ ] Remover todos os comentários
- [ ] **🚨 VERIFICAR: Mensagem NÃO menciona IA, Claude, ChatGPT ou similares**
- [ ] Verificar mensagem de commit em português
- [ ] Confirmar referência ao ticket
- [ ] **📝 ATUALIZAR CHANGELOG.md com mudanças implementadas**
- [ ] Gerar documentação para stakeholders

#### **🚨 VERIFICAÇÃO CRÍTICA DE COMMITS:**

**Antes de fazer push, SEMPRE verificar:**

```bash
# Ver último commit
git log -1 --oneline

# Verificar se NÃO contém palavras proibidas:
git log -1 | grep -i "claude\|chatgpt\|ia\|ai\|artificial\|generated\|assistente\|ajuda.*ia\|co-authored.*ai"

# Se retornar algo = COMMIT INVÁLIDO - Corrigir imediatamente
```

**Se encontrar menção proibida:**
```bash
# Corrigir último commit
git commit --amend -m "[PROJ-123] feat: nova mensagem limpa sem menções"

# Força push (apenas se não foi compartilhado ainda)
git push --force-with-lease
```

**🔍 Script de Verificação Automática:**
```bash
#!/bin/bash
# check-commit.sh
COMMIT_MSG=$(git log -1 --pretty=%B)
FORBIDDEN_WORDS="claude|chatgpt|ia|ai|artificial|generated|assistente|co-authored.*ai"

if echo "$COMMIT_MSG" | grep -i "$FORBIDDEN_WORDS" > /dev/null; then
    echo "🚨 ERRO: Commit contém palavras proibidas!"
    echo "Mensagem: $COMMIT_MSG"
    echo "Corrija antes de fazer push!"
    exit 1
fi

echo "✅ Commit aprovado - sem menções proibidas"
```

#### **📝 CHANGELOG - Atualização Obrigatória:**

**🚨 REGRA CRÍTICA:** Após cada sessão de trabalho ou funcionalidade implementada, o CHANGELOG.md DEVE ser atualizado antes do commit.

**Processo Obrigatório:**
1. **Implementar funcionalidade** seguindo Clean Architecture
2. **Testar e validar** todas as mudanças 
3. **Atualizar CHANGELOG.md** com as mudanças implementadas
4. **Fazer commits** divididos por responsabilidade
5. **Criar tag** de versão quando apropriado

**Formato do CHANGELOG:**
```markdown
## [X.Y.Z] - AAAA-MM-DD

### Adicionado
- **Nome da Funcionalidade**
  - Descrição detalhada da implementação
  - Benefícios e impacto da mudança
  - Integração com outros módulos

### Melhorado
- **Otimização específica**
  - Performance melhorada
  - UX aprimorada

### Corrigido
- **Bug específico**
  - Descrição do problema resolvido
  - Impacto da correção

### Técnico
- **Mudanças de arquitetura**
  - Refatorações importantes
  - Atualizações de dependências
```

**Quando Atualizar:**
- ✅ **Após implementar nova funcionalidade** (Products CRUD, Orders, etc.)
- ✅ **Após corrigir bugs importantes**
- ✅ **Após melhorias de performance**
- ✅ **Após mudanças de arquitetura**
- ✅ **Antes de finalizar sessão de desenvolvimento**

**Quando Criar Tag:**
- 🏷️ **Funcionalidade principal completa** (ex: v0.2.0 - CRUD Products)
- 🏷️ **Milestone do projeto** (ex: v1.0.0 - Mini ERP Completo)
- 🏷️ **Release candidate** (ex: v1.0.0-rc1)

**Comandos de Versionamento:**
```bash
# Após atualizar CHANGELOG.md
git add CHANGELOG.md
git commit -m "[PROJ-XXX] docs: atualiza CHANGELOG versão X.Y.Z"

# Criar tag de versão
git tag -a v0.2.0 -m "Versão 0.2.0 - CRUD Products implementado"
git push origin v0.2.0

# Verificar tags
git tag -l
```

#### **Comandos Essenciais:**
```bash
# Desenvolvimento
make up                    # Subir ambiente Docker
make shell                 # Acessar container
php artisan serve         # Servidor local (alternativa)

# Qualidade
composer test             # Executar testes
composer analyse          # Análise estática (PHPStan)
composer format           # Formatação de código (PHP CS Fixer)

# 🚨 COMMIT - VERIFICAÇÃO OBRIGATÓRIA
make check-commit         # Verificar se commit não menciona IA
git log -1 | grep -i "claude\|ia\|ai"  # Verificação manual

# Documentação
php artisan docs:generate # Gerar Swagger
make swagger              # Via Docker

# Banco de dados
php artisan migrate:fresh --seed  # Resetar banco
php artisan migrate:status        # Status das migrations

# Workflow recomendado antes de push
make test                 # Executar testes
make check-commit         # 🚨 VERIFICAR COMMIT 
git push                  # Fazer push
```

### 🎯 **RESULTADO ESPERADO**

Toda implementação deve resultar em:
- ✅ **Código limpo**: Sem comentários, autoexplicativo
- ✅ **Arquitetura consistente**: Seguindo padrões estabelecidos
- ✅ **Documentação rica**: Swagger completo
- ✅ **Validações robustas**: Defensive programming
- ✅ **Commits organizados**: Em português, com ticket
- ✅ **Testes funcionais**: Build passando
- ✅ **Performance otimizada**: Queries eficientes, cache quando necessário

---

**⚠️ IMPORTANTE:** Estas regras são **OBRIGATÓRIAS** e baseadas nos padrões consolidados dos projetos Dourado. Seguir rigorosamente garante consistência, qualidade e manutenibilidade do código.