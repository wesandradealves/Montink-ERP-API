<?php

namespace App\Common\Enums;

enum ResponseMessage: string
{
    // Mensagens gerais
    case OPERATION_SUCCESS = 'messages.general.operation_success';
    case OPERATION_ERROR = 'messages.general.operation_error';
    case RESOURCE_NOT_FOUND = 'messages.general.resource_not_found';
    case VALIDATION_ERROR = 'messages.general.validation_error';
    case DEFAULT_SUCCESS = 'messages.general.default_success';
    case DEFAULT_ERROR = 'messages.general.default_error';
    case DEFAULT_CREATED = 'messages.general.default_created';
    
    // Mensagens de produtos
    case PRODUCT_CREATED = 'messages.product.created';
    case PRODUCT_UPDATED = 'messages.product.updated';
    case PRODUCT_DELETED = 'messages.product.deleted';
    case PRODUCT_FOUND = 'messages.product.found';
    case PRODUCT_NOT_FOUND = 'messages.product.not_found';
    case PRODUCT_STOCK_NOT_FOUND = 'messages.product.stock_not_found';
    case PRODUCT_SKU_ALREADY_EXISTS = 'messages.product.sku_already_exists';
    case PRODUCT_PRICE_MIN_ERROR = 'messages.product.price_min_error';
    case PRODUCT_VARIATION_REQUIRED = 'messages.product.variation_required';
    
    // Mensagens de pedidos
    case ORDER_CREATED = 'messages.order.created';
    case ORDER_FOUND = 'messages.order.found';
    case ORDER_STATUS_UPDATED = 'messages.order.status_updated';
    case ORDER_CANCELLED = 'messages.order.cancelled';
    case ORDER_NOT_FOUND = 'messages.order.not_found';
    case ORDER_EMPTY_CART = 'messages.order.empty_cart';
    case ORDER_CANNOT_CANCEL = 'messages.order.cannot_cancel';
    case ORDER_INVALID_STATUS = 'messages.order.invalid_status';
    case ORDER_DEFAULT_CUSTOMER_NAME = 'messages.order.default_customer_name';
    case ORDER_DEFAULT_CUSTOMER_EMAIL = 'messages.order.default_customer_email';
    case ORDER_DEFAULT_CUSTOMER_CEP = 'messages.order.default_customer_cep';
    case ORDER_DEFAULT_CUSTOMER_ADDRESS = 'messages.order.default_customer_address';
    case ORDER_DEFAULT_CUSTOMER_NEIGHBORHOOD = 'messages.order.default_customer_neighborhood';
    case ORDER_DEFAULT_CUSTOMER_CITY = 'messages.order.default_customer_city';
    case ORDER_DEFAULT_CUSTOMER_STATE = 'messages.order.default_customer_state';
    
    // Mensagens de carrinho
    case CART_ITEM_ADDED = 'messages.cart.item_added';
    case CART_ITEM_UPDATED = 'messages.cart.item_updated';
    case CART_ITEM_REMOVED = 'messages.cart.item_removed';
    case CART_CLEARED = 'messages.cart.cleared';
    case CART_INSUFFICIENT_STOCK = 'messages.cart.insufficient_stock';
    case CART_ITEM_ID_REQUIRED = 'messages.cart.item_id_required';
    case CART_COUPON_CODE_REQUIRED = 'messages.cart.coupon_code_required';
    case PRODUCT_ADDED_TO_CART = 'messages.cart.product_added';
    
    // Mensagens de cupons
    case COUPON_CREATED = 'messages.coupon.created';
    case COUPON_UPDATED = 'messages.coupon.updated';
    case COUPON_DELETED = 'messages.coupon.deleted';
    case COUPON_FOUND = 'messages.coupon.found';
    case COUPON_NOT_FOUND = 'messages.coupon.not_found';
    case COUPON_INVALID = 'messages.coupon.invalid';
    case COUPON_EXPIRED = 'messages.coupon.expired';
    case COUPON_MINIMUM_NOT_MET = 'messages.coupon.minimum_not_met';
    case COUPON_USAGE_LIMIT_REACHED = 'messages.coupon.usage_limit_reached';
    case COUPON_ALREADY_EXISTS = 'messages.coupon.already_exists';
    case COUPON_INVALID_WITH_REASON = 'messages.coupon.invalid_with_reason';
    case COUPON_INACTIVE = 'messages.coupon.inactive';
    case COUPON_NOT_YET_VALID = 'messages.coupon.not_yet_valid';
    case COUPON_TYPE_FIXED = 'messages.coupon.type_fixed';
    case COUPON_TYPE_PERCENTAGE = 'messages.coupon.type_percentage';
    case COUPON_APPLIED_SUCCESSFULLY = 'messages.coupon.applied_successfully';
    
    // Mensagens de endereço
    case ADDRESS_FOUND = 'messages.address.found';
    case ADDRESS_NOT_FOUND = 'messages.address.not_found';
    case ADDRESS_CEP_INVALID = 'messages.address.cep_invalid';
    case ADDRESS_CEP_INVALID_FORMAT = 'messages.address.cep_invalid_format';
    case ADDRESS_CEP_API_ERROR = 'messages.address.cep_api_error';
    
    // Mensagens de estoque
    case STOCK_INSUFFICIENT = 'messages.stock.insufficient';
    case STOCK_UPDATED = 'messages.stock.updated';
    case STOCK_INSUFFICIENT_AVAILABLE = 'messages.stock.insufficient_available';
    
    // Mensagens de validação
    case VALIDATION_REQUIRED = 'messages.validation.required';
    case VALIDATION_STRING = 'messages.validation.string';
    case VALIDATION_NUMERIC = 'messages.validation.numeric';
    case VALIDATION_INTEGER = 'messages.validation.integer';
    case VALIDATION_EMAIL = 'messages.validation.email';
    case VALIDATION_MIN = 'messages.validation.min';
    case VALIDATION_MAX = 'messages.validation.max';
    case VALIDATION_UNIQUE = 'messages.validation.unique';
    case VALIDATION_EXISTS = 'messages.validation.exists';
    case VALIDATION_BOOLEAN = 'messages.validation.boolean';
    case VALIDATION_ARRAY = 'messages.validation.array';
    case VALIDATION_DATE = 'messages.validation.date';
    case VALIDATION_DATE_FORMAT = 'messages.validation.date_format';
    case VALIDATION_IN = 'messages.validation.in';
    case VALIDATION_DECIMAL = 'messages.validation.decimal';
    case VALIDATION_SIZE = 'messages.validation.size';
    case VALIDATION_GT = 'messages.validation.gt';
    case VALIDATION_AFTER = 'messages.validation.after';
    case VALIDATION_CPF_FORMAT = 'messages.validation.cpf_format';
    case VALIDATION_CEP_FORMAT = 'messages.validation.cep_format';
    case VALIDATION_STATE_FORMAT = 'messages.validation.state_format';
    
    // Mensagens de email
    case EMAIL_ORDER_CONFIRMATION_SUBJECT = 'messages.email.order_confirmation_subject';
    case EMAIL_SEND_ERROR = 'messages.email.send_error';
    case EMAIL_TEMPLATE_NOT_FOUND = 'messages.email.template_not_found';
    
    // Mensagens de log
    case LOG_ORDER_REMOVED_WEBHOOK = 'messages.log.order_removed_webhook';
    case LOG_ORDER_STATUS_UPDATED_WEBHOOK = 'messages.log.order_status_updated_webhook';
    
    // Mensagens de autenticação
    case AUTH_LOGIN_SUCCESS = 'messages.auth.login_success';
    case AUTH_LOGOUT_SUCCESS = 'messages.auth.logout_success';
    case AUTH_REGISTER_SUCCESS = 'messages.auth.register_success';
    case AUTH_INVALID_CREDENTIALS = 'messages.auth.invalid_credentials';
    case AUTH_USER_NOT_FOUND = 'messages.auth.user_not_found';
    case AUTH_USER_INACTIVE = 'messages.auth.user_inactive';
    case AUTH_TOKEN_INVALID = 'messages.auth.token_invalid';
    case AUTH_TOKEN_EXPIRED = 'messages.auth.token_expired';
    case AUTH_TOKEN_REFRESHED = 'messages.auth.token_refreshed';
    case AUTH_UNAUTHORIZED = 'messages.auth.unauthorized';
    case AUTH_EMAIL_ALREADY_EXISTS = 'messages.auth.email_already_exists';
    case AUTH_TOKEN_NOT_PROVIDED = 'messages.auth.token_not_provided';
    case AUTH_TOKEN_INVALID_FORMAT = 'messages.auth.token_invalid_format';
    case AUTH_TOKEN_SIGNATURE_INVALID = 'messages.auth.token_signature_invalid';
    case AUTH_TOKEN_PAYLOAD_INVALID = 'messages.auth.token_payload_invalid';
    
    public function get(array $replace = []): string
    {
        $message = config($this->value) ?? $this->getDefault();
        
        if (!empty($replace)) {
            foreach ($replace as $key => $value) {
                $message = str_replace(':' . $key, $value, $message);
            }
        }
        
        return $message;
    }
    
    private function getDefault(): string
    {
        return match($this) {
            // Mensagens gerais
            self::OPERATION_SUCCESS => 'Operação realizada com sucesso',
            self::OPERATION_ERROR => 'Ocorreu um erro ao processar a operação',
            self::RESOURCE_NOT_FOUND => 'Recurso não encontrado',
            self::VALIDATION_ERROR => 'Erro de validação',
            self::DEFAULT_SUCCESS => 'Success',
            self::DEFAULT_ERROR => 'Error',
            self::DEFAULT_CREATED => 'Criado com sucesso',
            
            // Mensagens de produtos
            self::PRODUCT_CREATED => 'Produto criado com sucesso',
            self::PRODUCT_UPDATED => 'Produto atualizado com sucesso',
            self::PRODUCT_DELETED => 'Produto excluído com sucesso',
            self::PRODUCT_FOUND => 'Produto encontrado com sucesso',
            self::PRODUCT_NOT_FOUND => 'Produto não encontrado',
            self::PRODUCT_STOCK_NOT_FOUND => 'Produto com identificador \'estoque\' não encontrado',
            self::PRODUCT_VARIATION_REQUIRED => 'Este produto possui variações. Especifique a variação desejada',
            self::PRODUCT_SKU_ALREADY_EXISTS => 'Este SKU já está em uso',
            self::PRODUCT_PRICE_MIN_ERROR => 'O preço deve ser maior que zero',
            
            // Mensagens de pedidos
            self::ORDER_CREATED => 'Pedido criado com sucesso',
            self::ORDER_FOUND => 'Pedido encontrado com sucesso',
            self::ORDER_STATUS_UPDATED => 'Status do pedido atualizado com sucesso',
            self::ORDER_CANCELLED => 'Pedido cancelado com sucesso',
            self::ORDER_NOT_FOUND => 'Pedido não encontrado',
            self::ORDER_EMPTY_CART => 'Carrinho vazio. Adicione produtos antes de finalizar o pedido.',
            self::ORDER_CANNOT_CANCEL => 'Pedido não pode ser cancelado no status atual: :status',
            self::ORDER_INVALID_STATUS => 'Status inválido: :status',
            self::ORDER_DEFAULT_CUSTOMER_NAME => 'Cliente',
            self::ORDER_DEFAULT_CUSTOMER_EMAIL => 'pedido@montystorepro.com',
            self::ORDER_DEFAULT_CUSTOMER_CEP => '00000-000',
            self::ORDER_DEFAULT_CUSTOMER_ADDRESS => 'Endereço não informado',
            self::ORDER_DEFAULT_CUSTOMER_NEIGHBORHOOD => 'Bairro',
            self::ORDER_DEFAULT_CUSTOMER_CITY => 'Cidade',
            self::ORDER_DEFAULT_CUSTOMER_STATE => 'SP',
            
            // Mensagens de carrinho
            self::CART_ITEM_ADDED => 'Produto adicionado ao carrinho',
            self::CART_ITEM_UPDATED => 'Quantidade atualizada no carrinho',
            self::CART_ITEM_REMOVED => 'Produto removido do carrinho',
            self::CART_CLEARED => 'Carrinho limpo com sucesso',
            self::CART_INSUFFICIENT_STOCK => 'Estoque insuficiente para o produto',
            self::CART_ITEM_ID_REQUIRED => 'ID do item é obrigatório',
            self::CART_COUPON_CODE_REQUIRED => 'Código do cupom é obrigatório',
            self::PRODUCT_ADDED_TO_CART => 'Produto adicionado ao carrinho com sucesso',
            
            // Mensagens de cupons
            self::COUPON_CREATED => 'Cupom criado com sucesso',
            self::COUPON_UPDATED => 'Cupom atualizado com sucesso',
            self::COUPON_DELETED => 'Cupom excluído com sucesso',
            self::COUPON_FOUND => 'Cupom encontrado com sucesso',
            self::COUPON_NOT_FOUND => 'Cupom não encontrado',
            self::COUPON_INVALID => 'Cupom inválido',
            self::COUPON_EXPIRED => 'Cupom expirado',
            self::COUPON_MINIMUM_NOT_MET => 'Valor mínimo não atingido para usar este cupom',
            self::COUPON_USAGE_LIMIT_REACHED => 'Limite de uso do cupom atingido',
            self::COUPON_ALREADY_EXISTS => 'Cupom com este código já existe',
            self::COUPON_INVALID_WITH_REASON => 'Cupom inválido: :reason',
            self::COUPON_INACTIVE => 'Cupom inativo',
            self::COUPON_NOT_YET_VALID => 'Cupom ainda não está válido',
            self::COUPON_TYPE_FIXED => 'Valor Fixo',
            self::COUPON_TYPE_PERCENTAGE => 'Porcentagem',
            
            // Mensagens de endereço
            self::ADDRESS_FOUND => 'Endereço encontrado com sucesso',
            self::ADDRESS_NOT_FOUND => 'CEP não encontrado',
            self::ADDRESS_CEP_INVALID => 'CEP inválido',
            self::ADDRESS_CEP_INVALID_FORMAT => 'CEP deve conter 8 dígitos',
            self::ADDRESS_CEP_API_ERROR => 'Erro ao consultar CEP na API ViaCEP',
            
            // Mensagens de estoque
            self::STOCK_INSUFFICIENT => 'Estoque insuficiente',
            self::STOCK_UPDATED => 'Estoque atualizado com sucesso',
            self::STOCK_INSUFFICIENT_AVAILABLE => 'Estoque insuficiente. Disponível: :available',
            
            // Mensagens de validação
            self::VALIDATION_REQUIRED => 'O campo :attribute é obrigatório',
            self::VALIDATION_STRING => 'O campo :attribute deve ser um texto',
            self::VALIDATION_NUMERIC => 'O campo :attribute deve ser um número',
            self::VALIDATION_INTEGER => 'O campo :attribute deve ser um número inteiro',
            self::VALIDATION_EMAIL => 'O campo :attribute deve ser um email válido',
            self::VALIDATION_MIN => 'O campo :attribute deve ser no mínimo :min',
            self::VALIDATION_MAX => 'O campo :attribute não pode ser maior que :max',
            self::VALIDATION_UNIQUE => 'Este :attribute já está em uso',
            self::VALIDATION_EXISTS => ':Attribute não encontrado',
            self::VALIDATION_BOOLEAN => 'O campo :attribute deve ser verdadeiro ou falso',
            self::VALIDATION_ARRAY => 'O campo :attribute deve ser uma lista',
            self::VALIDATION_DATE => 'O campo :attribute deve ser uma data válida',
            self::VALIDATION_DATE_FORMAT => 'O campo :attribute deve estar no formato :format',
            self::VALIDATION_IN => 'O campo :attribute selecionado é inválido',
            self::VALIDATION_DECIMAL => 'O campo :attribute deve ter :decimal casas decimais',
            self::VALIDATION_SIZE => 'O campo :attribute deve ter :size caracteres',
            self::VALIDATION_GT => 'O campo :attribute deve ser maior que :value',
            self::VALIDATION_AFTER => 'O campo :attribute deve ser uma data posterior a :date',
            
            // Mensagens de autenticação
            self::AUTH_LOGIN_SUCCESS => 'Login realizado com sucesso',
            self::AUTH_LOGOUT_SUCCESS => 'Logout realizado com sucesso',
            self::AUTH_REGISTER_SUCCESS => 'Usuário registrado com sucesso',
            self::AUTH_INVALID_CREDENTIALS => 'Credenciais inválidas',
            self::AUTH_USER_NOT_FOUND => 'Usuário não encontrado',
            self::AUTH_USER_INACTIVE => 'Usuário inativo',
            self::AUTH_TOKEN_INVALID => 'Token inválido',
            self::AUTH_TOKEN_EXPIRED => 'Token expirado',
            self::AUTH_TOKEN_REFRESHED => 'Token atualizado com sucesso',
            self::AUTH_UNAUTHORIZED => 'Não autorizado',
            self::AUTH_EMAIL_ALREADY_EXISTS => 'Este email já está em uso',
            self::AUTH_TOKEN_NOT_PROVIDED => 'Token não fornecido',
            self::AUTH_TOKEN_INVALID_FORMAT => 'Token em formato inválido',
            self::AUTH_TOKEN_SIGNATURE_INVALID => 'Assinatura do token inválida',
            self::AUTH_TOKEN_PAYLOAD_INVALID => 'Payload do token inválido',
            
            // Mensagens de validação customizadas
            self::VALIDATION_CPF_FORMAT => 'O CPF deve ter 14 caracteres (incluindo pontos e traço)',
            self::VALIDATION_CEP_FORMAT => 'O CEP deve ter 9 caracteres (incluindo traço)',
            self::VALIDATION_STATE_FORMAT => 'O estado deve ter 2 caracteres (ex: SP)',
            
            // Mensagens de email
            self::EMAIL_ORDER_CONFIRMATION_SUBJECT => 'Confirmação de Pedido #:orderNumber',
            self::EMAIL_SEND_ERROR => 'Erro ao enviar email de confirmação',
            self::EMAIL_TEMPLATE_NOT_FOUND => 'Template de email \':template\' não encontrado',
            
            // Mensagens de log
            self::LOG_ORDER_REMOVED_WEBHOOK => 'Pedido removido via webhook',
            self::LOG_ORDER_STATUS_UPDATED_WEBHOOK => 'Status do pedido atualizado via webhook',
        };
    }
}