# Bolt Forms Extra Recipients Extension

An extension of bolt/forms that allows you to add extra recipients to forms.

Installation:

```bash
composer require bolt/forms-extra-recipients
```

## Setting up

Suppose your `contact` form has a `department` field like so:

```yaml
        department:
            type: choice
            options:
                required: true
                choices: { 'sales' : 'sales', 'accounts': 'accounts' }
```

Depending on what the user selects, different people will receive this form.

To do this, go to `config/extensions/bolt-boltformsextrarecipients.yaml` and put the following in your configuration:

# Reference extension configuration file

actions:
  send_contact_submissions:
    form: contact
    to:
      field:
        name: department
        values:
          sales: [ dan@arb.com, casey@arb.com, bob@twokings.nl, sammar@twokings.nl ]
          accounts: [ james@arb.com, anne@arb.com ]

Based on the value of the `department` field, the form will go _either_ to the people from Sales, or Accounts.

## Running PHPStan and Easy Codings Standard

First, make sure dependencies are installed:

```
COMPOSER_MEMORY_LIMIT=-1 composer update
```

And then run ECS:

```
vendor/bin/ecs check src
```
