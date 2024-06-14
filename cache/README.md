# Redis cache for teentix.org

This folder is used to deploy and manage the caches tht support the teentix.org website.

As of 2022-07-19, Redis 6.2.6 is deployed via Fly.io using application and volumes in the Seattle region.

This PR has the details for how the caches were originally setup and configured. https://github.com/teentix/teentix-site/pull/121#issuecomment-1189886276

Editing the fly.{dev,staging,production}.toml files and/or the secrets is likely what you'll need to do to conduct maintenance on the caches (e.g., for upgrades).

## SSHing into the instances

```
make ssh-dev
make ssh-staging
make ssh-production
```

Once on an instance, use this to access the redis server:

```
REDISCLI_AUTH=$REDIS_PASSWORD redis-cli
```

## Deploy updates

To deploy a given cache for a given environment:

```
make deploy-dev
make deploy-staging
make deploy-production
```
