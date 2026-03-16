<?php
/**
 * Date: 10/24/16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast\ArgumentValue;


use Youshido\GraphQL\Parser\Ast\AbstractAst;
use Youshido\GraphQL\Parser\Ast\Interfaces\ValueInterface;
use Youshido\GraphQL\Parser\Location;

class VariableReference extends AbstractAst implements ValueInterface
{

    private string $name;

    private Variable|null $variable;

    private mixed $value;

    /**
     * @param string        $name
     * @param Variable|null $variable
     * @param Location|null $location
     */
    public function __construct($name, ?Variable $variable = null, ?Location $location = null)
    {
        parent::__construct($location ?? new Location(0, 0));

        $this->name     = $name;
        $this->variable = $variable;
    }

    public function getVariable()
    {
        return $this->variable;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
