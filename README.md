
# Versio

Git is awesome if you want to manage versions for your project, but how to manage the numbers?
There comes Versio into the game.

Versio helps you to create your releases.

## Installation

Download the latest phar and install it with:

```shell script
sudo mv versio.phar /usr/local/bin/versio
sudo chmod +x /usr/local/bin/versio
```

## Configuration

In every project you want to use Versio you have to create a `versio.json` file.
This file contains the actual version and contains configurations about the release workflow.
To create it automatically use the `init` command.

Always ensure that you start with the master and end somewhere with the release.

### Basic configuration

The basic configuration contains the most basic workflow you can have.

```json
{
    "version": "0.1.0-DEV",
    "workflow": {
        "transitions": {
            "master": [
                "release"
            ]
        }
    }
}
```

### Advanced configuration

This advanced configuration is some of the most advanced configurations you can have.

```json
{
    "version": "0.1.0-DEV",
    "workflow": {
        "places": [
            "alpha",
            "beta",
            "rc",
            "rtm"
        ],
        "transitions": {
            "master": [
                "alpha",
                "beta"
            ],
            "alpha": [
                "beta",
                "alpha"
            ],
            "beta": [
                "beta",
                "rc",
                "release"
            ],
            "rc": [
                "rc",
                "rtm",
                "release"
            ],
            "rtm": [
                "rtm",
                "release"
            ]
        }
    }
}
```

## Roadmap

- [ ] Website
- [ ] Documentation
- [ ] Free definable places
- [ ] Better git interactions
- [ ] Ensure that all branches are localy available. Currently its enough if you are `git fetch --all && git pull --all` before working with the command, or ensure that the master and all release branches localy available.
- [ ] Command tests. Currently not that easy cause of the git interactions.
- [ ] `validate` Command, to validate the versio file.
- [ ] As mentioned in the `WorkflowGenerator` ensure that every path can end in release.

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
    - [ ] Npm
