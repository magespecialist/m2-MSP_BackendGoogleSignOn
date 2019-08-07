<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\GoogleSignOn\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class Domain extends AbstractFieldArray
{
    /**
     * @var Rule
     */
    private $ruleRenderer;

    /**
     * Get Rule renderer
     *
     * @return Rule
     * @throws LocalizedException
     */
    private function getRuleRenderer(): Rule
    {
        if (!$this->ruleRenderer) {
            $this->ruleRenderer = $this->getLayout()->createBlock(
                Rule::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->ruleRenderer->setClass('rule_select');
        }

        return $this->ruleRenderer;
    }

    /**
     * @inheritDoc
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn(
            'domain',
            [
                'label' => __('Domain')
            ]
        );
        $this->addColumn(
            'rule',
            [
                'label' => __('Rule'),
                'renderer' => $this->getRuleRenderer()
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Domain');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->getRuleRenderer()->calcOptionHash($row->getData('rule'))] =
            'selected="selected"';

        $row->setData('option_extra_attrs', $optionExtraAttr);
    }
}
