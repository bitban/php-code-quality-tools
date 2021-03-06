# PHP Code Quality Tools

[![Latest Stable Version](https://poser.pugx.org/bitban/php-code-quality-tools/v/stable)](https://packagist.org/packages/bitban/php-code-quality-tools)
[![License](https://poser.pugx.org/bitban/php-code-quality-tools/license)](https://packagist.org/packages/bitban/php-code-quality-tools)
[![Build Status](https://travis-ci.org/bitban/php-code-quality-tools.svg?branch=master)](https://travis-ci.org/bitban/php-code-quality-tools)
[![Coverage Status](https://coveralls.io/repos/github/bitban/php-code-quality-tools/badge.svg?branch=master)](https://coveralls.io/github/bitban/php-code-quality-tools?branch=master)

These tools may be used to detect and fix some common errors before committing them to the code repository.

Check command reference section for more detailed information.

## Installation

If you have not done this yet, add bin directory to composer.json:

`composer config bin-dir bin/`

It will add something like this to your composer.json file:

```json
"config": {
    "bin-dir": "bin/"
}
```
For a better experience, we also suggest to configure post-install and post-update script:

```json
"scripts": {
    "post-install-cmd": [
        "Bitban\\PhpCodeQualityTools\\Composer\\ScriptHandler::checkHooks"
    ],
    "post-update-cmd": [
        "Bitban\\PhpCodeQualityTools\\Composer\\ScriptHandler::checkHooks"
    ]
}
```

Then, require new dev dependency:

`composer require --dev "bitban/php-code-quality-tools:dev-master"`

After that, a new command is available from project's root path:

`bin/php-cqtools`

## Command reference

### Check Git Hooks

Checks if Git hooks are installed for current project. If not, it gives a hint to install them, but does not take any action automatically.

`bin/php-cqtools hooks:check`

### Install Git Hooks

Installs Git hooks into .git/hooks/. If destination files already exist, the are backed up.

`bin/php-cqtools hooks:install`

Git hooks managed are:

* pre-commit
* post-checkout
* post-merge

#### pre-commit hook

It calls code:validate command.

`bin/php-cqtools code:validate --only-commited-files`

#### post-checkout hook

It checks whether composer.lock has changed or not. If so, launches `composer install` command.

`bin/php-cqtools hooks:post-checkout <prevCommit> <postCommit> [<projectPath>]`

`projectPath` argument sets path to be processed. Default value is current project base path.

#### post-merge hook

It has the same behaviour than post-checkout hook.

`bin/php-cqtools hooks:post-merge [<projectPath>]`

### Uninstall Git Hooks

Removes Git hooks from destination path. If backup files exist, they are restored.

`bin/php-cqtools hooks:uninstall`

### Fix Code Style

Fixes code style of files according to defined code style.

It may fix all project files or only files to be commited. This second option is very convinient to fix errors detected in pre-commit hook.

`bin/php-cqtools code:fix-codestyle [--dry-run] [--excluded-paths=EXCLUDED_PATHS] [--only-commited-files] [--custom-ruleset=CUSTOM_RULESET] [<projectPath>]`

`projectPath` argument sets path to be processed. Default value is current project base path. 

`--dry-run` option shows diffs without applying any changes to files.
`--excluded-paths` option sets some paths not to be processed.
`--only-commited-files` option processes only files scheduled to be commited.
`--custom-ruleset` option sets phpcs custom ruleset to be used instead of default one

### Validate Code

It performs several tasks:

* PHP files:
  * Check for syntax errors
  * Check that forbidden functions (i.e., var_dump(), empty()) are not commited
  * Check whether multibyte string functions are being used
  * Check that code complies defined code style recommendations (errors cannot be commited, warnings may be)
  * Check that variable usage is right (i.e., use undefined variables, parameters not used, etc.)
* JSON files:
  * Check for syntax errors
* Composer related files:
  * Check that composer.json is not commited without composer.lock (checked only with --only-commited-files modifier)

`bin/php-cqtools code:validate [--excluded-paths=EXCLUDED_PATHS] [--only-commited-files] [--custom-ruleset=CUSTOM_RULESET] [<projectPath>]`

`projectPath` argument sets path to be processed. Default value is current project base path.

`--excluded-paths` option sets some paths not to be processed.
`--only-commited-files` option processes only files scheduled to be commited.
`--custom-ruleset` option sets phpcs custom ruleset to be used instead of default one

## Show debug information

Shows debug information about:

* Validation/fix tools installed, such as phpcs, jsonlint, phpcbf
* Project basepath
* Git hooks path
* Git hooks source path
* Commited files
* Excluded paths
* Project files

`bin/php-cqtools debug:show [--show-full-paths] [<projectPath]`

`projectPath` argument sets path to be processed. Default value is current project base path.

`--show-full-paths` option enables full path file listing instead of relative path

This command, may be unavailable in any future versions of these tools, as it has been added for debug purposes.

## Run Bitban Lightweight tests

Runs Bitban's framework lightweight tests. It assumes that Bitban's Development VM is up and running, and it has test launcher script `run_light_tests.sh` installed. 

`bin/php-cqtools test:run --vmHost[=VMHOST] [<projectPath]`

`projectPath` argument sets path to be processed. Default value is current project base path.

`--vmHost=VMHOST` option sets Development VM hostname

This command has no use for projects not integrating Bitban's framework.

## References

Here are several interesting links with information (and inspiration) about this subject.

* https://carlosbuenosvinos.com/write-your-git-hooks-in-php-and-keep-them-under-git-control/
* https://www.sitepoint.com/writing-php-git-hooks-with-static-review/
* https://github.com/sdieunidou/php-git-hooks
* http://tech.zumba.com/2014/04/14/control-code-quality/
* https://www.sitepoint.com/8-must-have-php-quality-assurance-tools/
* http://phpqatools.org/
* https://github.com/illusori/PHP_Codesniffer-VariableAnalysis/
