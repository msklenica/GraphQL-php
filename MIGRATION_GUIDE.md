# Migration Guide: GraphQL-PHP v1.0.2 → v2.0.0

## Overview

This guide helps you upgrade from GraphQL-PHP v1.0.2 to v2.0.0. The upgrade is straightforward for most projects, but you need to be aware of **one critical breaking change**: Symfony version requirements.

**TL;DR:** 
- ✅ If you're on Symfony 6.4+: Upgrade directly to v2.0.0
- ⚠️ If you're on Symfony <6.4: Upgrade Symfony first, then upgrade GraphQL-PHP

---

## The Main Breaking Change: Symfony Versions

### What Changed?

GraphQL-PHP v2.0.0 drops support for older Symfony versions:

| Symfony | v1.0.2 | v2.0.0 | Action Required |
|---------|--------|--------|-----------------|
| 2.8 | ✅ Supported | ❌ Dropped | **MUST upgrade Symfony** |
| 3.4 | ✅ Supported | ❌ Dropped | **MUST upgrade Symfony** |
| 4.4 | ✅ Supported | ❌ Dropped | **MUST upgrade Symfony** |
| 5.4 | ✅ Supported | ❌ Dropped | **MUST upgrade Symfony** |
| 6.4 | ✅ Supported | ✅ Supported | ✅ Safe to upgrade |
| 7.4 | ❌ Not supported | ✅ Supported | ✅ New support! |
| 8.0 | ❌ Not supported | ✅ Supported | ✅ New support! |

### Why This Change?

1. **End of Life:** Symfony 2.8-5.4 are no longer receiving updates
2. **Type Safety:** Dropping legacy versions unblocks modern PHP 8.4 features
3. **Maintenance:** Supporting modern versions reduces technical debt
4. **Future-Proof:** Aligns with PHP 8.3+ minimum requirement

### How to Check Your Current Symfony Version

```bash
# Check installed version
composer show symfony/property-access

# Check your composer.json requirement
grep symfony/property-access composer.json
```

---

## Upgrade Paths

### Path 1: You're Using Symfony 6.4+ ✅ (Easy)

**Your situation:** You're already on Symfony 6.4, 7.4, or 8.0

**Steps:**
1. Update composer.json or run:
   ```bash
   composer require 99designs/graphql:^2.0
   ```

2. Run tests:
   ```bash
   ./vendor/bin/phpunit
   ```

3. Done! No other changes needed.

**Duration:** 5-10 minutes

---

### Path 2: You're Using Symfony 2.8-5.4 ⚠️ (Requires Planning)

**Your situation:** You're using an older Symfony version that's no longer supported

**Step 1: Upgrade Symfony First**

```bash
# First, upgrade Symfony to 6.4 or newer
composer require symfony/property-access:^6.4

# Or use Symfony Flex for full framework upgrade
composer require symfony/console:^6.4
# (repeat for other Symfony packages you use)
```

**Step 2: Run Your Tests**

```bash
# Make sure your application still works with Symfony 6.4+
./vendor/bin/phpunit
```

**Step 3: Then Upgrade GraphQL-PHP**

```bash
composer require 99designs/graphql:^2.0
```

**Step 4: Final Testing**

```bash
./vendor/bin/phpunit
./bin/console lint:yaml config/  # or your config path
```

**Duration:** 30 minutes - 2 hours depending on your Symfony version gap

**Pro Tip:** Test Symfony upgrades thoroughly. We recommend:
1. First upgrade to Symfony 6.4 LTS (most stable)
2. Run all tests locally
3. Then upgrade to Symfony 7.4 or 8.0 if you want the latest

---

## No Code Changes Needed

The good news: **You don't need to change any of your GraphQL code!**

All improvements in v2.0.0 are:
- ✅ Backward compatible at runtime
- ✅ Better type hints (IDE improvements only)
- ✅ Bug fixes (don't change behavior)
- ✅ Better error messages

This means:
- Your GraphQL schema definitions stay the same
- Your type implementations stay the same
- Your resolvers stay the same
- Your error handling stays the same

### Example: Your Code Works As-Is

```php
// v1.0.2 code - still works in v2.0.0!
class UserType extends ObjectType {
    public function build($config) {
        $config
            ->addField('id', IntType::class)
            ->addField('name', StringType::class)
            ->addField('email', StringType::class);
    }
}

// This works exactly the same in v2.0.0
$schema = new Schema([
    'query' => new QueryType(),
    'mutation' => new MutationType(),
]);

// Queries execute identically
$result = $schema->execute($query, $variables);
```

No changes needed!

---

## PHPUnit Dependency Update (For Contributors)

If you're running tests for a project that depends on GraphQL-PHP:

### What Changed?
- Upgraded from PHPUnit ^9.6 to ^10.5
- This is a dev dependency (doesn't affect end users)

### If You See PHPUnit Compatibility Issues

```bash
# Update your test dependencies
composer require --dev phpunit/phpunit:^10.5

# Run tests
./vendor/bin/phpunit
```

### Common Issues & Solutions

**Issue:** `Call to undefined method` in test framework
```
Solution: Update to PHPUnit 10.5 which has different assertion methods
Run: composer require --dev phpunit/phpunit:^10.5
```

**Issue:** `Prophecy\Exception` not found
```
Solution: Add prophecy integration for PHPUnit 10
Run: composer require --dev phpspec/prophecy-phpunit:^2.5
```

---

## Troubleshooting

### "Cannot upgrade Symfony - too many dependencies"

**Problem:** You have many packages that need specific Symfony versions

**Solution:** 
1. Use Symfony Flex recipes to guide the upgrade:
   ```bash
   composer update --interactive
   ```

2. Or upgrade step-by-step:
   ```bash
   composer require symfony/property-access:^6.4 --no-update
   composer require symfony/console:^6.4 --no-update
   # ... list all your Symfony packages ...
   composer update
   ```

3. Test thoroughly before committing

### "Tests fail after upgrade"

**Problem:** Tests break after upgrading

**Most Common Causes:**
1. Symfony 6+ removed deprecated features you were using
2. PHPUnit 10+ has different assertions
3. Your code uses deprecated PHP features

**Solution:**
1. Check the error messages carefully
2. Run `composer show` to verify versions
3. Check Symfony's upgrade guide: https://symfony.com/doc/current/setup/upgrade_major.html
4. For PHP: Run `php -d error_reporting=E_DEPRECATED your-script.php`

### "I need to stay on old Symfony for now"

**Option:** Don't upgrade to v2.0.0 yet

You can stay on v1.0.2 indefinitely. But plan to upgrade:
- Symfony 5.4 support ends in November 2026
- PHP 8.3 support ends in November 2026
- Consider scheduling a compatibility upgrade

---

## Verification Checklist

After upgrading, verify:

- [ ] `composer show | grep graphql` shows v2.0.0
- [ ] `composer show | grep symfony/property-access` shows ^6.4+
- [ ] All tests pass: `./vendor/bin/phpunit`
- [ ] No deprecation warnings: `php -d error_reporting=E_DEPRECATED`
- [ ] Static analysis passes (if you use PHPStan): `./vendor/bin/phpstan analyze src`
- [ ] Your application starts correctly
- [ ] Sample GraphQL queries execute successfully

---

## Getting Help

### Documentation
- **CHANGELOG.md** - Detailed change list
- **AGENTS.md** - Development and testing guide

### If Something Goes Wrong
1. Check the error message carefully
2. Review this guide's troubleshooting section
3. Create an issue on GitHub with:
   - Your PHP version
   - Your Symfony version
   - The exact error message
   - Steps to reproduce

---

## Timeline Summary

| Version | Release Date | End of Life | Status |
|---------|--------------|------------|--------|
| v1.0.2 | Before 2026 | TBD | Previous |
| v2.0.0 | 2026-03-09 | TBD | **Current** |

---

## Summary

- ✅ Upgrade is safe for Symfony 6.4+ users
- ✅ No code changes needed
- ✅ Better type hints and error messages included
- ⚠️ Symfony 2.8-5.4 users must upgrade Symfony first
- ✅ All 268 tests passing
- ✅ PHPStan level 1 clean

**Happy upgrading! 🎉**
