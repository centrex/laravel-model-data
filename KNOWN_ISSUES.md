# Known Issues — laravel-model-data

_Last checked: 2026-08-02_

## Failing tests

No failing tests. `composer test:unit` (`pest -p`) runs 2 tests, 4 assertions, both pass. As with the other small polymorphic-storage packages in this monorepo, the suite is thin (`tests/ExampleTest.php` + `ArchTest.php`) — it does not exercise `HasModelData`/`Concerns/HasModelData.php` or the `Data` facade at all, which is why the bug below isn't caught.

`composer test` halts at `test:refacto` (rector `--dry-run` exits non-zero) before `test:lint`/`test:types`/`test:unit` run in the chain, so each step was run individually below.

**Likely real bug (not a source edit, just flagging it):** `src/Facades/Data.php::getFacadeAccessor()` returns `\Centrex\ModelData\Data::class`, but there is no `src/Data.php` in this package — only `src/Models/Data.php` (the Eloquent model). `src/ModelDataServiceProvider.php::register()` only binds a `'model-data'` string key in the container (`$this->app->singleton('model-data', fn (): Data => new Data())`, using `Centrex\ModelData\Models\Data`), never the `Centrex\ModelData\Data` class the facade asks for. Calling the `Data` facade (e.g. `Data::something()`) would throw `Target class [Centrex\ModelData\Data] does not exist.` This also explains the phpstan errors below ("Class Centrex\ModelData\Data not found").

## Style / static-analysis debt

- `vendor/bin/pint --test` — **4 files** flagged: `src/Concerns/HasModelData.php`, `src/Models/Data.php`, `src/ModelDataServiceProvider.php`, `database/migrations/2023_11_15_010000_create_model_data_table.php`. Run `composer lint` to apply.
- `vendor/bin/rector --dry-run` — **4 files** flagged, all `AddOverrideAttributeToOverriddenMethodsRector` (missing `#[\Override]`): `src/Facades/Data.php`, `src/ModelDataServiceProvider.php`, `src/Models/Data.php`, `tests/TestCase.php`. Run `composer refacto` to apply.
- `vendor/bin/phpstan analyse` — **13 errors**, all unbaselined (`phpstan-baseline.neon` is empty). Highlights:
  - `config/config.php:9,14`, `src/Events/DataUpdated.php:13,16,21` (x2), `src/Facades/Data.php:16` — all "Class `Centrex\ModelData\Data` not found", i.e. phpstan independently confirms the missing-class issue described above.
  - `src/Models/Data.php:35-37` — undefined properties `$model_type`, `$model_id`, `$data_type` accessed on the `Data` model.
  - `src/Models/Data.php:48` — "Part `$modelId` (mixed) of encapsed string cannot be cast to string."
  - `src/Models/Data.php:51` — `MorphTo` relation missing generic type params.
  - `database/migrations/2023_11_15_010000_create_model_data_table.php:11` — anonymous migration `up()` has no return type.

## TODO / FIXME markers

None found (`grep -rn "TODO\|FIXME" --include="*.php" src/ config/ database/`).

## Open GitHub issues

Not checked — the `gh` CLI is not installed in this environment.
