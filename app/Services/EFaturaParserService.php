<?php

namespace App\Services;

use SimpleXMLElement;

class EFaturaParserService
{
    /**
     * Parse UBL-TR format e-Fatura XML file.
     */
    public function parseXml(string $xmlPath): array
    {
        $xml = simplexml_load_file($xmlPath);

        if ($xml === false) {
            throw new \RuntimeException("XML dosyası okunamadı: {$xmlPath}");
        }

        // Register UBL-TR namespaces
        $namespaces = [
            'cac' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            'cbc' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            'ext' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2',
        ];

        foreach ($namespaces as $prefix => $uri) {
            $xml->registerXPathNamespace($prefix, $uri);
        }

        // Extract supplier party (firma bilgileri)
        $supplierParty = $xml->xpath('//cac:AccountingSupplierParty/cac:Party')[0] ?? null;
        $supplierTaxScheme = $supplierParty?->xpath('cac:PartyTaxScheme')[0] ?? null;
        $vkn = (string) ($supplierTaxScheme?->xpath('cbc:TaxScheme/cbc:TaxTypeCode')[0] ?? '');

        // Get company name
        $partyName = $supplierParty?->xpath('cac:PartyName/cbc:Name')[0] ?? null;
        $firmaAdi = (string) ($partyName ?? '');

        // Extract invoice details
        $faturaNo = (string) ($xml->xpath('//cbc:ID')[0] ?? '');
        $tarih = (string) ($xml->xpath('//cbc:IssueDate')[0] ?? '');

        // Extract totals
        $legalMonetaryTotal = $xml->xpath('//cac:LegalMonetaryTotal')[0] ?? null;
        $netTutar = (float) ($legalMonetaryTotal?->xpath('cbc:LineExtensionAmount')[0] ?? 0);
        $kdvTutar = (float) ($legalMonetaryTotal?->xpath('cbc:TaxInclusiveAmount')[0] ?? 0) - $netTutar;
        $toplam = (float) ($legalMonetaryTotal?->xpath('cbc:TaxInclusiveAmount')[0] ?? 0);

        // Extract KDV rate from tax total
        $taxSubtotal = $xml->xpath('//cac:TaxTotal/cac:TaxSubtotal')[0] ?? null;
        $kdvOran = (float) ($taxSubtotal?->xpath('cbc:Percent')[0] ?? 0);

        // Extract description (first invoice line)
        $invoiceLine = $xml->xpath('//cac:InvoiceLine')[0] ?? null;
        $item = $invoiceLine?->xpath('cac:Item')[0] ?? null;
        $aciklama = (string) ($item?->xpath('cbc:Name')[0] ?? null);

        return [
            'vkn' => $vkn,
            'fatura_no' => $faturaNo,
            'tarih' => $tarih,
            'kdv_oran' => $kdvOran,
            'kdv_tutar' => $kdvTutar,
            'net_tutar' => $netTutar,
            'toplam' => $toplam,
            'firma_adi' => $firmaAdi,
            'aciklama' => $aciklama ?: null,
            'belge_turu' => 'fatura',
            'confidence' => 1.0,
            'validation_passed' => true,
            'validation_errors' => [],
        ];
    }
}
