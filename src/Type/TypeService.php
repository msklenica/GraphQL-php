<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/11/16 10:19 PM
*/

namespace Youshido\GraphQL\Type;


use Youshido\GraphQL\Type\InputObject\AbstractInputObjectType;
use Youshido\GraphQL\Type\ListType\AbstractListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;
use Youshido\GraphQL\Type\Scalar\StringType;

class TypeService
{

    const TYPE_CALLABLE               = 'callable';
    const TYPE_GRAPHQL_TYPE           = 'graphql_type';
    const TYPE_OBJECT_TYPE            = 'object_type';
    const TYPE_OBJECT_INPUT_TYPE      = 'object_input_type';
    const TYPE_LIST                   = 'list';
    const TYPE_BOOLEAN                = TypeMap::TYPE_BOOLEAN;
    const TYPE_STRING                 = TypeMap::TYPE_STRING;
    const TYPE_ARRAY                  = 'array';
    const TYPE_ARRAY_OF_FIELDS_CONFIG = 'array_of_fields';
    const TYPE_ARRAY_OF_GRAPHQL_TYPES = 'array_of_objects';
    const TYPE_ARRAY_OF_INPUT_FIELDS  = 'array_of_inputs';
    const TYPE_ENUM_VALUES            = 'array_of_values';
    const TYPE_ARRAY_OF_INTERFACES    = 'array_of_interfaces';
    const TYPE_ANY                    = 'any';
    const TYPE_ANY_OBJECT             = 'any_object';
    const TYPE_ANY_INPUT              = 'any_input';

    public static function resolveNamedType($object)
    {
        if (is_object($object)) {
            if ($object instanceof AbstractType) {
                return $object->getType();
            }
        } elseif (is_null($object)) {
            return null;
        } elseif (is_scalar($object)) {
            return new StringType();
        }

        throw new \Exception('Invalid type');
    }

    /**
     * @param AbstractType|mixed $type
     * @return bool
     */
    public static function isInterface($type)
    {
        if (!is_object($type)) {
            return false;
        }

        return $type->getKind() == TypeMap::KIND_INTERFACE;
    }

    /**
     * @param AbstractType|mixed $type
     * @return bool
     */
    public static function isAbstractType($type)
    {
        if (!is_object($type)) {
            return false;
        }

        return in_array($type->getKind(), [TypeMap::KIND_INTERFACE, TypeMap::KIND_UNION]);
    }

    public static function isScalarType($type)
    {
        if (is_object($type)) {
            return $type instanceof AbstractScalarType;
        }

        return in_array(strtolower($type), TypeFactory::getScalarTypesNames());
    }

    public static function isGraphQLType($type)
    {
        return TypeService::isObjectType($type) || TypeService::isScalarType($type);
    }

    public static function isObjectType($type)
    {
        return $type instanceof AbstractObjectType;
    }

    /**
     * @param mixed|AbstractType $type
     * @return bool
     */
    public static function isInputType($type)
    {
        if (is_object($type)) {
            $type = $type->getNullableType();

            return ($type instanceof AbstractScalarType)
                   || ($type instanceof AbstractInputObjectType)
                   || ($type instanceof AbstractListType);
        } else {
            return TypeService::isScalarType($type);
        }
    }

    public static function isInputObjectType($type)
    {
        return $type instanceof AbstractInputObjectType;
    }
}
