<?php

declare(strict_types=1);

namespace Tests\Unit\Classes;

use Nette\Application\UI\Control;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 *
 * @TestRule
 */
class TestControl extends Control
{
    /**
     * @TestRule
     */
    public function handleSignal(): void
    {
    }

    /**
     * @TestRule
     */
    public function createComponentComponent(): void
    {
    }
}
