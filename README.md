# ZMS Messaging

[![pipeline status](https://gitlab.com/eappointment/zmsmessaging/badges/main/pipeline.svg)](https://gitlab.com/eappointment/zmsmessaging/-/commits/main)
[![coverage report](https://gitlab.com/eappointment/zmsmessaging/badges/main/coverage.svg)](https://eappointment.gitlab.io/zmsmessaging/_tests/coverage/index.html)

# ZMS HTTP messaging

Use this library to messaging email and notifications.

## Requirements

* PHP 7.3+

## Installation

The variable `$WEBROOT` represents the parent path to install the application.

```bash
    cd $WEBROOT
    git clone https://gitlab.com/eappointment/zmsmessaging.git
    cd zmsmessaging
    make live
    cp config.example.php config.php
```

## Development

For development, additional modules are required. Commits from a live environment require to ignore the pre-commit hooks.
For local development do

```bash
    ...
    make dev
    ...
```

## Configuration

Edit the `config.php` and add/change settings for accessing the API.

## Testing

To test application run the following command:

    bin/test

For a detailed project description, see https://gitlab.com/eappointment/eappointment
