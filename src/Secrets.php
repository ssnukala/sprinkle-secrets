<?php

/*
 * UserFrosting Content Sprinkle (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/sprinkle-secrets
 * @copyright Copyright (c) 2022 Srinivas Nukala
 * @license   https://github.com/userfrosting/sprinkle-admin/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\Secrets;

use UserFrosting\Event\EventListenerRecipe;
use UserFrosting\Sprinkle\Account\Account;
use UserFrosting\Sprinkle\Admin\Admin;
use UserFrosting\Sprinkle\Core\Core;
use UserFrosting\Sprinkle\Account\Event\UserRedirectedAfterLoginEvent;
use UserFrosting\Sprinkle\Admin\Listener\UserRedirectedToDashboard;
use UserFrosting\Sprinkle\SprinkleRecipe;
use UserFrosting\Sprinkle\CRUD5\CRUD5;

use UserFrosting\Theme\AdminLTE\AdminLTE;

class Secrets implements
    SprinkleRecipe,
    EventListenerRecipe
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Secrets Sprinkle';
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): string
    {
        return __DIR__ . '/../';
    }

    /**
     * {@inheritdoc}
     */
    public function getSprinkles(): array
    {
        return [
            Core::class,
            Admin::class,
            AdminLTE::class,
            CRUD5::class
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getRoutes(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getServices(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     * N.B.: Last listeners will be executed first.
     */
    public function getEventListeners(): array
    {
        return [
            UserRedirectedAfterLoginEvent::class => [
                UserRedirectedToDashboard::class,
            ],
        ];
    }
}
