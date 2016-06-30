# PHP Code Quality Tools - Roadmap

## Initial launch features

* <del>Single script to process all hooks</del>
* <del>Command to install hooks (copy rather than symlink)</del>
* <del>Check that hooks are installed</del>
* <del>post-merge script</del>
  * <del>composer install (refactor)</del>
* <del>post-checkout script</del>
  * <del>composer install (refactor)</del>
* pre-commit script
    * <del>PHP linter</del>
    * <del>Errors: var_dump()</del>
    * <del>Warnings: empty()</del>
    * <del>Detect not initialised variable usage (and other sniffs related to variables)</del>
    * <del>JSON linter</del>
    * Show all errors instead of stopping on first
  * <del>composer.json file commited alone</del>
  * <del>PSR-2 validation/del>
  * <del>PSR-2 fixer</del>
    * <del>dry run</del>

## Further improvements

* <del>validate given folder (appart from commited files)</del>
* improve and simplify installation procedure
* refactor code
* pre-commit script
  * php-unit
  * code compiling
  * asset minification
* commit-msg script
