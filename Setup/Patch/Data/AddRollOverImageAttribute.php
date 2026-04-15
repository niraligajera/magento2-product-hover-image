<?php
/**
 * @author    Ethnic
 * @copyright Copyright (c) Ethnic
 * @package   Ethnic_RollOverImage
 */

declare(strict_types=1);

namespace Ethnic\RollOverImage\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
 * Class AddRollOverImageAttribute
 * Adds a new media image attribute role for hover images
 */
class AddRollOverImageAttribute implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private EavSetupFactory $eavSetupFactory;

    /**
     * AddRollOverImageAttribute constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory          $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        /**
         * @var EavSetup $eavSetup
        */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        if (!$eavSetup->getAttributeId(Product::ENTITY, 'ethnic_rollover_image')) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'ethnic_rollover_image',
                [
                    'type' => 'varchar',
                    'label' => 'Hover / RollOver Image',
                    'input' => 'media_image',
                    'frontend' => \Magento\Catalog\Model\Product\Attribute\Frontend\Image::class,
                    'required' => false,
                    'sort_order' => 50,
                    'global' => ScopedAttributeInterface::SCOPE_STORE,
                    'used_in_product_listing' => true,
                    'user_defined' => true,
                    'visible' => true,
                    'visible_on_front' => false
                ]
            );
        }

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
