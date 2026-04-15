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

/**
 * Class AssignRollOverImageAttributeToSet
 * Assigns the media attribute to all product attribute sets
 */
class AssignRollOverImageAttributeToSet implements DataPatchInterface
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
     * AssignRollOverImageAttributeToSet constructor.
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

        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $attributeSetIds = $eavSetup->getAllAttributeSetIds($entityTypeId);
        $attributeId = $eavSetup->getAttributeId($entityTypeId, 'ethnic_rollover_image');

        if ($attributeId) {
            foreach ($attributeSetIds as $attributeSetId) {
                // Determine the group ID (often 'image-management' for media attributes)
                $groupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'image-management');
                
                // If 'image-management' group doesn't exist in a custom set, fallback to default group
                if (!$groupId) {
                    $groupId = $eavSetup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);
                }

                $eavSetup->addAttributeToSet(
                    $entityTypeId,
                    $attributeSetId,
                    $groupId,
                    $attributeId
                );
            }
        }

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [
            AddRollOverImageAttribute::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
