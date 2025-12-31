<?php

namespace app\helpers;

use Yii;
use yii\helpers\Url;

/**
 * MenuHelper contains menu-related utility functions
 */
class MenuHelper
{
    /**
     * Check if current route is active
     * 
     * @param string|array $route Route to check (e.g., 'site/index' or ['site/index'])
     * @param bool $exactMatch Whether to match exactly or check if route starts with given route
     * @return bool
     */
    public static function isActive($route, $exactMatch = false)
    {
        $currentRoute = Yii::$app->controller->getRoute();
        
        if (is_array($route)) {
            $route = $route[0];
        }
        
        // Remove leading slash
        $route = ltrim($route, '/');
        $currentRoute = ltrim($currentRoute, '/');
        
        if ($exactMatch) {
            return $currentRoute === $route;
        }
        
        return strpos($currentRoute, $route) === 0;
    }

    /**
     * Get active CSS class if route is active
     * 
     * @param string|array $route Route to check
     * @param string $activeClass CSS class to return if active (default: 'active')
     * @param bool $exactMatch Whether to match exactly
     * @return string Active class or empty string
     */
    public static function activeClass($route, $activeClass = 'active', $exactMatch = false)
    {
        return self::isActive($route, $exactMatch) ? $activeClass : '';
    }

    /**
     * Check if menu should be shown (collapsed/expanded)
     * 
     * @param array $routes Array of routes to check
     * @return bool
     */
    public static function isMenuOpen($routes)
    {
        if (!is_array($routes)) {
            $routes = [$routes];
        }
        
        foreach ($routes as $route) {
            if (self::isActive($route)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get show/collapse class for menu
     * 
     * @param array $routes Array of routes to check
     * @param string $showClass CSS class when menu is open (default: 'show')
     * @return string Show class or empty string
     */
    public static function menuShowClass($routes, $showClass = 'show')
    {
        return self::isMenuOpen($routes) ? $showClass : '';
    }

    /**
     * Generate menu item HTML
     * 
     * @param array $item Menu item configuration
     * @return string HTML string
     */
    public static function renderMenuItem($item)
    {
        $label = $item['label'] ?? '';
        $url = $item['url'] ?? '#';
        $icon = $item['icon'] ?? '';
        $badge = $item['badge'] ?? null;
        $active = self::isActive($url);
        
        $html = '<a href="' . Url::to($url) . '" class="' . ($active ? 'active' : '') . '">';
        
        if ($icon) {
            $html .= '<i class="' . $icon . '"></i> ';
        }
        
        $html .= '<span>' . $label . '</span>';
        
        if ($badge) {
            $badgeClass = $badge['class'] ?? 'badge-primary';
            $badgeText = $badge['text'] ?? '';
            $html .= ' <span class="badge ' . $badgeClass . '">' . $badgeText . '</span>';
        }
        
        $html .= '</a>';
        
        return $html;
    }

    /**
     * Get breadcrumb items based on current route
     * 
     * @param array $customItems Custom breadcrumb items to append
     * @return array Breadcrumb items
     */
    public static function getBreadcrumbs($customItems = [])
    {
        $items = [
            ['label' => 'Home', 'url' => ['site/index']],
        ];
        
        return array_merge($items, $customItems);
    }

    /**
     * Check if user has access to menu item
     * 
     * @param array $item Menu item configuration
     * @return bool
     */
    public static function hasAccess($item)
    {
        // Check if visible key exists and is false
        if (isset($item['visible']) && !$item['visible']) {
            return false;
        }
        
        // Check if permission key exists
        if (isset($item['permission'])) {
            return MyHelper::can($item['permission']);
        }
        
        // Check if roles key exists
        if (isset($item['roles'])) {
            if (Yii::$app->user->isGuest) {
                return in_array('?', $item['roles']);
            }
            return in_array('@', $item['roles']) || Yii::$app->user->can($item['roles']);
        }
        
        return true;
    }

    /**
     * Filter menu items based on user access
     * 
     * @param array $items Menu items array
     * @return array Filtered menu items
     */
    public static function filterMenuItems($items)
    {
        $filtered = [];
        
        foreach ($items as $item) {
            if (self::hasAccess($item)) {
                if (isset($item['items']) && is_array($item['items'])) {
                    $item['items'] = self::filterMenuItems($item['items']);
                }
                $filtered[] = $item;
            }
        }
        
        return $filtered;
    }

    /**
     * Get current controller ID
     * 
     * @return string
     */
    public static function getControllerId()
    {
        return Yii::$app->controller->id;
    }

    /**
     * Get current action ID
     * 
     * @return string
     */
    public static function getActionId()
    {
        return Yii::$app->controller->action->id;
    }

    /**
     * Check if current controller matches
     * 
     * @param string|array $controllerId Controller ID(s) to check
     * @return bool
     */
    public static function isController($controllerId)
    {
        $current = self::getControllerId();
        
        if (is_array($controllerId)) {
            return in_array($current, $controllerId);
        }
        
        return $current === $controllerId;
    }

    /**
     * Check if current action matches
     * 
     * @param string|array $actionId Action ID(s) to check
     * @return bool
     */
    public static function isAction($actionId)
    {
        $current = self::getActionId();
        
        if (is_array($actionId)) {
            return in_array($current, $actionId);
        }
        
        return $current === $actionId;
    }
}
