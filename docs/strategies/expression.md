
# Expression Strategy

This strategy replaces by a regular expression.

## Example

```json5
{
    "type": "expression",
    "options": {
        "directories": ["src"], // by default the current working directory ,
        "pattern": "Kernel.php", // required 
        "expression": "versio = '{{SEMVER}}';", // required,
        "replacement": "versio = '{{VERSION}}';" // required
    }
}
```

The placeholder `{{SEMVER}}` is a bit special here, it will replaced by a Semver regular expression.

In the replacement you can use [placeholder](placeholder.md).

Versio uses the Finder Component from Symfony to resolve files.
- The `directories` will used for the [`in`](https://symfony.com/doc/current/components/finder.html#location) method
- The `pattern` options will used for the [`name`](https://symfony.com/doc/current/components/finder.html#file-name) method

