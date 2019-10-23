
# Release Command

This command creates the first stable release of a version.
It is the transformation from a prerelease version to a non prerelease version.

Usage: `versio release [<master>]`

<dl>
    <dt>Master</dt>
    <dd>The version that will be bumped on the master branch. (Only required if your release workflow will be from master directly to release.)</dd>
</dl>

## Example

```
$ versio release
> Release version "0.1.0" on branch "release/0.1".
> Bump version on branch "release/0.1" to "0.1.1-DEV".
```
