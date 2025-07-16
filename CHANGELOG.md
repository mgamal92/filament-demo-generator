# Changelog

All notable changes to this project will be documented in this file.

---

## [v1.4.0] – 2025-07-16

### Added – Image Field Support

- Image fields are now supported when generating demo data.
- Uses real image URLs from **Picsum Photos**
  ```text
  https://picsum.photos/seed/{field}-{uuid}/600/600
  ```
- Image field detection is based on a configurable list of keywords:
  ```php
  'image_fields' => ['image', 'avatar', 'logo', 'photo', 'thumbnail']
  ```
- New method `isImageField()` handles detection based on field name.
- Ensures unique, realistic images for every generated field.

### Configuration

If you haven't published the config file yet, do so with:

```bash
php artisan vendor:publish --tag=filament-demo-generator-config
```

Then update the following section to customize image field handling:

```php
'image_fields' => [
    'image', 'avatar', 'logo', 'photo', 'thumbnail',
],
```

---

## [v1.3.1] - 2025-07-14

### Refactoring & Internal Improvements

- Refactored `GenerateDemoDataAction` into smaller, focused classes:
  - `FakeValueGenerator`: Handles all fake data generation logic.
  - `ModelGenerator`: Handles on-the-fly related model creation.
  - `RelationResolver`: Extracts `BelongsTo` relationships.
- Removed redundant and unused internal methods.
- Improved code readability, structure, and maintainability.
- No breaking changes — functionality remains the same.

---

## [1.3.0] - 2025-07-13

### Added
- Smart delete: One button to generate or delete demo data (based on state)
- Caching of generated record IDs for tracking
- Event: `DemoDataGenerated` is fired after generation
- Support for `enum` columns (auto-pick random enum value)
- Confirmation modal for delete action
- Dynamic label, color, and form visibility based on state

### Changed
- Refactored action logic to handle both generate and delete in one button

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
