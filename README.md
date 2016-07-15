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

Checks if Git hooks are installed. If not, it gives a hint to install them, but does not take any action automatically.

`bin/php-cqtools hooks:check [<gitProjectPath>]`

If `gitProjectPath` is not set, it takes current working directory as default value.

### Install Git Hooks

Installs Git hooks into destination path. If destination path already exists, it is deleted and recreated.

`bin/php-cqtools hooks:install <hooksSourcePath> <hooksDestinationPath>`

Files (hooks) are copied from hooksSourcePath to hooksDestinationPath.

Git hooks managed are:

* pre-commit
* post-checkout
* post-merge

#### pre-commit hook

It calls code:validate command.

`bin/php-cqtools code:validate --only-commited-files <projectPath>`

#### post-checkout hook

It checks whether composer.lock has changed or not. If so, launches `composer install` command.

`bin/php-cqtools hooks:post-checkout <projectPath> <prevCommit> <postCommit>`

#### post-merge hook

It has the same behaviour than post-checkout hook.

`bin/php-cqtools hooks:post-merge <projectPath>`

### Fix Code Style

Fixes code style of files according to Bitban's code style.

It may fix all project files or only files to be commited. This second option is very convinient to fix errors detected in pre-commit hook.

`bin/php-cqtools code:fix-codestyle [--dry-run] [--excluded-paths] [--only-commited-files] <path>`

`--dry-run` option shows diffs without applying any changes to files.
`--excluded-paths` option sets some paths not to be processed.
`--only-commited-files` option processes only files scheduled to be commited.

### Validate Code

It performs several tasks:

* PHP files:
  * Check for syntax errors
  * Check that forbidden functions (i.e., var_dump(), empty()) are commited
  * Check that code complies Bitban's code style recommendations (errors cannot be commited, warnings may be)
* JSON files:
  * Check for syntax errors
* Composer related files:
  * Check that composer.json is not commited without composer.lock (checked only with --only-commited-files modifier)

`bin/php-cqtools code:validate [--excluded-paths] [--only-commited-files] <path>`

`--excluded-paths` option sets some paths not to be processed.
`--only-commited-files` option processes only files scheduled to be commited.

## References

Here are several interesting links with information (and inspiration) about this subject.

* https://carlosbuenosvinos.com/write-your-git-hooks-in-php-and-keep-them-under-git-control/
* https://www.sitepoint.com/writing-php-git-hooks-with-static-review/
* https://github.com/sdieunidou/php-git-hooks
* http://tech.zumba.com/2014/04/14/control-code-quality/
* https://www.sitepoint.com/8-must-have-php-quality-assurance-tools/
* http://phpqatools.org/
* https://github.com/illusori/PHP_Codesniffer-VariableAnalysis/
