<?php

namespace App\Factory;

use LLPhant\OllamaConfig;

class OllamaConfigFactory
{
    private OllamaConfig $ollamaConfig;


    public function __construct(string $ollamaHost, string $ollamaPort, string $ollamaModel)
    {
        $config = new OllamaConfig();
        $config->model = $ollamaModel;
        $config->url = $ollamaHost . ':' . $ollamaPort . "/api/";

        $this->ollamaConfig = $config;
    }

    public function getConfig(): OllamaConfig
    {
        return $this->ollamaConfig;
    }
}
