
# Prerelease Command

This command releases a prerelease.

Usage: `version prerelease <type> <next> [<master>]`

<dl>
  <dt>Type</dt>
  <dd>The prerelease type you want to release.</dd>
  <dt>Next</dt>
  <dd>The next type of prerelease you want to release</dd>
  <dt>Master</dt>
  <dd>This is the version you want to bump on the master branch (minor or major). Only required if you are on the master branch and want to release your first prerelease of a version.</dd>
</dl>

## Example

```
$ versio prerelease alpha beta minor
> Bump version on branch "master" to "0.2.0-DEV".
> Release version "0.1.0-ALPHA.1" on branch "release/0.1".
> Bump version on branch "release/0.1" to "0.1.0-BETA.1-DEV".
```
