<?php

namespace App\Modules\Address\Services;

use Illuminate\Support\Facades\Http;

class ViaCepService
{
    private const BASE_URL = 'https://viacep.com.br/ws';

    public function getAddressByCep(string $cep): ?array
    {
        $cep = preg_replace('/\D/', '', $cep);
        
        if (strlen($cep) !== 8) {
            throw new \InvalidArgumentException('CEP deve conter 8 dígitos');
        }

        $response = Http::timeout(10)->get(self::BASE_URL . "/{$cep}/json/");

        if (!$response->successful()) {
            throw new \Exception('Erro ao consultar CEP na API ViaCEP');
        }

        $data = $response->json();

        if (isset($data['erro']) && $data['erro']) {
            return null;
        }

        return [
            'cep' => $data['cep'],
            'logradouro' => $data['logradouro'],
            'complemento' => $data['complemento'],
            'bairro' => $data['bairro'],
            'localidade' => $data['localidade'],
            'uf' => $data['uf'],
            'ibge' => $data['ibge'],
            'gia' => $data['gia'],
            'ddd' => $data['ddd'],
            'siafi' => $data['siafi'],
        ];
    }

    public function validateCep(string $cep): bool
    {
        try {
            return $this->getAddressByCep($cep) !== null;
        } catch (\Exception $e) {
            return false;
        }
    }
}