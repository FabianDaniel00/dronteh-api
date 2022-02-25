<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    public function __construct(string $environment, bool $debug)
    {
        date_default_timezone_set('Europe/Belgrade');
        parent::__construct($environment, $debug);
    }

    use MicroKernelTrait;
}
