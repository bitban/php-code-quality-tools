# PHP Code Quality Tools

These tools may be used to detect and fix some common errors before committing them to the code repository.

Check command reference section for more detailed information.

## Installation

If you have not done this yet, add bin directory to composer.json:

```json
"config": {
    "bin-dir": "bin/"
},
```
For a better experience, we also suggest to configure post-install and post-update script:

```json
"scripts": {
    "post-install-cmd": [
        "Bitban\\PhpCodeQualityTools\\Composer\\ScriptHandler::checkSniffs",
        "Bitban\\PhpCodeQualityTools\\Composer\\ScriptHandler::checkHooks"
    ],
    "post-update-cmd": [
        "Bitban\\PhpCodeQualityTools\\Composer\\ScriptHandler::checkSniffs",
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

Git hooks managed are:

* pre-commit
* post-checkout
* post-merge

#### pre-commit hook

It performs several tasks:

* PHP files:
  * Check for syntax errors
  * Check that forbidden functions (i.e., var_dump(), empty()) are commited
  * Check that code complies PSR-2 style recommendations (errors cannot be commited, warnings may be)
* JSON files:
  * Check for syntax errors
* Composer related files:
  * Check that composer.json is not commited without composer.lock

#### post-checkout hook

It checks whether composer.lock has changed or not . If so, launches `composer install` command.

`bin/php-cqtools hooks:post-checkout <projectPath> <prevCommit> <postCommit>`

#### post-merge hook

It has the same behaviour than post-checkout hook.

`bin/php-cqtools hooks:post-merge <projectPath>`

### Fix Code Style (PSR-2)

Fixes code style of files according to PSR-2 recommendations.

It may fix all project files or only files to be commited. This second option is very convinient to fix errors detected in pre-commit hook.

`bin/php-cqtools code:fix-psr2 [--commited-files] <path>`

### Validate Code

Performs all code validations across files in given path.

`bin/php-cqtools code:validate [--commited-files] <path>`

## References

Here are several interesting links with information (and inspiration) about this subject.

* https://carlosbuenosvinos.com/write-your-git-hooks-in-php-and-keep-them-under-git-control/
* https://www.sitepoint.com/writing-php-git-hooks-with-static-review/
* https://github.com/sdieunidou/php-git-hooks
* http://tech.zumba.com/2014/04/14/control-code-quality/
* https://www.sitepoint.com/8-must-have-php-quality-assurance-tools/
* http://phpqatools.org/
* https://github.com/illusori/PHP_Codesniffer-VariableAnalysis/
