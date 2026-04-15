<?php
/**
 * @author    Ethnic
 * @copyright Copyright (c) Ethnic
 * @package   Ethnic_RollOverImage
 */

declare(strict_types=1);

namespace Ethnic\RollOverImage\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class AnimationDuration
 * Source model for CSS transition durations
 */
class AnimationDuration implements OptionSourceInterface
{
    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => '0.2', 'label' => __('0.2 Seconds (Fast)')],
            ['value' => '0.3', 'label' => __('0.3 Seconds')],
            ['value' => '0.5', 'label' => __('0.5 Seconds (Normal)')],
            ['value' => '0.8', 'label' => __('0.8 Seconds')],
            ['value' => '1.0', 'label' => __('1.0 Second (Slow)')],
            ['value' => '1.5', 'label' => __('1.5 Seconds')],
            ['value' => '2.0', 'label' => __('2.0 Seconds')],
        ];
    }
}
