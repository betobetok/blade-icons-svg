<?php

declare(strict_types=1);

namespace BladeUI\Icons\Shapes;

<<<<<<< Updated upstream
use BladeUI\Icons\SvgElement;
use Exception;
=======
use ASK\Svg\SvgElement;
>>>>>>> Stashed changes

/**
 * Path
 */
class Path extends Shape
{
    /** @var string  $dString*/
    private $dString = '';

    /** @var array d*/
    protected array $d = [];

    /**
     * 
     *
     * @param  mixed $name
     * @param  mixed $contents
     * @param  mixed $attributes
     * @param  SvgElement $context
     * @return void
     */
    public function __construct(string $contents, array $attributes = [], SvgElement $context = null)
    {
        parent::__construct($contents,  $attributes, $context);
        if (isset($this->attributes()['d']) && !empty($this->attributes()['d'])) {
            $this->dString = $this->attributes()['d'];
        }
        $this->d = $this->getExistingComands($this->dString);
        $this->removeAtt('d');

        if (!empty($this->d)) {
            $this->startPosition = $this->d[0][array_key_first($this->d[0])]->endPointAbs;
        }
        unset($this->dString);
    }

    /**
     * toHtml
     *
     * @return string
     */
    public function toHtml(): string
    {
        $dString = $this->content();
        return sprintf('<%s d="%s" %s/>', $this->name(), $dString, $this->renderAttributes());
    }

    /**
     * content get the content string of the svg elemnt to print in a HTML document
     *
     * @return string
     */
    public function content(): string
    {
        $content = '';
        foreach ($this->d as $comand) {
            foreach ($comand as $name => $confg) {
                $content .= $confg->toHtml();
            }
        }
        return $content;
    }

    /**
     * getExistingComands get the existing comands in a d attribute of a Paht element
     *
     * @param  string $d
     * @return array
     */
    public function getExistingComands(string $d): array
    {
        $commands = [];
        $prev = null;
        preg_match_all('/([a-zA-Z]{1})\s?([e0-9\s,.-]+)?[^A-Za-z]?/', $d, $match);
        foreach ($match[1] as $k => $name) {
            preg_match_all('/(-?[0-9.]+(e-\d+)?)/', $match[2][$k], $arguments);
            $commandClass = 'ASK\\Svg\\DCommands\\' . ucfirst($name);
            $type = $name === strtolower($name) ? 'relative' : 'absolute';
            if (class_exists($commandClass)) {
                $command = new $commandClass($type, $arguments[0], $prev);
                $prev = $command;
            }
            $commands[$k][$name] = $command ?? $commandClass;
        }
        return $commands;
    }

    protected function renderAttributes(): string
    {
        if (count($this->attributes()) == 0) {
            return '';
        }

        return ' ' . collect($this->attributes())->map(function (string $value, $attribute) {
            if (is_int($attribute)) {
                return $value;
            }

            return sprintf('%s="%s"', $attribute, $value);
        })->implode(' ');
    }
}
