<?php

namespace app\helpers;

use Yii;
use yii\helpers\Url;

/**
 * Css helper class.
 */
class MenuHelper
{
    public static function getMenuItems()
    {

        $menuItems = [];

        if (!Yii::$app->user->isGuest) {
            // // Top title
            // $menuItems[] = [
            //     'type' => 'title',
            //     'label' => 'Menu'
            // ];

            // // Dashboards
            // $menuItems[] = [
            //     'label' => '<i class="ri-dashboard-2-line"></i> <span>Dashboards</span>',
            //     'items' => [
            //         ['label' => 'Analytics', 'url' => 'dashboard-analytics'],
            //         ['label' => 'CRM', 'url' => 'dashboard-crm'],
            //         ['label' => 'Ecommerce', 'url' => '/index'],
            //         ['label' => 'Crypto', 'url' => 'dashboard-crypto'],
            //         ['label' => 'Projects', 'url' => 'dashboard-projects'],
            //         ['label' => 'NFT', 'url' => 'dashboard-nft'],
            //         ['label' => 'Job', 'url' => 'dashboard-job'],
            //     ]
            // ];

            // Layouts
            // $menuItems[] = [
            //     'label' => '<i class="ri-layout-3-line"></i> <span>Layouts</span> <span class="badge badge-pill bg-danger">Hot</span>',
            //     'items' => [
            //         ['label' => 'Horizontal', 'url' => 'layouts-horizontal', 'target' => '_blank'],
            //         ['label' => 'Detached', 'url' => 'layouts-detached', 'target' => '_blank'],
            //         ['label' => 'Two Column', 'url' => 'layouts-two-column', 'target' => '_blank'],
            //         ['label' => 'Hovered', 'url' => 'layouts-vertical-hovered', 'target' => '_blank'],
            //     ]
            // ];


            $menuItems[] = [
                'type' => 'title',
                'label' => 'Sources'
            ];

            // // Landing
            // $menuItems[] = [
            //     'label' => '<i class="ri-rocket-line"></i> <span>Landing</span>',
            //     'items' => [
            //         ['label' => 'One Page', 'url' => 'landing'],
            //         ['label' => 'NFT Landing', 'url' => 'nft-landing'],
            //         ['label' => 'Job', 'url' => 'job-landing'],
            //     ]
            // ];


            $menuItems[] = [
                'label' => '<i class="ri-contacts-book-line"></i> <span>Hospital</span>',
                'url' => ['site/hospitals']
            ];

            $menuItems[] = [
                'label' => '<i class="ri-users-line"></i> <span>Patients</span>',
                'url' => ['site/patients']
            ];


            // $menuItems[] = [
            //     'type' => 'title',
            //     'label' => 'Administration'
            // ];

            // // RBAC Management
            // $menuItems[] = [
            //     'label' => '<i class="ri-shield-user-line"></i> <span>RBAC Management</span>',
            //     'url' => ['rbac/index'],
            //     'visible' => Yii::$app->user->can('creator')
            // ];


            // $menuItems[] = [
            //     'type' => 'title',
            //     'label' => 'Components'
            // ];

            // Components (Base UI simplified)
            // $menuItems[] = [
            //     'label' => '<i class="ri-pencil-ruler-2-line"></i> <span>Base UI</span>',
            //     'items' => [
            //         ['label' => 'Alerts', 'url' => 'ui-alerts'],
            //         ['label' => 'Badges', 'url' => 'ui-badges'],
            //         ['label' => 'Buttons', 'url' => 'ui-buttons'],
            //         ['label' => 'Cards', 'url' => 'ui-cards'],
            //         ['label' => 'Tabs', 'url' => 'ui-tabs'],
            //         ['label' => 'Modals', 'url' => 'ui-modals'],
            //     ]
            // ];

            // // Multi-level
            // $menuItems[] = [
            //     'label' => '<i class="ri-share-line"></i> <span>Multi Level</span>',
            //     'items' => [
            //         ['label' => 'Level 1.1', 'url' => '#'],
            //         [
            //             'label' => 'Level 1.2',
            //             'items' => [
            //                 ['label' => 'Level 2.1', 'url' => '#'],
            //                 [
            //                     'label' => 'Level 2.2',
            //                     'items' => [
            //                         ['label' => 'Level 3.1', 'url' => '#'],
            //                         ['label' => 'Level 3.2', 'url' => '#'],
            //                     ]
            //                 ],
            //             ]
            //         ],
            //     ]
            // ];


            // $menuItems[] = [
            //     'label' => '<i class="ri-share-line"></i> <span>Master</span>',
            //     'visible' => Yii::$app->user->can('admin'),
            //     'items' => [
			// 		[
			// 			'label' => '<span> Auth Item</span>',
			// 			'url' => ['auth-item/index'],
			// 			'visible' => Yii::$app->user->can('creator')
			// 		],
			// 		[
			// 			'label' => '<span> Auth Item Child</span>',
			// 			'url' => ['auth-item-child/index'],
			// 			'visible' => Yii::$app->user->can('creator')
			// 		],
            //         [
            //             'label' => 'Users',
            //             'url' => ['user/index'],
			// 			'visible' => Yii::$app->user->can('admin')
            //         ]
            //     ]
            // ];
        } else {
            // $menuItems[] = [
            //     'label' => '<i class="ri-contacts-book-line"></i> <span>Login</span>',
            //     'url' => ['site/signin']
            // ];
        }

        return $menuItems;
    }

    public static function getUserMenuItems()
    {
        $menuItems = [];

        $name = !Yii::$app->user->isGuest && isset(Yii::$app->user->identity->nama) ? Yii::$app->user->identity->nama : 'Guest';

        // Header
        $menuItems[] = [
            'type' => 'header',
            'label' => 'Welcome ' . $name . '!'
        ];

        // Standard user links
        $menuItems[] = [
            'label' => '<i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Profile</span>',
            'url' => ['user/profile']
        ];
        // $menuItems[] = [
        //     'label' => '<i class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Messages</span>',
        //     'url' => ['apps-chat']
        // ];
        // $menuItems[] = [
        //     'label' => '<i class="mdi mdi-calendar-check-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Taskboard</span>',
        //     'url' => ['apps-tasks-kanban']
        // ];
        $menuItems[] = [
            'label' => '<i class="mdi mdi-lifebuoy text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Help</span>',
            'url' => ['pages-faqs']
        ];

        // divider
        $menuItems[] = [
            'type' => 'divider'
        ];

        // Balance (static here, could be dynamic)
        // $menuItems[] = [
        //     'label' => '<i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Balance : <b>$5971.67</b></span>',
        //     'url' => ['pages-profile']
        // ];
        // $menuItems[] = [
        //     'label' => '<i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Settings</span>',
        //     'url' => ['pages-profile-settings']
        // ];
        // $menuItems[] = [
        //     'label' => '<i class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Lock screen</span>',
        //     'url' => ['auth-lockscreen-basic']
        // ];
        $menuItems[] = [
            'label' => '<i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Logout</span>',
            'url' => ['site/logout']
        ];

        return $menuItems;
    }
}