# Changelog

All notable changes to this project will be documented in this file.

---

## [v1.0.0] - 2025-07-13

🎉 First stable release of `filament-demo-generator`.

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