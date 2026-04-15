<?php
/**
 * @author Ethnic
 * @copyright Copyright (c) Ethnic
 * @package Ethnic_RollOverImage
 */

declare(strict_types=1);

namespace Ethnic\RollOverImage\Plugin\Hyva;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Gallery\ReadHandler;
use Ethnic\RollOverImage\Helper\Data;

/**
 * Class ProductListItemPlugin
 * Intercepts ProductListItem to inject AlpineJS properties for Luma's RollOverImage logic in Hyvä grids
 */
class ProductListItemPlugin
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
     * ProductListItemPlugin constructor.
     *
     * @param Data $helper
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
     * Inject Hover code
     *
     * @param \Hyva\Theme\ViewModel\ProductListItem $subject
     * @param string $html
     * @param AbstractBlock $itemRendererBlock
     * @param Product $product
     * @param AbstractBlock $parentBlock
     * @param string $viewMode
     * @param string $templateType
     * @param string $imageDisplayArea
     * @param bool $showDescription
     * @return string
     */
    public function afterGetItemHtmlWithRenderer(
        \Hyva\Theme\ViewModel\ProductListItem $subject,
        string $html,
        AbstractBlock $itemRendererBlock,
        Product $product,
        AbstractBlock $parentBlock,
        string $viewMode,
        string $templateType,
        string $imageDisplayArea,
        bool $showDescription
    ): string {

        if (!$this->helper->isEnabled()) {
            return $html;
        }

        $enabledSections = $this->helper->getEnabledSections();
        if (!in_array($imageDisplayArea, $enabledSections, true)) {
            return $html;
        }

        $role = $this->helper->getImageRole();
        $imageFile = null;

        if ($role === '_gallery_fallback_') {
            if (!$product->hasData('media_gallery_images')) {
                // Utilizing ReadHandler prevents loading the whole product via Repo (Performance fix)
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
            return $html;
        }

        $hoverImageUrl = $this->imageHelper->init($product, $imageDisplayArea)
            ->setImageFile($imageFile)
            ->getUrl();

        if (!$hoverImageUrl) {
            return $html;
        }

        $isAnimation = $this->helper->isAnimationEnabled();
        $durationMs = $isAnimation ? ((float) $this->helper->getAnimationDuration() * 1000) : 0;

        $alpineProps = ' x-data="{ hoverImg: \'' . $hoverImageUrl . '\' }"';

        if ($isAnimation) {
            $alpineProps .= ' x-on:mouseenter="if (!(\'ontouchstart\' in window)) { ' .
                '$el.parentElement.style.position = \'relative\'; ' .
                'let overlay = $el.parentElement.querySelector(\'.hover-overlay\'); ' .
                'if (!overlay) { ' .
                '   overlay = document.createElement(\'img\'); ' .
                '   overlay.src = hoverImg; ' .
                '   overlay.className = $el.className + \' hover-overlay\'; ' .
                '   overlay.style.position = \'absolute\'; ' .
                '   overlay.style.top = \'0\'; ' .
                '   overlay.style.left = \'0\'; ' .
                '   overlay.style.width = \'100%\'; ' .
                '   overlay.style.height = \'100%\'; ' .
                '   overlay.style.objectFit = \'contain\'; ' .
                '   overlay.style.transition = \'opacity ' . $durationMs . 'ms ease-in-out\'; ' .
                '   overlay.style.opacity = \'0\'; ' .
                '   overlay.style.pointerEvents = \'none\'; ' .
                '   $el.parentElement.appendChild(overlay); ' .
                '} ' .
                'setTimeout(() => { overlay.style.opacity = \'1\'; }, 10); ' .
                '}"';
            $alpineProps .= ' x-on:mouseleave="if (!(\'ontouchstart\' in window)) { ' .
                'let overlay = $el.parentElement.querySelector(\'.hover-overlay\'); ' .
                'if (overlay) overlay.style.opacity = \'0\'; ' .
                '}"';
        } else {
            $alpineProps .= ' x-on:mouseenter="if (!(\'ontouchstart\' in window)) { oldImg=$el.src; $el.src=hoverImg; }"';
            $alpineProps .= ' x-on:mouseleave="if (!(\'ontouchstart\' in window) && typeof oldImg !== \'undefined\') { $el.src=oldImg; }"';
        }

        if ($this->helper->isLazyLoadEnabled()) {
            $alpineProps .= ' x-intersect.once="(new Image()).src=\'' . $hoverImageUrl . '\'"';
        }

        // Inject Hover into HTML
        $html = preg_replace(
            '/<img/i',
            '<img' . $alpineProps,
            $html,
            1
        );

        return $html;
    }
}
