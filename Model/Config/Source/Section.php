<?php
/**
 * @author    Ethnic
 * @copyright Copyright (c) Ethnic
 * @package   Ethnic_RollOverImage
 */

declare(strict_types=1);

namespace Ethnic\RollOverImage\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\ConfigInterface;

/**
 * Class Section
 * Source model for active sections
 */
class Section implements OptionSourceInterface
{
    /**
     * @var ConfigInterface
     */
    private ConfigInterface $viewConfig;

    /**
     * Section constructor.
     *
     * @param ConfigInterface $viewConfig
     */
    public function __construct(ConfigInterface $viewConfig)
    {
        $this->viewConfig = $viewConfig;
    }

    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];
        try {
            $config = $this->viewConfig->getViewConfig(['area' => 'frontend']);
            $mediaEntities = $config->getMediaEntities('Magento_Catalog', 'images');
            
            foreach ($mediaEntities as $id => $data) {
                $label = ucwords(str_replace('_', ' ', $id));
                $options[] = ['value' => $id, 'label' => $label . ' (' . $id . ')'];
            }
        } catch (\Exception $e) {
            $options = [
                ['value' => 'category_page_grid', 'label' => __('Category Page Grid (category_page_grid)')],
                ['value' => 'category_page_list', 'label' => __('Category Page List (category_page_list)')],
                ['value' => 'product_small_image', 'label' => __('Product Small Image (product_small_image)')],
                ['value' => 'related_products_list', 'label' => __('Related Products (related_products_list)')],
                ['value' => 'upsell_products_list', 'label' => __('Upsell Products (upsell_products_list)')],
                ['value' => 'crosssell_products_list', 'label' => __('Crosssell Products (crosssell_products_list)')],
            ];
        }

        usort(
            $options,
            function ($a, $b) {
                return strcmp((string) $a['label'], (string) $b['label']);
            }
        );

        return $options;
    }
}
