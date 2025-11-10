<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/helpers.php';

load_env(base_path('.env'));

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/services/database_status.php';
require_once __DIR__ . '/services/user_stats.php';


