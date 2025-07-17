<?php

namespace App\Common\Traits;

trait DocumentFormatter
{
    /**
     * Formata CEP para o padrão brasileiro (00000-000)
     */
    public function formatCep(string $cep): string
    {
        $cep = preg_replace('/\D/', '', $cep);
        
        if (strlen($cep) !== 8) {
            return $cep;
        }
        
        return substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
    }

    /**
     * Formata CPF para o padrão brasileiro (000.000.000-00)
     */
    public function formatCpf(string $cpf): string
    {
        $cpf = preg_replace('/\D/', '', $cpf);
        
        if (strlen($cpf) !== 11) {
            return $cpf;
        }
        
        return substr($cpf, 0, 3) . '.' . 
               substr($cpf, 3, 3) . '.' . 
               substr($cpf, 6, 3) . '-' . 
               substr($cpf, 9, 2);
    }

    /**
     * Remove formatação de CEP
     */
    public function unformatCep(string $cep): string
    {
        return preg_replace('/\D/', '', $cep);
    }

    /**
     * Remove formatação de CPF
     */
    public function unformatCpf(string $cpf): string
    {
        return preg_replace('/\D/', '', $cpf);
    }

    /**
     * Valida formato de CEP
     */
    public function isValidCepFormat(string $cep): bool
    {
        $cep = $this->unformatCep($cep);
        return strlen($cep) === 8 && is_numeric($cep);
    }

    /**
     * Valida formato de CPF
     */
    public function isValidCpfFormat(string $cpf): bool
    {
        $cpf = $this->unformatCpf($cpf);
        return strlen($cpf) === 11 && is_numeric($cpf);
    }
}