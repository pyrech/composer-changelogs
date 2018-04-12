# Plugin configuration

This plugin supports some configuration - for example to specify the gitlab
hosts it should detect.

## Location

Configuration can be setup by adding parameters in the `extra` section of your
`composer.json`.

```json
{
    "extra": {
        "composer-changelogs": {
            "{{ the configuration key }}": "{{ the configuration value }}",
        }
    }
}
```

This `composer-changelogs` extra config can be put either in your local
`composer.json` (the one of the project you are working on) or the global
one in your `.composer` home directory (like
`/home/{user}/.composer/composer.json` on Linux).

## Configuration available

The available configuration options are listed below:

### Gitlab hosts

Unlike Github or Bitbucket that have fixed domains, Gitlab instances are
self-hosted so there is no way to automatically detects that a domain
correspond to a Gitlab instance.

The `gitlab-hosts` option can be setup to inform the plugin about the hosts it
should consider as Gitlab instance.

```json
{
    "extra": {
        "composer-changelogs": {
            "gitlab-hosts": [
                "gitlab.my-company.org"
            ],
        }
    }
}
```

### Autocommit feature

See [the full documentation of this feature](autocommit.md).

### Post Update Priority

The option `post-update-priority` can set a custom event priority for
the composer `post-update-cmd` event. This will delay the changelog
printing and [autocommit feature](autocommit.md).

The default value is set to `-1`. The value must be a signed int.
A lower event priority also means it's run later.

This default behaviour ensures that you can run user defined
[composer scripts](https://getcomposer.org/doc/articles/scripts.md#command-events)
for the `post-update-cmd` event before.

```json
{
    "extra": {
        "composer-changelogs": {
            "post-update-priority": -1
        }
    }
}
```
