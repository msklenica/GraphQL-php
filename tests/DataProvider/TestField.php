<?php
/**
 * Date: 13.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\DataProvider;

use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;

class TestField extends AbstractField
{

    /**
     * @return AbstractObjectType
     */
    #[\Override]
    public function getType()
    {
        return new IntType();
    }

    #[\Override]
    public function resolve($value, array $args, ResolveInfo $info)
    {
        return $value;
    }

    #[\Override]
    public function getDescription()
    {
        return 'description';
    }
}
