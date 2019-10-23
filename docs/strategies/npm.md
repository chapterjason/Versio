
# Npm Strategy

This strategy updates the version property in a `package.json` file.
It will use `yarn` if available, otherwise `npm` to set the version.

## Example

```json5
{
    "type": "npm",
    "options": {
        "directory": "./foo" // by default the current working directory
    }
}
```
