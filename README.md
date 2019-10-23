
# Versio

Git is awesome if you want to manage versions for your project, but what is if you want to create a release?
You always have to set the version on several places, create new branches, tag the commits and bump the version on several places.
This can be annoying, so this is what Versio do for you.

Starting with the following history:

```
* Initial commit (HEAD -> master)
```

Now run the following command:

```
                       The type of version you want to release
                       |     The next type of version you are expecting
                       |     |     The next version on the master branch
                      \/    \/    \/
$ versio prerelease alpha beta minor
> Bump version on branch "master" to "0.2.0-DEV".
> Release version "0.1.0-ALPHA.1" on branch "release/0.1".
> Bump version on branch "release/0.1" to "0.1.0-BETA.1-DEV".
```

History now:

```
* Bump version to 0.1.0-BETA.1-DEV (HEAD -> release/0.1)
* Update version for 0.1.0-ALPHA.1 (tag: v0.1.0-BETA.1)
| * Bump version to 0.2.0-DEV (master)
|/
* Initial commit
```

Awesome!

## Installation

Download the latest [release](https://github.com/chapterjason/Versio/releases) phar and install it with:

```shell script
wget https://github.com/chapterjason/Versio/releases/download/v0.1.0-BETA.1/versio.phar
sudo mv versio.phar /usr/local/bin/versio
sudo chmod +x /usr/local/bin/versio
```

## Documentation

- [Configuration](docs/configuration.md)
- [Commands](docs/commands.md)

## Roadmap

- [ ] Website
- [x] Documentation
- [ ] Free definable places
- [ ] Better git interactions
- [ ] Ensure that all branches are localy available. Currently its enough if you are `git fetch --all && git pull --all` before working with the command, or ensure that the master and all release branches localy available.
- [ ] Command tests. Currently not that easy cause of the git interactions.
- [ ] `validate` Command, to validate the versio file.
- [ ] As mentioned in the `WorkflowGenerator` ensure that every path can end in release.
- [ ] json schema file

### 0.2.0

- [ ] Custom exceptions
- [ ] More detailed error messages
- [ ] Better command description and help texts
- [ ] Commit message format
- [ ] Strategy tests

### 0.1.0

- [x] `init` command. Allows to init the versio in a git project or init a new git before.
- [x] `get` command. Displays the current version.
- [x] Strategies.
    - [x] Versio
    - [x] Composer
    - [x] Expression
    - [x] Line
    - [x] Npm
