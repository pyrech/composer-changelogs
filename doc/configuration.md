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

Currently, there is 2 configurable behaviors:

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

