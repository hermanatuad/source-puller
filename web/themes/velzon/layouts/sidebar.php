<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="/" class="logo logo-dark">
            <span class="logo-sm">
                <img src="/images/logo-sm.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="/images/logo-dark.png" alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="/" class="logo logo-light">
            <span class="logo-sm">
                <img src="/images/logo-sm.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="/images/logo-light.png" alt="" height="17">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <?php
                use app\helpers\MenuHelper;
                use yii\helpers\Url;

                $menu = MenuHelper::getMenuItems();
                $currentRoute = Yii::$app->controller->route;

                // Function to check if current route matches menu item
                function isActiveRoute($url, $currentRoute) {
                    if (is_array($url)) {
                        $route = isset($url[0]) ? trim($url[0], '/') : '';
                        return strpos($currentRoute, $route) === 0;
                    }
                    return false;
                }

                // Function to check if menu has active child
                function hasActiveChild($items, $currentRoute) {
                    foreach ($items as $item) {
                        if (isset($item['url']) && isActiveRoute($item['url'], $currentRoute)) {
                            return true;
                        }
                        if (!empty($item['items']) && hasActiveChild($item['items'], $currentRoute)) {
                            return true;
                        }
                    }
                    return false;
                }

                function renderMenu($items, $currentRoute) {
                    foreach ($items as $item) {
                        // Check visibility
                        if (isset($item['visible']) && !$item['visible']) {
                            continue;
                        }

                        if (isset($item['type']) && $item['type'] === 'title') {
                            echo '<li class="menu-title"><span>' . $item['label'] . '</span></li>';
                            continue;
                        }

                        $hasChildren = !empty($item['items']);
                        
                        if ($hasChildren) {
                            $id = 'sidebar' . substr(md5(strip_tags($item['label'])), 0, 8);
                            $isActive = hasActiveChild($item['items'], $currentRoute);
                            $collapseClass = $isActive ? 'collapse menu-dropdown show' : 'collapse menu-dropdown';
                            $ariaExpanded = $isActive ? 'true' : 'false';
                            
                            echo '<li class="nav-item">';
                            echo '<a class="nav-link menu-link' . ($isActive ? ' active' : '') . '" href="#' . $id . '" data-bs-toggle="collapse" role="button" aria-expanded="' . $ariaExpanded . '" aria-controls="' . $id . '">' . $item['label'] . '</a>';
                            echo '<div class="' . $collapseClass . '" id="' . $id . '"><ul class="nav nav-sm flex-column">';
                            renderMenu($item['items'], $currentRoute);
                            echo '</ul></div></li>';
                        } else {
                            $url = isset($item['url']) ? (is_array($item['url']) ? Url::to($item['url']) : $item['url']) : '#';
                            $target = isset($item['target']) ? ' target="' . $item['target'] . '"' : '';
                            $isActive = isset($item['url']) && isActiveRoute($item['url'], $currentRoute);
                            $activeClass = $isActive ? ' active' : '';
                            echo '<li class="nav-item"><a class="nav-link' . $activeClass . '" href="' . $url . '"' . $target . '>' . $item['label'] . '</a></li>';
                        }
                    }
                }

                renderMenu($menu, $currentRoute);
                ?>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>