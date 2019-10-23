
# Line Strategy

This strategy replaces a line in spezified files.

## Example

```json5
{
    "type": "line",
    "options": {
        "directories": ["src"], // by default the current working directory ,
        "pattern": "Kernel.php", // required 
        "line": 5, // required
        "replacement": "    protected $versio = '{{VERSION}}';" // required
    }
}
```

In the replacement you can use [placeholder](placeholder.md).

Versio uses the Finder Component from Symfony to resolve files.
- The `directories` will used for the [`in`](https://symfony.com/doc/current/components/finder.html#location) method
- The `pattern` options will used for the [`name`](https://symfony.com/doc/current/components/finder.html#file-name) method
