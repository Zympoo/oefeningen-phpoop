<?php
declare(strict_types=1);

/**
 * Centrale configuratie voor de admin.
 * Hier definiëren we het pad waaronder de admin bereikbaar is.
 *
 * In onze vhost setup:
 * https://minicms.test/admin
 */
define('ADMIN_BASE_PATH', '/admin');

define('GITHUB_CLIENT_ID', 'Ov23liqa1DwJCJg80tMY');
define('GITHUB_CLIENT_SECRET', '89e6d2a7ca344116925e5a3c3187b039b0074058');
define('GITHUB_REDIRECT_URI', 'http://minicms.test/admin/login/github/callback');