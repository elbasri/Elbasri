<?php
/**
 * Elbasri Media Manager
 */
namespace Elbasri\MediaManager\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Data
 * @author Andre Elbasri <andre@Elbasri.com.br>
 * @since 0.1.0
 * @package Elbasri\MediaManager
 * @license GNU General Public License, version 3
 */
class Data extends AbstractHelper
{
    /**
     * getConfigData
     * @param string $path The configuration path, like "enabled".
     * @param string $area The configuration area, like "general".
     * @param string $scope The store scope
     * @return mixed
     */
    public function getConfigData($path, $area = 'general', $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        // gets the config value and returns it
        return $this->scopeConfig->getValue(
            "Elbasri_media_manager/{$area}/{$path}",
            $scope
        );
    }
}
