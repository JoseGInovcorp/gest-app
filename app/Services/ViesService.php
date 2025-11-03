<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ViesService
{
    private const VIES_URL = 'http://ec.europa.eu/taxation_customs/vies/services/checkVatService';
    
    /**
     * Validar número de IVA europeu através da API VIES
     */
    public function validateVat(string $countryCode, string $vatNumber): array
    {
        try {
            // Limpar o número de IVA
            $vatNumber = preg_replace('/[^0-9A-Za-z]/', '', $vatNumber);
            
            // Remover prefixo do país se existir
            if (str_starts_with($vatNumber, $countryCode)) {
                $vatNumber = substr($vatNumber, strlen($countryCode));
            }

            $soapEnvelope = $this->buildSoapRequest($countryCode, $vatNumber);

            $response = Http::withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => ''
            ])->timeout(30)->post(self::VIES_URL, $soapEnvelope);

            if ($response->failed()) {
                throw new Exception('Erro na comunicação com VIES: ' . $response->status());
            }

            return $this->parseSoapResponse($response->body());

        } catch (Exception $e) {
            Log::error('Erro na validação VIES', [
                'country_code' => $countryCode,
                'vat_number' => $vatNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'valid' => false,
                'error' => $e->getMessage(),
                'country_code' => $countryCode,
                'vat_number' => $vatNumber,
                'request_date' => now()->toISOString(),
            ];
        }
    }

    /**
     * Construir pedido SOAP para VIES
     */
    private function buildSoapRequest(string $countryCode, string $vatNumber): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" 
               xmlns:tns1="urn:ec.europa.eu:taxud:vies:services:checkVat:types" 
               xmlns:impl="urn:ec.europa.eu:taxud:vies:services:checkVat">
    <soap:Header/>
    <soap:Body>
        <tns1:checkVat xmlns:tns1="urn:ec.europa.eu:taxud:vies:services:checkVat:types">
            <tns1:countryCode>{$countryCode}</tns1:countryCode>
            <tns1:vatNumber>{$vatNumber}</tns1:vatNumber>
        </tns1:checkVat>
    </soap:Body>
</soap:Envelope>
XML;
    }

    /**
     * Fazer parse da resposta SOAP do VIES
     */
    private function parseSoapResponse(string $xmlResponse): array
    {
        try {
            $xml = simplexml_load_string($xmlResponse, 'SimpleXMLElement', LIBXML_NOCDATA);
            
            if ($xml === false) {
                throw new Exception('Resposta XML inválida do VIES');
            }

            $namespaces = $xml->getNamespaces(true);
            $body = $xml->children('soap', true)->Body;
            
            if (isset($body->children('')->checkVatResponse)) {
                $response = $body->children('')->checkVatResponse;
                
                return [
                    'valid' => (bool) $response->valid,
                    'country_code' => (string) $response->countryCode,
                    'vat_number' => (string) $response->vatNumber,
                    'company_name' => (string) ($response->name ?? ''),
                    'company_address' => (string) ($response->address ?? ''),
                    'request_date' => (string) $response->requestDate,
                    'raw_response' => $xmlResponse,
                ];
            }

            // Verificar se há fault (erro)
            if (isset($body->children('soap', true)->Fault)) {
                $fault = $body->children('soap', true)->Fault;
                throw new Exception('VIES Fault: ' . $fault->faultstring);
            }

            throw new Exception('Formato de resposta VIES inesperado');

        } catch (Exception $e) {
            throw new Exception('Erro ao processar resposta VIES: ' . $e->getMessage());
        }
    }

    /**
     * Verificar se um país está na EU e suporta VIES
     */
    public static function isViesCountry(string $countryCode): bool
    {
        $viesCountries = [
            'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'EL', 'ES',
            'FI', 'FR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT',
            'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK', 'XI' // XI = Ireland do Norte
        ];

        return in_array(strtoupper($countryCode), $viesCountries);
    }

    /**
     * Obter formato esperado de VAT por país
     */
    public static function getVatFormat(string $countryCode): string
    {
        return match (strtoupper($countryCode)) {
            'PT' => 'PT999999999',
            'ES' => 'ESX99999999 ou ES99999999X',
            'FR' => 'FRXX999999999',
            'DE' => 'DE999999999',
            'IT' => 'IT99999999999',
            'NL' => 'NL999999999B99',
            'BE' => 'BE0999999999',
            default => 'Consulte as regras específicas do país'
        };
    }
}
