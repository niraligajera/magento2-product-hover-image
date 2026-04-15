<?php
/**
 * @author    Ethnic
 * @copyright Copyright (c) Ethnic
 * @package   Ethnic_RollOverImage
 */

declare(strict_types=1);

namespace Ethnic\RollOverImage\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Ethnic\RollOverImage\Helper\Data;

class ConfigResolver implements ResolverInterface
{
    /**
     * @var Data
     */
    private Data $helper;

    /**
     * ConfigResolver constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Resolve configuration data identically to backend scope values
     *
     * @param Field       $field
     * @param mixed       $context
     * @param ResolveInfo $info
     * @param array|null  $value
     * @param array|null  $args
     * @return array
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): array {
        return [
            'enabled' => $this->helper->isEnabled(),
            'animation' => $this->helper->isAnimationEnabled(),
            'animation_duration' => $this->helper->getAnimationDuration(),
            'lazy_load' => $this->helper->isLazyLoadEnabled(),
            'image_role' => $this->helper->getImageRole(),
            'enabled_sections' => $this->helper->getEnabledSections()
        ];
    }
}
