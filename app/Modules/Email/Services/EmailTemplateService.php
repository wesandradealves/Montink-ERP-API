<?php

namespace App\Modules\Email\Services;

use App\Common\Base\BaseService;
use App\Common\Enums\ResponseMessage;
use App\Common\Traits\MoneyFormatter;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailTemplateService extends BaseService
{
    use MoneyFormatter;

    /**
     * Envia email usando template
     */
    public function send(string $template, string $to, array $data, ?string $toName = null): bool
    {
        try {
            $config = $this->getTemplateConfig($template);
            $subject = $this->prepareSubject($config, $data);
            
            // Se tiver template de view, usa ele
            if (isset($config['view'])) {
                Mail::send($config['view'], $data, function ($message) use ($to, $toName, $subject) {
                    $message->to($to, $toName)->subject($subject);
                });
            } else {
                // Senão, usa template de texto
                $content = $this->prepareTextContent($config, $data);
                Mail::raw($content, function ($message) use ($to, $toName, $subject) {
                    $message->to($to, $toName)->subject($subject);
                });
            }

            $this->logEmail($template, $to, true);
            return true;

        } catch (\Exception $e) {
            $this->logEmail($template, $to, false, $e->getMessage());
            return false;
        }
    }

    /**
     * Carrega configuração do template
     */
    private function getTemplateConfig(string $template): array
    {
        $config = config("email-templates.{$template}");
        
        if (!$config) {
            throw new \InvalidArgumentException(
                ResponseMessage::EMAIL_TEMPLATE_NOT_FOUND->get(['template' => $template])
            );
        }
        
        return $config;
    }

    /**
     * Prepara o assunto do email
     */
    private function prepareSubject(array $config, array $data): string
    {
        $subject = ResponseMessage::tryFrom($config['subject'])?->get($data) ?? $config['subject'];
        
        // Substitui variáveis no subject
        return $this->replaceVariables($subject, $data);
    }

    /**
     * Prepara conteúdo de texto do email
     */
    private function prepareTextContent(array $config, array $data): string
    {
        $content = '';
        
        foreach ($config['sections'] ?? [] as $sectionKey => $section) {
            $content .= $this->renderSection($section, $data) . "\n\n";
        }
        
        return trim($content);
    }

    /**
     * Renderiza uma seção do template
     */
    private function renderSection(array $section, array $data): string
    {
        $output = '';
        
        foreach ($section as $key => $value) {
            if (is_string($value)) {
                $message = ResponseMessage::tryFrom($value)?->get($data) ?? $value;
                $output .= $this->replaceVariables($message, $data) . "\n";
            } elseif (is_array($value) && $key === 'items' && isset($data['items'])) {
                $output .= $this->renderItems($data['items'], $value);
            }
        }
        
        return trim($output);
    }

    /**
     * Renderiza lista de items
     */
    private function renderItems(array $items, array $config): string
    {
        $output = '';
        
        if (isset($config['header'])) {
            $header = ResponseMessage::tryFrom($config['header'])?->get() ?? $config['header'];
            $output .= $header . "\n";
        }
        
        foreach ($items as $item) {
            $template = $config['template'] ?? '- :name x :quantity - :price';
            $line = $this->replaceVariables($template, $item);
            $output .= $line . "\n";
        }
        
        return $output;
    }

    /**
     * Substitui variáveis no texto
     */
    private function replaceVariables(string $text, array $data): string
    {
        foreach ($data as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $text = str_replace(":{$key}", $value, $text);
                $text = str_replace("{{{$key}}}", $value, $text);
            }
        }
        
        return $text;
    }

    /**
     * Registra envio de email
     */
    private function logEmail(string $template, string $to, bool $success, ?string $error = null): void
    {
        $message = $success 
            ? "Email '{$template}' enviado com sucesso para {$to}"
            : "Erro ao enviar email '{$template}' para {$to}: {$error}";
            
        if ($success) {
            Log::info($message);
        } else {
            Log::error($message);
        }
    }
}