<?php
/**
 * @author    Ethnic
 * @copyright Copyright (c) Ethnic
 * @package   Ethnic_RollOverImage
 */

declare(strict_types=1);

namespace Ethnic\RollOverImage\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * Helper for RollOver Image configurations
 */
class Data extends AbstractHelper
{
    private const XML_PATH_ENABLE = 'ethnic_rollover/general/enable';
    private const XML_PATH_ROLE = 'ethnic_rollover/general/image_role';
    private const XML_PATH_SECTIONS = 'ethnic_rollover/general/sections';
    private const XML_PATH_ANIMATION = 'ethnic_rollover/general/animation';
    private const XML_PATH_ANIMATION_DURATION = 'ethnic_rollover/general/animation_duration';
    private const XML_PATH_LAZY_LOAD = 'ethnic_rollover/general/lazy_load';
    private const XML_PATH_ROLE_POSITION = 'ethnic_rollover/general/role_position';

    /**
     * Check if module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get image role for rollover
     *
     * @return string
     */
    public function getImageRole(): string
    {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_ROLE, ScopeInterface::SCOPE_STORE);
    }

     /**
     * Get image position  for rollover
     *
     * @return string
     */
    public function getImageRolePosition(): string
    {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_ROLE_POSITION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get enabled sections
     *
     * @return array
     */
    public function getEnabledSections(): array
    {
        $sections = $this->scopeConfig->getValue(self::XML_PATH_SECTIONS, ScopeInterface::SCOPE_STORE);
        if ($sections) {
            return explode(',', $sections);
        }
        return [];
    }

    /**
     * Check if fade animation is enabled
     *
     * @return bool
     */
    public function isAnimationEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ANIMATION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get animation duration
     *
     * @return string
     */
    public function getAnimationDuration(): string
    {
        $duration = $this->scopeConfig->getValue(
            self::XML_PATH_ANIMATION_DURATION,
            ScopeInterface::SCOPE_STORE
        );
        return (string) $duration ?: '0.5';
    }

    /**
     * Check if lazy load is enabled
     *
     * @return bool
     */
    public function isLazyLoadEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_LAZY_LOAD, ScopeInterface::SCOPE_STORE);
    }
}
