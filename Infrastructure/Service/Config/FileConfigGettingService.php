<?php

declare(strict_types=1);

namespace Infrastructure\Service\Config;

class FileConfigGettingService
{
    private const DIRECTORY_FILEPATH_DEPTH = 3;

    private string $rootProjectPath;

    public function __construct()
    {
        $this->rootProjectPath = dirname(__DIR__, self::DIRECTORY_FILEPATH_DEPTH);
    }

    /**
     * @param string $configFilePath
     *
     * @return array
     */
    public function getConfig(string $configFilePath): array
    {
        $projectConfigPath = $this->getProjectConfigPath($configFilePath);
        return file_exists($projectConfigPath) ? include $projectConfigPath : [];
    }

    /**
     * @param $configFilePath
     *
     * @return string
     */
    private function getProjectConfigPath($configFilePath): string
    {
        return $this->rootProjectPath . DIRECTORY_SEPARATOR
            . 'config' . DIRECTORY_SEPARATOR
            . $configFilePath . '.php';
    }
}
