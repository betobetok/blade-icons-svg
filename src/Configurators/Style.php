<?php

declare(strict_types=1);

namespace ASK\Svg\Configurators;

use ASK\Svg\Configurators\Configurator;
use ASK\Svg\SvgElement;

/**
 * The Style element in a svg document
 */
class Style extends Configurator
{
    /** @var array $classes */
    private $classes;

    /** @var array $classes */
    private $rules;

    public function __construct(string $svgContent, array $attributes = [], SvgElement $context = null)
    {
        $name = '';
        if ($context !== null) {
            $name = $context->id();
        }
        $contents = $this->configAttributesAndContent('style', $svgContent, []);

        parent::__construct($contents, [], $context);
        $this->getRules();
        $this->renameClasses($name);
        $this->removeContents();
        unset($this->elements);
    }

    /**
     * renameClasses agregate the name of the svg at ende of the classes neme 
     * to avoid a name conflict between svgs in a merge
     *
     * @param  string $svgElementName
     * @return self
     */
    public function renameClasses(string $svgElementName): self
    {
        $this->classes = [];
        foreach ($this->rules as $selector => $declarations) {
            if (strpos($selector, '.') === 0) {
                $className = $selector . '-' . $svgElementName;
                $this->classes[$className] = $declarations;
                $this->rules[$className] = $declarations;
                unset($this->rules[$selector]);
            }
        }
        return $this;
    }

    /**
     * makeRules create an array of roules from the string content
     *
     * @return void
     */
    public function makeRules()
    {
        $this->rules = [];
        preg_match_all("/([.#a-z0-9-]*)\s?({[^}]*})/i", $this->contents(), $comands);
        foreach ($comands[1] as $k => $selector) {
            preg_match_all("/([a-z0-9-]*):([^;]*);/i", $comands[2][$k], $declarations);
            $this->rules[$selector] = [];
            foreach ($declarations[1] as $i => $property) {
                $this->rules[$selector][$property] = $declarations[2][$i];
            }
        }
    }

    /**
     * get the css classes from style
     *
     * @return array
     */
    public function classes(): array
    {
        return $this->classes;
    }

    /**
     *  get the css rules from style
     *
     * @return array
     */
    public function rules(): array
    {
        return $this->rules;
    }

    /**
     * setClasses set the css classes for style
     *
     * @param  array $classes
     * @return self
     */
    public function setClasses(array $classes): self
    {
        $this->classes = $classes;
        return $this;
    }

    /**
     * (overloaded Method from SvgElement)
     *
     * @return string
     */
    public function toHtml(): string
    {
        if (empty($this->rules())) {
            return '';
        }

        $ret = sprintf("<style %s>\n", $this->renderAttributes());

        foreach ($this->rules() as $ruleName => $declarations) {
            $ret .=  sprintf("%s {\n", $ruleName);
            foreach ($declarations as $property => $value) {
                $ret .=  sprintf("\t%s: %s;\n", $property, $value);
            }
            $ret .= "}\n";
        }
        $ret .= '</style>';
        return $ret;
    }

    /**
     * mergeStyles Merge one Style with an other
     *
     * @param  Style $add
     * @return self|null
     */
    public function mergeStyles(Style $add): ?self
    {
        $this->rules = array_merge($this->rules, $add->rules);
        $array = array_merge($this->attributes(), $add->attributes());
        foreach ($array as $name => $arguments) {
            $this->setAttribute($name, $arguments);
        }

        return $this;
    }

    /**
     * toArray
     *
     * @return void
     */
    public function toArray()
    {
        $ret = parent::toArray();
        $ret['rules'] = $this->rules();
        return $ret;
    }
}
