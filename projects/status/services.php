<?php
/**
 * Services shown on /projects/status/, grouped by project.
 *
 * Types:
 *   page      the browser loads the page itself (same origin) and times it
 *   http      the server requests an external URL (no CORS issues)
 *   notefox   the Notefox health endpoint; 'part' = sync (API + database + schema) or mail
 *   app-db    database of an app with its own credentials.php: connection + schema version
 *             ('dir' = folder under /projects/, may be nested like dad/rendicontazione; 'prefix' = default table prefix)
 *   fischietto-db  same, Fischietto keeps its settings in lib/config.php
 *   game-db   shared database of the online games ('dir' with api/db.php, 'table' to read)
 *   mail      SMTP server answers with its 220 greeting; 'source' = site | fischietto | app:<dir>
 *
 * Only names and public URLs live here: the page prints this list as it is.
 */

return [
    [
        'name' => 'saveriomorelli.com',
        'icon' => '/images/icon-primary.svg',
        'url' => '/',
        'services' => [
            ['id' => 'site-web', 'name' => 'Website', 'type' => 'page', 'url' => '/'],
            ['id' => 'site-mail', 'name' => 'Contact form emails', 'type' => 'mail', 'source' => 'site'],
        ],
    ],
    [
        'name' => 'Notefox',
        'icon' => '/images/projects/websites-notes.png',
        'url' => '/projects/notefox/',
        'more' => ['label' => 'Detailed status', 'url' => 'https://www.notefox.eu/help/status/'],
        'services' => [
            ['id' => 'notefox-web', 'name' => 'Website', 'type' => 'http', 'url' => 'https://www.notefox.eu/'],
            ['id' => 'notefox-sync', 'name' => 'Sync server', 'type' => 'notefox', 'part' => 'sync', 'url' => 'https://www.notefox.eu/api/v2/status/'],
            ['id' => 'notefox-mail', 'name' => 'Account emails', 'type' => 'notefox', 'part' => 'mail', 'url' => 'https://www.notefox.eu/api/v2/status/'],
        ],
    ],
    [
        'name' => 'savmrl.it',
        'icon' => '/images/projects/savmrl.png',
        'url' => '/projects/savmrl/',
        'services' => [
            ['id' => 'savmrl-web', 'name' => 'Link shortener', 'type' => 'http', 'url' => 'https://www.savmrl.it/'],
        ],
    ],
    [
        'name' => 'Fischietto',
        'icon' => '/images/projects/ripasso-fipav.png',
        'url' => '/projects/ripasso-fipav/',
        'services' => [
            ['id' => 'fischietto-web', 'name' => 'Web app', 'type' => 'page', 'url' => '/projects/ripasso-fipav/'],
            ['id' => 'fischietto-db', 'name' => 'Accounts & quizzes', 'type' => 'fischietto-db'],
            ['id' => 'fischietto-mail', 'name' => 'Emails', 'type' => 'mail', 'source' => 'fischietto'],
        ],
    ],
    [
        'name' => 'Illerom',
        'icon' => '/images/projects/illerom.png',
        'url' => 'https://www.illerom.eu/',
        'more' => ['label' => 'Detailed status', 'url' => 'https://www.illerom.eu/en/status/'],
        'services' => [
            ['id' => 'illerom-web', 'name' => 'Portal, games and apps', 'type' => 'http', 'url' => 'https://www.illerom.eu/en/'],
        ],
    ],
    [
        'name' => 'SavComponents',
        'icon' => 'https://savcomponents.com/docs/logo.svg',
        'url' => 'https://savcomponents.com/',
        'services' => [
            ['id' => 'savcomponents-web', 'name' => 'Website & library', 'type' => 'http', 'url' => 'https://savcomponents.com/'],
            ['id' => 'savcomponents-api', 'name' => 'Accounts API', 'type' => 'page', 'url' => '/projects/savcomponents/api/?r=auth.config'],
            ['id' => 'savcomponents-db', 'name' => 'Accounts, favourites & themes', 'type' => 'app-db', 'dir' => 'savcomponents', 'prefix' => 'sc_'],
            ['id' => 'savcomponents-mail', 'name' => 'Account emails', 'type' => 'mail', 'source' => 'app:savcomponents'],
        ],
    ],
    [
        'name' => 'SavAccount',
        'icon' => '/images/projects/savaccount.png',
        'url' => 'https://www.savaccount.eu/',
        'services' => [
            ['id' => 'savaccount-web', 'name' => 'Website', 'type' => 'http', 'url' => 'https://www.savaccount.eu/'],
            ['id' => 'savaccount-oidc', 'name' => 'Sign-in for services (OpenID Connect)', 'type' => 'http', 'url' => 'https://www.savaccount.eu/.well-known/openid-configuration'],
            ['id' => 'savaccount-api', 'name' => 'Accounts API & database', 'type' => 'http', 'url' => 'https://www.savaccount.eu/api/health'],
            ['id' => 'savaccount-mail', 'name' => 'Codes & security emails', 'type' => 'http', 'url' => 'https://www.savaccount.eu/api/health.mail'],
        ],
    ],
    [
        'name' => 'Rendicontazione Ore',
        'icon' => '/projects/dad/rendicontazione/assets/icons/icon.svg',
        'url' => '/projects/dad/rendicontazione/',
        'services' => [
            ['id' => 'rendicontazione-web', 'name' => 'Web app', 'type' => 'page', 'url' => '/projects/dad/rendicontazione/'],
            ['id' => 'rendicontazione-db', 'name' => 'Accounts & hours', 'type' => 'app-db', 'dir' => 'dad/rendicontazione', 'prefix' => 'dad_rend_'],
        ],
    ],
];
