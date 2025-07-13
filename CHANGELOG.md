# Changelog

All notable changes to this project will be documented in this file.

---
## [v1.2.0] – 2025-07-13

This release introduces advanced field intelligence for generating more realistic demo data.

### Added
- Support for `field_keywords` to generate fake values based on field names.
- Support for `column_types` to customize generation logic per database type.
- Extended coverage of all known MySQL/Laravel column types in the config file.

### Breaking Changes
- None

---

## [v1.1.0] – 2025-07-13

This release adds full support for automatically generating demo data for `BelongsTo` relationships.

### Added
- Detects `BelongsTo` relationships using model reflection.
- Automatically fills foreign key fields with either:
  - Existing related records, or
  - New related records created dynamically.
- Introduced `generateRelatedModel()` to create related records using `fillable` fields.

### Improved
- Unified fake data generation by reusing the existing `generateFakeValue()` method.
- Works entirely with `fillable` attributes.
- Safer method reflection and model scanning with error handling.

### Breaking Changes
- None

---

## [v1.0.0] - 2025-07-13

First stable release of `filament-demo-generator`.

### Features

- Add `Generate Demo Data` action to any Filament resource
- Auto-detect model fillable fields and generate fake data
- Smart generators based on:
    - Field name keywords (e.g. `email`, `name`, `description`)
    - Column types (e.g. `string`, `integer`, `date`, `boolean`)
- Generate random date values from past or future
- Display modal with input for record count
- Config file for full customization:
    - Define your own field keyword rules
    - Define custom generators for column types

### Configuration

Publish the config file using the command below:

```bash
php artisan vendor:publish --tag=config --provider="Mgamal92\FilamentDemoGenerator\FilamentDemoGeneratorServiceProvider"
```

This will create:

```text
config/filament-demo-generator.php
```

---

> Next: Upcoming enhancements planned for `v1.1` include:
> - Support for replacing existing data
> - Language selector for Faker
> - Smart handling for foreign keys
