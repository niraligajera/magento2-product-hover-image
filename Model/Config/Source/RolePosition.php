<?php
/**
 * @author    Ethnic
 * @copyright Copyright (c) Ethnic
 * @package   Ethnic_RollOverImage
 */

declare(strict_types=1);

namespace Ethnic\RollOverImage\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Option\ArrayInterface;

class RolePosition implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => __('1')],
            ['value' => '1', 'label' => __('2')],
            ['value' => '2', 'label' => __('3')],
            ['value' => '3', 'label' => __('4')]
        ];
    }
}