<?php

namespace App\Schemas;

use Prism\Prism\Schema\EnumSchema;
use Prism\Prism\Schema\NumberSchema;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\StringSchema;

class FaturaSchema
{
    public static function schema(): ObjectSchema
    {
        return new ObjectSchema(
            name: 'fatura',
            description: 'Fatura veya makbuz belgesi için extraction schema',
            properties: [
                new StringSchema(
                    name: 'vkn',
                    description: 'Vergi Kimlik Numarası (10 hane)',
                    nullable: false
                ),
                new StringSchema(
                    name: 'fatura_no',
                    description: 'Fatura veya belge numarası',
                    nullable: false
                ),
                new StringSchema(
                    name: 'tarih',
                    description: 'Belge tarihi YYYY-MM-DD formatında',
                    nullable: false
                ),
                new NumberSchema(
                    name: 'kdv_oran',
                    description: 'KDV oranı (örn: 20, 10, 1 - yüzde işareti olmadan)',
                    nullable: false
                ),
                new NumberSchema(
                    name: 'kdv_tutar',
                    description: 'KDV tutarı',
                    nullable: false
                ),
                new NumberSchema(
                    name: 'net_tutar',
                    description: 'Net tutar (KDV hariç)',
                    nullable: false
                ),
                new NumberSchema(
                    name: 'toplam',
                    description: 'Toplam tutar (KDV dahil)',
                    nullable: false
                ),
                new StringSchema(
                    name: 'firma_adi',
                    description: 'Belgeyi düzenleyen firma adı',
                    nullable: false
                ),
                new StringSchema(
                    name: 'aciklama',
                    description: 'Belge açıklaması veya notlar (varsa)',
                    nullable: true
                ),
                new EnumSchema(
                    name: 'belge_turu',
                    description: 'Belge türü',
                    options: ['fatura', 'makbuz']
                ),
                new NumberSchema(
                    name: 'confidence',
                    description: 'Extraction güvenilirlik skoru (0-1 arası)',
                    nullable: false,
                    minimum: 0,
                    maximum: 1
                ),
            ],
            requiredFields: [
                'vkn',
                'fatura_no',
                'tarih',
                'kdv_oran',
                'kdv_tutar',
                'net_tutar',
                'toplam',
                'firma_adi',
                'belge_turu',
                'confidence',
            ],
            allowAdditionalProperties: false
        );
    }
}
