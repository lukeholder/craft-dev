# TeenTix Databases

This folder is used to deploy and manage the databases that support the teentix.org website.

As of 2022-07-19, MySQL 5.7. MySQL is deployed via Fly.io using applications and volumes in the Seattle region.

This PR has the details for how the databases were originally setup and configured. https://github.com/teentix/teentix-site/pull/120#issuecomment-1189832384

Editing the fly.{dev,staging,production}.toml files and/or the secrets is likely what you'll need to do to conduct maintenance on the databases (e.g., for upgrades).

## SSHing into the instances

```
make ssh-dev
make ssh-staging
make ssh-production
```

## Deploy updates

To deploy a given db for a given environment:

```
make deploy-dev
make deploy-staging
make deploy-production
```
