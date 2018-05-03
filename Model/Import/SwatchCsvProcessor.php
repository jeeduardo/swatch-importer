<?php

namespace Josephson\SwatchImporter\Model\Import;

class SwatchCsvProcessor
{
    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * @var \Josephson\SwatchImporter\Model\Import\Swatch\AttributeOptionParser
     */
    protected $attributeOptionParser;

    /**
     * @var \Josephson\SwatchImporter\Model\Import\Swatch\ApiClient
     */
    protected $swatchApiClient;

    /**
     * Class construct
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param \Josephson\SwatchImporter\Model\Import\Swatch\AttributeOptionParser $attributeOptionParser
     * @param \Josephson\SwatchImporter\Model\Import\Swatch\ApiClient $swatchApiClient
     */
    public function __construct(
        \Magento\Framework\File\Csv $csvProcessor,
        \Josephson\SwatchImporter\Model\Import\Swatch\AttributeOptionParser $attributeOptionParser,
        \Josephson\SwatchImporter\Model\Import\Swatch\ApiClient $swatchApiClient
    ) {
        $this->csvProcessor = $csvProcessor;
        $this->attributeOptionParser = $attributeOptionParser;
        $this->swatchApiClient = $swatchApiClient;
    }

    /**
     * Process the given CSV file path
     * @param string $wholePathToFile
     */
    public function processCsvFile($wholePathToFile)
    {
        $importRawData = $this->csvProcessor->getData($wholePathToFile);

        foreach ($importRawData as $index => $row) {
            // parse CSV row so that it'll be ready to be added via API
            $apiRequestParams = $this->attributeOptionParser->parseToAttributeOptionApiRequestParams($row);

            // send API request to /rest/V1/products/attributes/:attributeCode/swatches
            // because for some reason, \Magento\Catalog\Api\ProductAttributeOptionManagementInterface::add() doesn't work! (so far)
            if ($this->swatchApiClient->sendPostRequest($apiRequestParams)) {
                echo sprintf("Option \"%s\" for attribute \"%s\" saved successfully.\n", $row[1], $row[0]);
            }
        }
    }
}
