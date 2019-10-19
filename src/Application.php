<?php
/**
 * This file is part of the Versio package.
 *
 * (c) Jason Schilling <jason.schilling@sourecode.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * File that was distributed with this source code.
 */

namespace Versio;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class Application extends BaseApplication
{
    public static $version = '0.1.0-DEV';

    private $kernel;
    private $commandsRegistered = false;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        parent::__construct('Versio', self::$version);
    }

    /**
     * {@inheritdoc}
     */
    public function getKernel(): KernelInterface
    {
        return $this->kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        $this->registerCommands();
        $this->setDispatcher($this->kernel->getContainer()->get('event_dispatcher'));

        return parent::doRun($input, $output);
    }

    protected function registerCommands(): void
    {
        if ($this->commandsRegistered) {
            return;
        }

        $this->commandsRegistered = true;

        $this->kernel->boot();

        $container = $this->kernel->getContainer();

        if ($container->has('custom.command_loader')) {
            $this->setCommandLoader($container->get('custom.command_loader'));
        }
    }

    public function add(Command $command): ?Command
    {
        $this->registerCommands();

        return parent::add($command);
    }

    /**
     * {@inheritdoc}
     */
    public function find($name): Command
    {
        $this->registerCommands();

        return parent::find($name);
    }

    /**
     * {@inheritdoc}
     */
    public function get($name): Command
    {
        $this->registerCommands();

        $command = parent::get($name);

        if ($command instanceof ContainerAwareInterface) {
            $command->setContainer($this->kernel->getContainer());
        }

        return $command;
    }

    /**
     * {@inheritdoc}
     */
    public function all($namespace = null): array
    {
        $this->registerCommands();

        return parent::all($namespace);
    }

}