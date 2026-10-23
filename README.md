# IFI Ecosystem Intelligence Map

Laravel 13 and Filament 5 application for managing four EIM data layers and exploring approved records on a public dashboard. The sample records are synthetic and must not be interpreted as real conditions in Nigeria.

## Local setup

This checkout is linked in Herd at [http://if-dashboard.test](http://if-dashboard.test). Herd uses PHP 8.4 and Node 22.23.1 for this site. Redis is used for cache, sessions, and queued imports.

```sh
composer install
npm install
mysql -uroot -e "CREATE DATABASE if_dashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
php artisan migrate --seed
npm run build
```

Create a local panel user with `php artisan make:filament-user`, then sign in at [http://if-dashboard.test/admin](http://if-dashboard.test/admin). Run `php artisan queue:work --tries=3` in a terminal while processing CSV imports.

## CSV ingestion

The panel has one importer for each of the four evidence layers. Importers validate rows, keep each layer's `record_key` as an idempotent upsert key, and save imported rows as drafts. Review the import result and record details, then use the panel's Publish action to make a record public. The public dashboard only reads published rows. Imports do not accept a publication status column.

Starter CSVs are in `data/samples/`:

- `issues.csv`: `record_key`, `issue_area`, `indicator`, `state`, `geography_level`, `value`, `unit`, `population_group`, `disaggregation`, `period`, `data_type`, `source_name`, `source_url`, `source_reference`, `notes`.
- `interventions.csv`: `record_key`, `issue_area`, `state`, `geography_level`, `name`, `intervention_type`, `implementer`, `target_population`, `start_date`, `end_date`, `coverage_geography`, `implementation_status`, `description`, `source_name`, `source_url`, `source_reference`, `notes`.
- `resources.csv`: `record_key`, `issue_area`, `state`, `geography_level`, `programme`, `funder_mda`, `resource_category`, `funding_stage`, `amount`, `currency`, `allocation_year`, `period`, `source_name`, `source_url`, `source_reference`, `notes`.
- `laws-policies.csv`: `record_key`, `issue_area`, `state`, `geography_level`, `instrument_title`, `instrument_category`, `law_policy_family`, `jurisdiction`, `status_label`, `enactment_date`, `source_name`, `source_url`, `source_reference`, `notes`.
- `relationships.csv`: `issue_record_key`, `related_record_type`, `related_record_key`, `relationship_type`, `source_reference`. Upload issue, intervention, resource and law/policy records first, then import this mapping from Issue Records. `related_record_type` must be `intervention`, `resource`, or `law`. Each row links one issue record to one related record; repeat the issue key for additional links. Both keys must already exist. A relationship is idempotently updated by the issue/related-record pair, and its source reference is required.

Keep the source and period with every imported row. Use one currency per resource amount, and keep budget allocation, release, commitment, and expenditure as distinct stages. Blank optional values are allowed. Rows with the same `record_key` update the existing record; choose stable, unique keys for the source dataset. Do not infer direct relationships from matching issue area or state. Use the relationship CSV only when the record-level link and its source/reference are known. The relationship rows shipped with sample/reference-synthetic data are development examples, not verified links.

## Stack

- Laravel 13, PHP 8.3+
- Filament 5 / Livewire 4 / Alpine.js
- Tailwind CSS 4, Vite, Chart.js, AOS
- MySQL for application and test data; Redis for cache, sessions, and queues

The public dashboard has issue charts, a state coverage tile map, two-state comparisons, resource charts grouped by currency, and searchable record details. It is a state-level tile view, not a geographic boundary map.

Feature tests use a separate `if_dashboard_test` MySQL database. Create it before running `php artisan test`; test refreshes clear and rebuild tables in that database.
