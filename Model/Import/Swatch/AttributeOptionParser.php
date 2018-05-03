<?php

namespace Josephson\SwatchImporter\Model\Import\Swatch;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class AttributeOptionParser
{

    public function __construct(
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Parse given data in array into API request parameters
     * @param array $dataParams
     * @param return array
     */
    public function parseToAttributeOptionApiRequestParams($data)
    {
        $json = [
            'option' => [
                'isDefault' => false,
                'storeLabels' => [
                    [
                        'storeId' => $data[2],
                        'label' => $data[1]
                    ]
                ]
            ]
        ];

        return [
            'json' => $json,
            'attribute_code' => $data[0],
        ];
    }

}
