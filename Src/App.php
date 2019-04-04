<?php
declare(strict_types=1);

namespace Src;

class App
{
    public const REQUIRED_CONFIG_ATTRIBUTES = [
        'timezone',
    ];
    private $config;
    /** @var Container */
    private $container;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @throws ServerException
     */
    public function run(): void
    {
        $this->init();
        $request = $this->container->getRequest();
        if(!$request->validateQuery()){
            $this->redirect('server/');
            return;
        }
        $request->getAction()->run();
        $this->container->getDb()->close();
    }

    /**
     * @throws ServerException
     */
    private function validateConfig(): void
    {
        if (!is_array($this->config)) {
            throw new ServerException('Config must be array');
        }
        foreach (self::REQUIRED_CONFIG_ATTRIBUTES as $configAttribute) {
            if (!isset($this->config[$configAttribute])) {
                throw $this->missingConfigAttributeException([$configAttribute]);
            }
        }
        if (!isset($this->getDbConfig()['dsn'])) {
            throw $this->missingConfigAttributeException([
                'services',
                Container::DB,
                'dsn'
            ]);
        }
    }

    private function registerErrorHandler(): void
    {
        set_error_handler(function (int $errorLevel, string $message, string $filename, int $lineno) {
            if (error_reporting() === 0) {
                throw new ServerException('Server error');
            }
            $messageParts = [
                'message' => $message
            ];
            if (Environment::isDev()) {
                $messageParts['level'] = "Error level: '$errorLevel'";
                $messageParts['filename'] = "Filename: '$filename'";
                $messageParts['line'] = "Line: '$lineno'";
            }
            throw new ServerException(implode("\n", $messageParts));
        });
    }

    private function setTimezone(): void
    {
        date_default_timezone_set($this->config['timezone']);
    }

    /**
     * @throws ServerException
     */
    private function init(): void
    {
        $this->registerErrorHandler();
        $this->validateConfig();
        $this->setTimezone();
        $this->container = new Container($this);
        $this->container->getDb()->connect();
    }

    public function getDbConfig(): array
    {
        return $this->config['services'][Container::DB] ?? [];
    }

    public function getFactoryConfig(): array
    {
        return $this->config['services'][Container::ACTION_FACTORY] ?? [];
    }

    private function missingConfigAttributeException(array $keys): ServerException
    {
        return new ServerException(sprintf(
            'Config "%s" missing',
            implode('->', $keys)
        ));
    }

    private function redirect(string $path): void
    {
        header("Location: /$path");
    }
}
