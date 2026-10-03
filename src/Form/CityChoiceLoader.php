<?php
declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\ChoiceList\Loader\AbstractChoiceLoader;

final class CityChoiceLoader extends AbstractChoiceLoader
{
    protected function loadChoices(): iterable
    {
        return [];
    }

    protected function doLoadChoicesForValues(array $values, ?callable $value): array
    {
        $choices = [];
        foreach ($values as $name) {
            $choices[$name] = $name;
        }

        return $choices;
    }
}