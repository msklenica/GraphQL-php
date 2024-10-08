<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/12/16 4:17 PM
*/

namespace Youshido\Tests\Library\Config;


use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Youshido\Tests\DataProvider\TestInterfaceType;

class ObjectTypeConfigTest extends \PHPUnit\Framework\TestCase
{

    public function testCreation()
    {
        $config = new ObjectTypeConfig(['name' => 'Test'], null, false);
        $this->assertEquals($config->getName(), 'Test', 'Normal creation');
    }

    public function testInvalidConfigNoFields()
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        ConfigValidator::getInstance()->assertValidConfig(
            new ObjectTypeConfig(['name' => 'Test'], null, true)
        );
    }

    public function testInvalidConfigInvalidInterface()
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        ConfigValidator::getInstance()->assertValidConfig(
            new ObjectTypeConfig(['name' => 'Test', 'interfaces' => ['Invalid interface']], null, false)
        );
    }

    public function testInterfaces()
    {
        $testInterfaceType = new TestInterfaceType();
        $config            = new ObjectTypeConfig(['name' => 'Test', 'interfaces' => [$testInterfaceType]], null, false);
        $this->assertEquals($config->getInterfaces(), [$testInterfaceType]);
    }


}
