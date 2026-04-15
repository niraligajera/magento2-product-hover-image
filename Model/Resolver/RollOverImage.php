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
use Magento\Catalog\Model\Product;
use Ethnic\RollOverImage\Helper\Data;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Gallery\ReadHandler;

class RollOverImage implements ResolverInterface
{
    /** @var Data */
    private Data $helper;
    
    /** @var ImageHelper */
    private ImageHelper $imageHelper;
    
    /** @var ReadHandler */
    private ReadHandler $galleryReadHandler;

    /**
     * RollOverImage constructor.
     *
     * @param Data        $helper
     * @param ImageHelper $imageHelper
     * @param ReadHandler $galleryReadHandler
     */
    public function __construct(
        Data $helper,
        ImageHelper $imageHelper,
        ReadHandler $galleryReadHandler
    ) {
        $this->helper = $helper;
        $this->imageHelper = $imageHelper;
        $this->galleryReadHandler = $galleryReadHandler;
    }

    /**
     * Process dynamic fallback resolutions specifically for the current GraphQl model payload
     *
     * @param Field       $field
     * @param mixed       $context
     * @param ResolveInfo $info
     * @param array|null  $value
     * @param array|null  $args
     * @return string|null
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        if (!$this->helper->isEnabled()) {
            return null;
        }

        if (!isset($value['model'])) {
            return null;
        }

        /** @var Product $product */
        $product = $value['model'];
        
        $role = $this->helper->getImageRole();
        $imageFile = null;

        if ($role === '_gallery_fallback_') {
            if (!$product->hasData('media_gallery_images')) {
                $this->galleryReadHandler->execute($product);
            }
            $images = $product->getMediaGalleryImages();
            if ($images && $images->getSize() > 1) {
                $count = 0;
                foreach ($images as $image) {
                    if ($image->getDisabled()) {
                        continue;
                    }
                    if ($count === 1) {
                        $imageFile = $image->getFile();
                        break;
                    }
                    $count++;
                }
            }
        } else {
            $imageFile = $product->getData($role);
        }

        if (!$imageFile || $imageFile === 'no_selection') {
            return null;
        }

        $imageId = $args['image_id'] ?? 'category_page_grid';

        return $this->imageHelper->init($product, $imageId)
            ->setImageFile($imageFile)
            ->getUrl();
    }
}
