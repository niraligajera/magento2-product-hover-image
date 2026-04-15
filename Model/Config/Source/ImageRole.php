<?php
/**
 * @author    Ethnic
 * @copyright Copyright (c) Ethnic
 * @package   Ethnic_RollOverImage
 */

declare(strict_types=1);

namespace Ethnic\RollOverImage\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Eav\Model\Config;
use Magento\Catalog\Model\Product;

/**
 * Class ImageRole
 * Source model for product image roles
 */
class ImageRole implements OptionSourceInterface
{
    /**
     * @var Config
     */
    private Config $eavConfig;

    /**
     * ImageRole constructor.
     *
     * @param Config $eavConfig
     */
    public function __construct(Config $eavConfig)
    {
        $this->eavConfig = $eavConfig;
    }

    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [
            ['value' => '_gallery_fallback_', 'label' => __('Use 2nd Gallery Image')]
        ];
        $entityType = $this->eavConfig->getEntityType(Product::ENTITY);
        $attributes = $entityType->getAttributeCollection()
            ->addFieldToFilter('frontend_input', 'media_image');

        foreach ($attributes as $attribute) {
            $options[] = [
                'value' => $attribute->getAttributeCode(),
                'label' => $attribute->getDefaultFrontendLabel()
            ];
        }

        return $options;
    }
}
