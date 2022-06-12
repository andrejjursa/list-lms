<?php

$config['redis']['lock'] = [
    'host' => getenv('LIST_REDIS_LOCK_HOST'),
    'port' => getenv('LIST_REDIS_LOCK_PORT'),
    'scheme' => getenv('LIST_REDIS_LOCK_SCHEME'),
];