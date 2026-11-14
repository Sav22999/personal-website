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
        'name' => 'FoodTrack',
        'icon' => '/images/projects/foodtrack.png',
        'url' => '/projects/foodtrack/',
        'services' => [
            ['id' => 'foodtrack-web', 'name' => 'Web app', 'type' => 'page', 'url' => '/projects/foodtrack/'],
            ['id' => 'foodtrack-db', 'name' => 'Accounts & pantry data', 'type' => 'app-db', 'dir' => 'foodtrack', 'prefix' => 'ft_'],
            ['id' => 'foodtrack-mail', 'name' => 'Emails', 'type' => 'mail', 'source' => 'app:foodtrack'],
        ],
    ],
    [
        'name' => 'Giftwise',
        'icon' => '/images/projects/giftwise.png',
        'url' => '/projects/giftwise/',
        'services' => [
            ['id' => 'giftwise-web', 'name' => 'Web app', 'type' => 'page', 'url' => '/projects/giftwise/'],
            ['id' => 'giftwise-db', 'name' => 'Accounts & wish lists', 'type' => 'app-db', 'dir' => 'giftwise', 'prefix' => 'gw_'],
            ['id' => 'giftwise-mail', 'name' => 'Emails', 'type' => 'mail', 'source' => 'app:giftwise'],
        ],
    ],
    [
        'name' => 'Fucina',
        'icon' => '/images/projects/fucina.png',
        'url' => '/projects/fucina/',
        'services' => [
            ['id' => 'fucina-web', 'name' => 'Web app', 'type' => 'page', 'url' => '/projects/fucina/'],
            ['id' => 'fucina-db', 'name' => 'Accounts & progress', 'type' => 'app-db', 'dir' => 'fucina', 'prefix' => 'fc_'],
            ['id' => 'fucina-mail', 'name' => 'Emails', 'type' => 'mail', 'source' => 'app:fucina'],
        ],
    ],
    [
        'name' => 'FaceGuess',
        'icon' => '/images/projects/faceguess.png',
        'url' => '/projects/faceguess/',
        'services' => [
            ['id' => 'faceguess-web', 'name' => 'Game', 'type' => 'page', 'url' => '/projects/faceguess/'],
            ['id' => 'faceguess-db', 'name' => 'Online multiplayer', 'type' => 'game-db', 'dir' => 'faceguess', 'table' => 'fg_rooms'],
        ],
    ],
    [
        'name' => 'Chess',
        'icon' => '/images/projects/chess.png',
        'url' => '/projects/chess/',
        'services' => [
            ['id' => 'chess-web', 'name' => 'Game', 'type' => 'page', 'url' => '/projects/chess/'],
            ['id' => 'chess-db', 'name' => 'Online multiplayer', 'type' => 'game-db', 'dir' => 'chess', 'table' => 'chess_rooms'],
        ],
    ],
    [
        'name' => 'Sudoku',
        'icon' => '/images/projects/sudoku.png',
        'url' => '/projects/sudoku/',
        'services' => [
            ['id' => 'sudoku-web', 'name' => 'Game', 'type' => 'page', 'url' => '/projects/sudoku/'],
        ],
    ],
    [
        'name' => 'Prism Mine',
        'icon' => '/images/projects/prism-mine.png',
        'url' => '/projects/prism-mine/',
        'services' => [
            ['id' => 'prism-mine-web', 'name' => 'Game', 'type' => 'page', 'url' => '/projects/prism-mine/'],
            ['id' => 'prism-mine-db', 'name' => 'Online multiplayer', 'type' => 'game-db', 'dir' => 'prism-mine', 'table' => 'pm_rooms'],
        ],
    ],
    [
        'name' => 'Sea Battle',
        'icon' => '/images/projects/sea-battle.png',
        'url' => '/projects/sea-battle/',
        'services' => [
            ['id' => 'sea-battle-web', 'name' => 'Game', 'type' => 'page', 'url' => '/projects/sea-battle/'],
            ['id' => 'sea-battle-db', 'name' => 'Online multiplayer', 'type' => 'game-db', 'dir' => 'sea-battle', 'table' => 'sb_rooms'],
        ],
    ],
    [
        'name' => 'Connect Four',
        'icon' => '/images/projects/connect-four.png',
        'url' => '/projects/connect-four/',
        'services' => [
            ['id' => 'connect-four-web', 'name' => 'Game', 'type' => 'page', 'url' => '/projects/connect-four/'],
            ['id' => 'connect-four-db', 'name' => 'Online multiplayer', 'type' => 'game-db', 'dir' => 'connect-four', 'table' => 'c4_rooms'],
        ],
    ],
    [
        'name' => 'LetterHop',
        'icon' => '/images/projects/letterhop.png',
        'url' => '/projects/letterhop/',
        'services' => [
            ['id' => 'letterhop-web', 'name' => 'Game', 'type' => 'page', 'url' => '/projects/letterhop/'],
        ],
    ],
    [
        'name' => 'ThinkHop',
        'icon' => '/images/projects/thinkhop.png',
        'url' => '/projects/thinkhop/',
        'services' => [
            ['id' => 'thinkhop-web', 'name' => 'Game', 'type' => 'page', 'url' => '/projects/thinkhop/'],
            ['id' => 'thinkhop-db', 'name' => 'Accounts & rankings', 'type' => 'app-db', 'dir' => 'thinkhop', 'prefix' => 'th_'],
            ['id' => 'thinkhop-mail', 'name' => 'Emails', 'type' => 'mail', 'source' => 'app:thinkhop'],
        ],
    ],
    [
        'name' => 'Forkville',
        'icon' => '/images/projects/forkville.png?v=2',
        'url' => '/projects/forkville/',
        'services' => [
            ['id' => 'forkville-web', 'name' => 'Game', 'type' => 'page', 'url' => '/projects/forkville/'],
            ['id' => 'forkville-db', 'name' => 'Accounts & restaurants', 'type' => 'app-db', 'dir' => 'forkville', 'prefix' => 'fv_'],
            ['id' => 'forkville-mail', 'name' => 'Emails', 'type' => 'mail', 'source' => 'app:forkville'],
        ],
    ],
    [
        'name' => 'MyCurriculum',
        'icon' => '/images/projects/mycurriculum.png',
        'url' => '/projects/mycurriculum/',
        'services' => [
            ['id' => 'mycurriculum-web', 'name' => 'Web app', 'type' => 'page', 'url' => '/projects/mycurriculum/'],
            ['id' => 'mycurriculum-db', 'name' => 'Accounts & CVs', 'type' => 'app-db', 'dir' => 'mycurriculum', 'prefix' => 'mc_'],
            ['id' => 'mycurriculum-mail', 'name' => 'Emails', 'type' => 'mail', 'source' => 'app:mycurriculum'],
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
