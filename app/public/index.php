<?php

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';


return function (array $context) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
