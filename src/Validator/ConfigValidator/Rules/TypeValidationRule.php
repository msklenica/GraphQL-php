<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 6:07 PM
*/

namespace Youshido\GraphQL\Validator\ConfigValidator\Rules;


use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Field\InputField;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\InputObject\AbstractInputObjectType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeFactory;
use Youshido\GraphQL\Type\TypeService;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;

class TypeValidationRule implements ValidationRuleInterface
{

    private $configValidator;

    public function __construct(ConfigValidator $validator)
    {
        $this->configValidator = $validator;
    }

    public function validate($data, $ruleInfo)
    {
            if (!is_string($ruleInfo)) return false;
//        /** why can it be an object? */
//        if (is_object($ruleInfo)) {
//            $className = get_class($data);
//            $className = substr($className, strrpos($className, '\\') + 1, -4);
//
//            return ($className == $ruleInfo);
//        } elseif (is_string($ruleInfo)) {

            switch ($ruleInfo) {
                case TypeService::TYPE_ANY:
                    return true;

                case TypeService::TYPE_ANY_OBJECT:
                    return is_object($data);

                case TypeService::TYPE_CALLABLE:
                    return is_callable($data);

                case TypeService::TYPE_BOOLEAN:
                    return is_bool($data);

                case TypeService::TYPE_ARRAY:
                    return is_array($data);

                case TypeService::TYPE_STRING:
                    return TypeFactory::getScalarType($ruleInfo)->isValidValue($data);

                case TypeService::TYPE_GRAPHQL_TYPE:
                    return TypeService::isGraphQLType($data);

                case TypeService::TYPE_OBJECT_TYPE:
                    return TypeService::isObjectType($data);

                case TypeService::TYPE_ARRAY_OF_GRAPHQL_TYPES:
                    return $this->isArrayOfGraphQLTypes($data);

                case TypeService::TYPE_ARRAY_OF_FIELDS_CONFIG:
                    return $this->isFieldsListConfig($data);

                case TypeService::TYPE_OBJECT_INPUT_TYPE:
                    return TypeService::isInputObjectType($data);

                case TypeService::TYPE_ENUM_VALUES:
                    return $this->isEnumValues($data);

                case TypeService::TYPE_ARRAY_OF_INPUTS:
                    return $this->isArrayOfInputs($data);

                case TypeService::TYPE_ANY_INPUT:
                    return TypeService::isInputType($data);

                case TypeService::TYPE_ARRAY_OF_INTERFACES:
                    return $this->isArrayOfInterfaces($data);

                default:
                    return false;
            }
        return false;

    }

    private function isArrayOfGraphQLTypes($data)
    {
        if (!is_array($data) || !count($data)) {
            return false;
        }

        foreach($data as $item) {
            if(!TypeService::isGraphQLType($item)) {
                return false;
            }
        }

        return true;
    }

    private function isEnumValues($data)
    {
        if (!is_array($data) || empty($data)) return false;

        foreach ($data as $item) {
            if (!is_array($item) || !array_key_exists('name', $item) || !is_string($item['name']) || !preg_match('/^[_a-zA-Z][_a-zA-Z0-9]*$/', $item['name'])) {
                return false;
            }

            if (!array_key_exists('value', $item)) {
                return false;
            }
        }

        return true;
    }

    private static function isArrayOfInterfaces($data)
    {
        if (!is_array($data)) return false;

        foreach ($data as $item) {
            if (!TypeService::isInterface($item)) {
                return false;
            }
        }

        return true;
    }

    private function isFieldsListConfig($data)
    {
        if (!is_array($data) || empty($data)) return false;

        foreach ($data as $name => $item) {
            if (!$this->isField($item, $name)) return false;
        }

        return true;
    }

    private function isField($data, $name = null)
    {
        if (is_object($data)) {
            return ($data instanceof Field) || ($data instanceof AbstractType);
        }
        if (!is_array($data)) {
            $data = [
                'type' => $data,
                'name' => $name,
            ];
        } elseif (empty($data['name'])) {
            $data['name'] = $name;
        }
        $this->configValidator->validate($data, $this->getFieldConfigRules());
        return $this->configValidator->isValid();
    }

    private function isArrayOfInputs($data)
    {
        if (!is_array($data)) return false;

        foreach ($data as $name => $item) {
            if (!$this->isInputField($item, $name)) return false;
        }

        return true;
    }

    private function isInputField($data, $name = null)
    {
        if (is_object($data)) {
            if ($data instanceof InputField) {
                return true;
            } elseif ($data instanceof AbstractType) {
                return TypeService::isInputType($data);
            }
        } else {
            if (!isset($data['type'])) {
                return false;
            }

            return TypeService::isInputType($data['type']);
        }

        return false;
    }

    /**
     * Exists for the performance
     * @return array
     */
    private function getFieldConfigRules()
    {
        return [
            'name'              => ['type' => TypeService::TYPE_STRING, 'required' => true],
            'type'              => ['type' => TypeService::TYPE_ANY, 'required' => true],
            'args'              => ['type' => TypeService::TYPE_ARRAY],
            'description'       => ['type' => TypeService::TYPE_STRING],
            'resolve'           => ['type' => TypeService::TYPE_CALLABLE],
            'isDeprecated'      => ['type' => TypeService::TYPE_BOOLEAN],
            'deprecationReason' => ['type' => TypeService::TYPE_STRING],
        ];
    }

}
