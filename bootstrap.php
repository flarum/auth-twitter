<?php

/*
 * This file is part of Flarum.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Flarum\Auth\Twitter\TwitterAuthController;
use Flarum\Extend;

return [
    (new Extend\Assets('forum'))
        ->defaultAssets(__DIR__)
        ->bootstrapper('flarum/auth/twitter/main'),
    (new Extend\Assets('admin'))
        ->asset(__DIR__.'/js/admin/dist/extension.js')
        ->bootstrapper('flarum/auth/twitter/main'),
    new Extend\Route(
        'forum', 'auth.twitter',
        'get', '/auth/twitter', TwitterAuthController::class
    )
];
