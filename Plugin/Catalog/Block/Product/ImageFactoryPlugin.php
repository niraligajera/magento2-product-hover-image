<?php
/**
 * @author    Ethnic
 * @copyright Copyright (c) Ethnic
 * @package   Ethnic_RollOverImage
 */

declare(strict_types=1);

namespace Ethnic\RollOverImage\Plugin\Catalog\Block\Product;

use Magento\Catalog\Block\Product\Image;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Gallery\ReadHandler;
use Ethnic\RollOverImage\Helper\Data;

/**
 * Class ImageFactoryPlugin
 * Intercepts ImageFactory block creation to add custom attributes for KO JS binding
 */
class ImageFactoryPlugin
{
    /**
     * @var Data
     */
    private Data $helper;

    /**
     * @var ImageHelper
     */
    private ImageHelper $imageHelper;

    /**
     * @var ReadHandler
     */
    private ReadHandler $galleryReadHandler;

    /**
     * ImageFactoryPlugin constructor.
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
     * Inject rollover configuration data to Product Image block
     *
     * @param  ImageFactory $subject
     * @param  Image        $result
     * @param  Product      $product
     * @param  string       $imageId
     * @param  array|null   $attributes
     * @return Image
     */
    public function afterCreate(
        ImageFactory $subject,
        Image $result,
        $product,
        string $imageId,
        ?array $attributes = null
    ): Image {
        

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/afterCreate.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info("\n");
        $logger->info($product->getName() . " -started image process");

        if (!$this->helper->isEnabled() || !$product || !$imageId) {
            return $result;
        }

        $enabledSections = $this->helper->getEnabledSections();
        
        if (!in_array($imageId, $enabledSections, true)) {
            return $result;
        }

        $role = $this->helper->getImageRole();
        $rolePosition = $this->helper->getImageRolePosition();        
        
        $imageFile = null;
        $imageFile = $product->getData($role);
        
        if ($role === '_gallery_fallback_' || (int)$rolePosition == 0) {
            if (!$product->hasData('media_gallery_images')) {
                $this->galleryReadHandler->execute($product);
            }
            $images = $product->getMediaGalleryImages();
            
            if($images && $imageFile === $images->getFirstItem()->getFile()) {               
                return $result;             
            } 

            if ($images && $images->getSize() > 1 && empty($imageFile) ) {
                $count = 0;              
                foreach ($images as $image) {
                    if ($image->getDisabled()) {
                        continue;
                    }
                   
                    if ($count === (int)$rolePosition) {                   
                        $imageFile = $image->getFile();
                        $logger->info("Selected role position image file: " . $imageFile);
                        break;
                    }
                    $count++;
                }
            }
        }     
        
        if (!$imageFile || $imageFile === 'no_selection') {         
            return $result;
        }

        $hoverImageUrl = $this->imageHelper->init($product, $imageId)
            ->setImageFile($imageFile)
            ->getUrl();
        $logger->info("Hover image URL: " . $hoverImageUrl);
        $logger->info("\n");
        $customAttributes = $result->getCustomAttributes() ?: [];
        $customAttributes['data-mage-init'] = json_encode(
            [
                'Ethnic_RollOverImage/js/rollover' => [
                    'hoverImage' => $hoverImageUrl,
                    'animation' => $this->helper->isAnimationEnabled(),
                    'animationDuration' => $this->helper->getAnimationDuration(),
                    'lazyLoad' => $this->helper->isLazyLoadEnabled()
                ]
            ]
        );
        
        $result->setCustomAttributes($customAttributes);

        $class = $result->getClass() ?: 'product-image-photo';
        $class .= ' rollover-image-initialized';
        $result->setClass($class);       
        return $result;
    }
}
