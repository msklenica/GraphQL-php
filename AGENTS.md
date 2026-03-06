# AGENTS.md - Guidelines for Agentic Code Operations

This document provides guidance for agentic coding agents operating in the GraphQL-php repository.

## Build, Lint & Test Commands

### Docker Setup (Recommended)

All testing **MUST** be done via Docker container to ensure consistency across environments.

```bash
# Build the Docker image
docker compose build

# Start the container
docker compose up -d

# Stop the container
docker compose down

# View container logs
docker compose logs -f app
```

### Running Tests via Docker

```bash
# Run all tests
docker exec app ./vendor/bin/phpunit

# Run a single test file
docker exec app ./vendor/bin/phpunit tests/Path/To/TestNameTest.php

# Run a specific test method
docker exec app ./vendor/bin/phpunit tests/Path/To/TestNameTest.php --filter testMethodName

# Run with code coverage
docker exec app ./vendor/bin/phpunit --coverage-clover=coverage.clover

# Run tests with verbose output
docker exec app ./vendor/bin/phpunit -v
```

### Code Quality & Analysis via Docker

```bash
# Static analysis with PHPStan
docker exec app ./vendor/bin/phpstan analyze src -c phpstan.neon -l 1

# Run Rector (code modernization) - dry run to preview
docker exec app ./vendor/bin/rector process src --dry-run

# Apply Rector changes
docker exec app ./vendor/bin/rector process src

# Install/update dependencies
docker exec app composer install
docker exec app composer update
```

### Local Development (Direct CLI - Not Recommended for CI)

If running locally without Docker:

```bash
# Run all tests
./vendor/bin/phpunit

# Run a single test file
./vendor/bin/phpunit tests/Path/To/TestNameTest.php

# Run a specific test method
./vendor/bin/phpunit tests/Path/To/TestNameTest.php --filter testMethodName
```

### Project Setup
- PHP: ^8.4 (currently running 8.4.18)
- Symfony: ^7.4 (property-access component)
- PHPUnit: ^10.5
- Rector: Latest
- Prophecy: ^1.19 (with prophecy-phpunit ^2.5 for PHPUnit 10 integration)

## Code Style Guidelines

### Namespacing & Imports
- Namespace: `Youshido\GraphQL\` (main code), `Youshido\Tests\` (tests)
- **Import Order**: Group imports by category, separated by blank lines:
  1. Symfony imports first
  2. Youshido GraphQL imports
  3. Youshido Test imports
  4. Built-in PHP classes (Exception, etc.)
- Example:
  ```php
  use Symfony\Component\PropertyAccess\PropertyAccess;
  
  use Youshido\GraphQL\Config\Field\FieldConfig;
  use Youshido\GraphQL\Type\AbstractType;
  use Youshido\GraphQL\Type\TypeFactory;
  
  use Youshido\Tests\DataProvider\TestObjectType;
  ```

### Formatting & Spacing
- **Indentation**: 4 spaces (not tabs)
- **Line length**: No strict limit, but keep reasonable (aim for < 120 chars)
- **Brace style**: Opening brace on same line for classes/methods (PSR-2)
  ```php
  class MyClass {
      public function myMethod() {
      }
  }
  ```
- **Array formatting**: Use modern PHP 7.4+ short array syntax `[]`
- **Spacing**: No space after method call parentheses: `myMethod()` not `myMethod ()`

### Type Declarations
- **Always use full type hints** on method parameters and return types
- **Use union types** where appropriate: `int|string|null`
- **Nullable types**: Use `?Type` or `Type|null` (prefer `?Type` for single types)
- **Generic types in docblocks**: Use PHPDoc for collections
  ```php
  /**
   * @param array<string, mixed> $config
   * @return array<int, Field>
   */
  public function getFields(array $config): array
  ```
- **Late static binding**: Use `static` return type when appropriate
- **Constructor promotion**: Supported (PHP 8.0+)

### Naming Conventions
- **Classes**: PascalCase, descriptive nouns (e.g., `AbstractObjectType`, `FieldConfig`)
- **Methods**: camelCase, verb-focused (e.g., `getType()`, `isValidValue()`, `setType()`)
- **Constants**: UPPER_SNAKE_CASE (e.g., `TYPE_STRING`, `KIND_INTERFACE`)
- **Properties**: camelCase, private/protected with underscore prefix discouraged
- **Test classes**: Suffix with `Test` (e.g., `ObjectTypeTest`)
- **Test methods**: Start with `test` (e.g., `testInvalidNameParam()`)

### Error Handling & Exceptions
- **Use specific exceptions**:
  - `ConfigurationException`: Invalid configuration during setup
  - `ResolveException`: Runtime resolution errors (extends `LocationableExceptionInterface`)
  - `ValidationException`: GraphQL validation failures
  - `SyntaxErrorException`: Parser syntax errors
- **Always provide context**: Include field/type names in error messages
  ```php
  throw new ConfigurationException(sprintf(
      'Invalid field "%s" on type "%s"',
      $fieldName,
      $this->getName()
  ));
  ```
- **Validation error messages**: Use `$type->getValidationError()` and `lastValidationError` property

### Class Structure Best Practices
- **Abstract base classes**: Prefix with `Abstract` (e.g., `AbstractObjectType`)
- **Traits for shared behavior**: Common suffix conventions
  - `FieldsArgumentsAwareObjectTrait`: Provides field/argument handling
  - `ResolvableObjectTrait`: Adds resolver support
  - `ErrorContainerTrait`: Error collection/reporting
- **Interfaces**: Suffix with `Interface` (e.g., `FieldInterface`)
- **Property visibility**: Use `protected` for subclass access, `private` for encapsulation
- **Factory patterns**: Static methods in `TypeFactory`, `PropertyAccess` usage

### Iteration Patterns
- Use **modern foreach** over `each()` (deprecated in PHP 8.1+)
- **Array operations**: Use `array_*` functions or collect/map patterns
- **Type coercion**: Explicitly cast with `(array)`, `(bool)`, `(int)`, etc.

### Documentation
- **File headers**: Include copyright/author block (optional per file style)
  ```php
  /**
   * Date: DD.MM.YY
   *
   * @author Name <email@example.com>
   */
  ```
- **PHPDoc tags**: Use `@param`, `@return`, `@throws` for public methods
- **Inline comments**: Explain "why", not "what" the code does

### Common Patterns to Follow
- **Builder patterns**: Use fluent interfaces where configuration requires chaining
- **Config classes**: Extend `AbstractConfig` with getter/setter patterns
- **Type checking**: Use `TypeService::isGraphQLType()`, `isScalarType()`, etc.
- **Property access**: Use Symfony `PropertyAccessor` for dynamic property resolution

## File Organization
```
src/
  ├── Type/              # GraphQL type definitions
  ├── Field/             # Field definitions
  ├── Schema/            # Schema management
  ├── Config/            # Configuration classes
  ├── Execution/         # Query execution logic
  ├── Parser/            # GraphQL parsing
  ├── Validator/         # Validation logic
  ├── Exception/         # Custom exceptions
  └── ...

tests/
  └── Library/           # Organized by component mirrors src/
```

## Quick Reference: Test File Template
```php
<?php
namespace Youshido\Tests\Library\Type;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Exception\ConfigurationException;

class MyTypeTest extends TestCase
{
    public function testValidBehavior(): void
    {
        $type = new ObjectType(['name' => 'MyType', 'fields' => []]);
        $this->assertEquals('MyType', $type->getName());
    }

    public function testInvalidConfiguration(): void
    {
        $this->expectException(ConfigurationException::class);
        new ObjectType([]);
    }

    #[DataProvider('myProvider')]
    public function testWithDataProvider(string $value): void
    {
        $this->assertTrue(true);
    }

    public static function myProvider(): array
    {
        return [
            ['test1'],
            ['test2'],
        ];
    }
}
```

## Before Committing
1. Run `./vendor/bin/phpunit` - all tests must pass
2. Run `./vendor/bin/phpstan analyze src -c phpstan.neon -l 1` - check analysis
3. Verify code follows namespace/import ordering
4. Ensure all public methods have type hints and return types
5. Add docblock for complex logic or non-obvious behavior

---
**Last Updated**: 2026-03-06 | **PHP Version**: 8.4+ | **Symfony**: 7.4+
