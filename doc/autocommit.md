# Autocommit feature

> Note: This feature is considered as experimental. Use it carefully and
> report any problem with it.

As this feature was requested many times, composer-changelogs plugin now allows
you to automatically commit the `composer.lock` file after you ran your
`composer update` command.

Please read the full documentation below to enable and make use of this feature.

## Setup

By default, this feature is disabled. To enabled it, you just need to setup
some `extra` config in your composer.json:

```json
{
    "extra": {
        "composer-changelogs": {
            "commit-auto": "ask",
            "commit-bin-file": "path/to/bin",
            "commit-message": "First line of commit message"
        }
    }
}
```

> See [this documentation](configuration.md) for where to put your config.

## Options available

### commit-auto

This option can be one value between `never`, `ask` and `always`.

- `never` is the default option. It disable completly the autocommit feature.
- `ask` will propose you interactively to trigger a commit after each
`composer update` in case some dependencies were updated.
- `always` will trigger a commit everytime a `composer update` is ran and some
dependencies were updated.

### commit-bin-file

This option should contain the path of the script to execute to make the
"commit" (this allows the plugin to not be tight to any VCS). The path can be
either absolute or relative to the `composer.json` file containing the plugin
configuration.

The script will be called with two parameters:
- the first one is the location of the current working directory
- the second one is the path to the temp file containing the message commit
with the complete changelogs summary.

The plugin provides a default script to trigger a git commit. The script is
located in `bin/git-commit.sh`. Here is an example configuration to use it
directly (it supposes the `composer.json` containing the extra config is the
one requiring the plugin):

```json
{
    "extra": {
        "composer-changelogs": {
            "commit-auto": "ask",
            "commit-bin-file": "vendor/pyrech/composer-changelogs/bin/git-commit.sh"
        }
    }
}
```

### commit-message

This option can override the first line of the default commit message. If the
setting is omitted or the value is empty, the default message is used:
> Update dependencies
