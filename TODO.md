* <del>Single script to process all hooks</del>
* <del>Command to install hooks (copy rather than symlink)</del>
* <del>Check that hooks are installed</del>
* post-merge script
  * composer install (refactor)
* post-checkout script
  * composer install (refactor)
* pre-commit script
  * linter
    * Errors: var_dump()
    * Warnings: empty()
  * composer.json + composer.lock pack
  * php-cs
  * php-cs-fixer
  * php-unit (not yet)
  * code compiling (not yet)
  * asset minification (not yet)
* commit-msg script
