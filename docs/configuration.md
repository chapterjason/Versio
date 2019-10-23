
# Configuration

All configurations are saved in the `versio.json` file.

You want to introduce another prerelease step?
No problem, the versio file contains the workflow, that means the workflow is also versioned.

## Versio File

On root level the versio file contains the following keys:

- `version` - The actual version
- `strategies` - [Strategies](#strategies) where and how to set the version
- `workflow` - The [Release Workflow](#release-workflow)

### Strategies

Strategies allow you to update the version in other places, e.g. in a `package.json` file.

The following strategies are available:

- [Versio](strategies/versio.md)
- [Composer](strategies/composer.md)
- [Npm](strategies/npm.md)
- [Expression](strategies/expression.md)
- [Line](strategies/line.md)

### Release Workflow

The release workflow helps to ensure compliance with the release workflow.
All the `places` and `transitions` will be set in uppercase.

#### Places

Currently the following places are available:

- ALPHA
- BETA
- RC
- RTM

##### Example

```json
{
    "places": [
        "BETA"
    ]
}
```

#### Transitions

Always ensure that you start with the transition `MASTER` and tht you can end from all places to `RELEASE`

The simplest workflow you can have is to release directly from `master` without a prerelease.
This would look like this:

```json
{
    "transitions": {
        "MASTER": [
            "RELEASE"
        ]
    }
}
```

I you now want to use a beta phase and sometimes an alpha pase you can configure it like this:

```json5
{
    "transitions": {
        "MASTER": [
            "ALPHA",
            "BETA"
        ],
        "ALPHA": [
            "ALPHA", // Needed if you want to create more than one alpha
            "BETA"
        ],
        "BETA": [
            "BETA", // Needed if you want to create more than one beta
            "RELEASE"
        ]
    }
}
```
