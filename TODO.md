* Single script to process all hooks
* Symbolic link from enabled hooks to centralised script
* Check that hooks are installed
* post-merge script
  * composer install
* post-checkout script
  * composer install
* pre-commit script
  * linter
  * php-cs
  * php-cs-fixer
  * php-unit (not yet)
  * code compiling (not yet)
  * asset minification (not yet)
* commit-msg script