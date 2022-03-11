<?php

namespace App\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AppExtension extends AbstractExtension
{
    protected ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('flagCode', [$this, 'getFlagCode']),
        ];
    }

    public function getFlagCode(string $locale)
    {
        switch ($locale) {
            case 'en':
                return 'GB';
            case 'hu':
                return 'HU';
            case 'sr_Latn':
                return 'RS';
            default:
                return $this->params->get('app.default_locale');
        }
    }
}
