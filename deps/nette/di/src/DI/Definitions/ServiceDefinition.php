<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */
declare (strict_types=1);
namespace Packetery\Nette\DI\Definitions;

use Packetery\Nette;
use Packetery\Nette\DI\ServiceCreationException;
/**
 * Definition of standard service.
 *
 * @property string|null $class
 * @property Statement $factory
 * @property Statement[] $setup
 */
final class ServiceDefinition extends Definition
{
    /** @var Statement */
    private $creator;
    /** @var Statement[] */
    private $setup = [];
    public function __construct()
    {
        $this->creator = new Statement(null);
    }
    /** @return static */
    public function setType(?string $type)
    {
        return parent::setType($type);
    }
    /**
     * Alias for setCreator()
     * @param  string|array|Definition|Reference|Statement  $factory
     * @return static
     */
    public function setFactory($factory, array $args = [])
    {
        return $this->setCreator($factory, $args);
    }
    /**
     * Alias for getCreator()
     */
    public function getFactory() : Statement
    {
        return $this->getCreator();
    }
    /**
     * @param  string|array|Definition|Reference|Statement  $creator
     * @return static
     */
    public function setCreator($creator, array $args = [])
    {
        $this->creator = $creator instanceof Statement ? $creator : new Statement($creator, $args);
        return $this;
    }
    public function getCreator() : Statement
    {
        return $this->creator;
    }
    /** @return string|array|Definition|Reference|null */
    public function getEntity()
    {
        return $this->creator->getEntity();
    }
    /** @return static */
    public function setArguments(array $args = [])
    {
        $this->creator->arguments = $args;
        return $this;
    }
    /** @return static */
    public function setArgument($key, $value)
    {
        $this->creator->arguments[$key] = $value;
        return $this;
    }
    /**
     * @param  Statement[]  $setup
     * @return static
     */
    public function setSetup(array $setup)
    {
        foreach ($setup as &$entity) {
            if (!$entity instanceof Statement) {
                throw new \Packetery\Nette\InvalidArgumentException('Argument must be \\Packetery\\Nette\\DI\\Definitions\\Statement[].');
            }
            $entity = $this->prependSelf($entity);
        }
        $this->setup = $setup;
        return $this;
    }
    /** @return Statement[] */
    public function getSetup() : array
    {
        return $this->setup;
    }
    /**
     * @param  string|array|Definition|Reference|Statement  $entity
     * @return static
     */
    public function addSetup($entity, array $args = [])
    {
        $entity = $entity instanceof Statement ? $entity : new Statement($entity, $args);
        $this->setup[] = $this->prependSelf($entity);
        return $this;
    }
    public function resolveType(\Packetery\Nette\DI\Resolver $resolver) : void
    {
        if (!$this->getEntity()) {
            if (!$this->getType()) {
                throw new ServiceCreationException('Factory and type are missing in definition of service.');
            }
            $this->setCreator($this->getType(), $this->creator->arguments ?? []);
        } elseif (!$this->getType()) {
            $type = $resolver->resolveEntityType($this->creator);
            if (!$type) {
                throw new ServiceCreationException('Unknown service type, specify it or declare return type of factory method.');
            }
            $this->setType($type);
            $resolver->addDependency(new \ReflectionClass($type));
        }
        // auto-disable autowiring for aliases
        if ($this->getAutowired() === \true && $this->getEntity() instanceof Reference) {
            $this->setAutowired(\false);
        }
    }
    public function complete(\Packetery\Nette\DI\Resolver $resolver) : void
    {
        $entity = $this->creator->getEntity();
        if ($entity instanceof Reference && !$this->creator->arguments && !$this->setup) {
            $ref = $resolver->normalizeReference($entity);
            $this->setCreator([new Reference(\Packetery\Nette\DI\ContainerBuilder::ThisContainer), 'getService'], [$ref->getValue()]);
        }
        $this->creator = $resolver->completeStatement($this->creator);
        foreach ($this->setup as &$setup) {
            $setup = $resolver->completeStatement($setup, \true);
        }
    }
    private function prependSelf(Statement $setup) : Statement
    {
        return \is_string($setup->getEntity()) && \strpbrk($setup->getEntity(), ':@?\\') === \false ? new Statement([new Reference(Reference::Self), $setup->getEntity()], $setup->arguments) : $setup;
    }
    public function generateMethod(\Packetery\Nette\PhpGenerator\Method $method, \Packetery\Nette\DI\PhpGenerator $generator) : void
    {
        $code = $generator->formatStatement($this->creator) . ";\n";
        if (!$this->setup) {
            $method->setBody('return ' . $code);
            return;
        }
        $code = '$service = ' . $code;
        foreach ($this->setup as $setup) {
            $code .= $generator->formatStatement($setup) . ";\n";
        }
        $code .= 'return $service;';
        $method->setBody($code);
    }
    public function __clone()
    {
        parent::__clone();
        $this->creator = \unserialize(\serialize($this->creator));
        $this->setup = \unserialize(\serialize($this->setup));
    }
}
\class_exists(\Packetery\Nette\DI\ServiceDefinition::class);
