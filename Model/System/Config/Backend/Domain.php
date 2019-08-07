<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\GoogleSignOn\Model\System\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;

class Domain extends Value
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param SerializerInterface $serializer
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        SerializerInterface $serializer,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );

        $this->serializer = $serializer;
    }

    /**
     * Filter input data
     *
     * @param array $value
     * @return array
     */
    private function filterInputData(array $value): array
    {
        $out = [];

        foreach ($value as $rowId => $row) {
            if (is_array($row) && !empty($row['domain'])) {
                $out[$rowId] = $row;
            }
        }

        return $out;
    }

    /**
     * @inheritDoc
     */
    protected function _afterLoad()
    {
        try {
            $this->setValue($this->serializer->unserialize($this->getValue()));
        } catch (\InvalidArgumentException $e) {
            $this->setValue('');
        }
    }

    /**
     * @inheritDoc
     */
    public function beforeSave()
    {
        $value = $this->filterInputData($this->getValue());
        $this->setValue($this->serializer->serialize($value));
    }
}
