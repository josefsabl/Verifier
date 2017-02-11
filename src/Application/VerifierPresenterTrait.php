<?php

namespace Arachne\Verifier\Application;

use Arachne\Verifier\Exception\NotSupportedException;
use Arachne\Verifier\Verifier;
use Nette\Application\BadRequestException;
use ReflectionClass;
use ReflectionMethod;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
trait VerifierPresenterTrait
{
    use VerifierControlTrait;

    /**
     * @var callable[]
     */
    public $onStartup;

    /**
     * @var Verifier
     */
    private $verifier;

    /**
     * @param Verifier $verifier
     */
    final public function injectVerifier(Verifier $verifier)
    {
        $this->verifier = $verifier;
    }

    /**
     * @param ReflectionClass|ReflectionMethod $reflection
     */
    public function checkRequirements($reflection)
    {
        $rules = $this->verifier->getRules($reflection);
        if ($rules && $reflection instanceof ReflectionMethod && substr($reflection->getName(), 0, 6) === 'render') {
            throw new NotSupportedException('Rules for render methods are not supported. Define the rules for action method instead.');
        }
        $this->verifier->checkRules($rules, $this->getRequest());
    }

    /**
     * @return Verifier
     */
    public function getVerifier()
    {
        return $this->verifier;
    }

    /**
     * Ensures that the action method exists.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return bool
     */
    protected function tryCall($method, array $parameters)
    {
        $called = parent::tryCall($method, $parameters);
        if (!$called && substr($method, 0, 6) === 'action') {
            $class = get_class($this);
            throw new BadRequestException("Action '$class::$method' does not exist.");
        }

        return $called;
    }

    /**
     * This method has to be public because it is called by VerifierControlTrait.
     *
     * @internal
     */
    public function createRequest($component, $destination, array $parameters, $mode)
    {
        return parent::createRequest($component, $destination, $parameters, $mode);
    }

    /**
     * Calls onStartup event which is used to verify presenter properties.
     */
    public function startup()
    {
        parent::startup();
        $this->onStartup();
    }
}
