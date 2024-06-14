# apex-redirect

This is a small application that redirects requests from the apex https://teentix.org to the https://www.teentix.org domain. 

It's deployed on fly.io as its own app.

## local testing

go run redirect.go

The redirect will listen on http://localhost:8080

E.g., 

```shell
curl -vs 'http://localhost:8080'
curl -vs 'http://localhost:8080/foo/bar?baz=unicorn#!rainbows'
```

## deployment

Pushing to the master branch will automatically deploy this to fly.io.

If you need to run a manual deployment from a local system:

```shell
make deploy-fly            # from this directory
make deploy-apex-redirect  # from the repo root
```
