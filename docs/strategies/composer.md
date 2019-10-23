
# Composer Strategy

This strategy updates the version property in a `comoser.json` file.
You can leave this strategy out, if you are using packagist. (For futher information [The composer.json Schema - Properties - version](https://getcomposer.org/doc/04-schema.md#version))

## Example

```json5
{
    "type": "composer",
    "options": {
        "directory": "./foo" // by default the current working directory
    }
}
```
