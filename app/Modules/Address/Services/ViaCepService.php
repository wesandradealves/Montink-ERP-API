<?php

namespace App\Modules\Address\Services;

use App\Common\Enums\ResponseMessage;
use App\Common\Traits\DocumentFormatter;
use Illuminate\Support\Facades\Http;

class ViaCepService
{
    use DocumentFormatter;
    private const BASE_URL = 'https://viacep.com.br/ws';

    public function getAddressByCep(string $cep): ?array
    {
        $cep = $this->unformatCep($cep);
        
        if (!$this->isValidCepFormat($cep)) {
            throw new \InvalidArgumentException(ResponseMessage::ADDRESS_CEP_INVALID_FORMAT->get());
        }

        $response = Http::timeout(10)->get(self::BASE_URL . "/{$cep}/json/");

        if (!$response->successful()) {
            throw new \Exception(ResponseMessage::ADDRESS_CEP_API_ERROR->get());
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