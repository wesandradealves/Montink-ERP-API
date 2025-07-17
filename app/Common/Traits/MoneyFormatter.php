<?php

namespace App\Common\Traits;

trait MoneyFormatter
{
    /**
     * Formata valor monetário para Real brasileiro
     */
    public function formatMoney(float $value): string
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }

    /**
     * Formata valor monetário sem o símbolo R$
     */
    public function formatMoneyWithoutSymbol(float $value): string
    {
        return number_format($value, 2, ',', '.');
    }

    /**
     * Converte string formatada para float
     */
    public function parseMoneyToFloat(string $value): float
    {
        // Remove R$, espaços e converte vírgula para ponto
        $value = str_replace(['R$', ' ', '.'], '', $value);
        $value = str_replace(',', '.', $value);
        return (float) $value;
    }
}