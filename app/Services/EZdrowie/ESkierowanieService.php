<?php

namespace App\Services\EZdrowie;

use SoapClient;
use SoapHeader;
use Exception;

class ESkierowanieService
{
    protected string $wsdl = 'https://sus.ezdrowie.gov.pl/services/ObslugaSkierowaniaWS?wsdl';
    protected string $certPath;
    protected string $certPass;

    public function __construct(string $certPath, string $certPass)
    {
        $this->certPath = $certPath;
        $this->certPass = $certPass;
    }

    public function getByPesel(int $pesel): array
    {
        try {
            $client = new SoapClient($this->wsdl, [
                'local_cert' => $this->certPath,
                'passphrase' => $this->certPass,
                'trace' => 1,
                'exceptions' => true,
            ]);

            $header = new SoapHeader(
                'http://schemas.xmlsoap.org/ws/2002/12/secext',
                'Security',
                new \SoapVar('<wsse:Security><wsse:UsernameToken><wsse:Username>CERT_SUBJECT</wsse:Username></wsse:UsernameToken></wsse:Security>', XSD_ANYXML),
                true
            );
            $client->__setSoapHeaders([$header]);

            $response = $client->__soapCall('WyszukajSkierowania', [[
                'identyfikatorPacjenta' => ['PESEL' => $pesel],
            ]]);

            return json_decode(json_encode($response), true);
        } catch (Exception $e) {
            report($e);
            return ['error' => $e->getMessage()];
        }
    }
}
