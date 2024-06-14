# teentix-site
TeenTix.org website built on Craft CMS.

# local development

We have docs for the platforms below. Each one is linked to setup steps.

* [Github Codespaces](https://github.com/teentix/docs/wiki/Developing-on-Github-Codespaces)
* [macOS](https://github.com/teentix/docs/wiki/Developing-on-macOS)
* [Windows using WSL](https://github.com/teentix/docs/wiki/Developing-on-Windows)
* [Ubuntu](https://github.com/teentix/docs/wiki/Developing-on-Ubuntu)

Other platforms will likely work, help us document them!

## get a copy of the staging db

Download a copy of the staging database (ask TVD or Michiko), and add it to: `docker/db_init_scripts`.

## running the site

To run the site:

```
make build-and-run-local
```

*Note:* the first takes a little while, because it populates the database.

Then open: http://localhost:4000

That's the main Seattle site.

The LA site is at: http://la.localhost:4000
The admin pane is at: http://admin.localhost:4000/admin


For now most changes require stopping and restarting the server, which is fairly quick.

## authentication to load page

Basic authentication is required for local development, as well as for the dev and staging environments. Use username and password:

* Username: `empowerteens`
* Password: (leave blank)

## admin authentication

Work with TVD or Michiko to get access to the http://localhost:4000/admin site. If you loaded the staging database, we'll provide a shared admin username and password.

## debugging with PHPStorm

See: https://github.com/teentix/docs/wiki/Debugging-with-PHPStorm

# development
For more guidance on developing for the TeenTix site, see additional documentation for:
- [CraftCMS Configuration](config/project/README.md)
- [Stylesheets](assets/styles/scss/README.md)
- [Templates](templates/README.md)
  - [Calendar](templates/calendar/README.md) 

# deployment

Deployments are managed with Github Actions. See: https://github.com/teentix/teentix-site/actions

Configuration for deployments is in the `.github/workflows` directory.

# teentix.org redirects

We redirect requests for `teentix.org` to `www.teentix.org`. The code to do that is in the `apex-redirect/` directory. The apex-redirect is deployed as part of the production Github Action.
