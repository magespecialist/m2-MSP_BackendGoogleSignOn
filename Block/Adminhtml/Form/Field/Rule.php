<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\GoogleSignOn\Block\Adminhtml\Form\Field;

use Magento\Authorization\Model\ResourceModel\Role;
use Magento\Authorization\Model\ResourceModel\Role\CollectionFactory;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class Rule extends Select
{
    /**
     * @var CollectionFactory
     */
    private $roleCollectionFactory;

    /**
     * @param CollectionFactory $roleCollectionFactory
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        CollectionFactory $roleCollectionFactory,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->roleCollectionFactory = $roleCollectionFactory;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value): Rule
    {
        return $this->setName($value);
    }

    /**
     * @inheritDoc
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $roles = $this->roleCollectionFactory->create();
            $roles->addFieldToFilter('role_type', 'G');

            $this->addOption(
                'force',
                __('Force login with Google')
            );

            foreach ($roles as $role) {
                /** @var Role $role */
                $this->addOption(
                    $role->getRoleId(),
                    __('Create user in %1', $role->getRoleName())
                );
            }
        }

        return parent::_toHtml();
    }
}
