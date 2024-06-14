# Stylesheets

The stylesheets are implemented in SASS (via `.scss`) and compiled as part of [the gulp build process](../../../gulpfile.js). 

## Organization

The stylesheets are broadly categorized according to the following:
- **[`/base`](base):** General classes shared across the site, related to e.g. typography, links, tables, and other core elements
- **[`/components`](components):** Classes associated with reusable components that are used across the site
  - _Note:_ These may also be related to specific third-party plugins
- **[`/layout`](layout):** Classes associated with the general layout and shared experience of every page on the site
- **[`/sections`](sections):** Classes that are specific to particular views, templates, or sections of the site

On top of that, the following files are critical to tying these together:
- **[`_settings.scss`](_settings.scss):** Overrides the default styles from [Foundation for Sites](https://get.foundation/sites.html)
- **[`_variables.scss`](_variables.scss):** Establishes shared variables to be reused across the styles, particular for establishing a common color palette and set of fonts
- **[`app.scss`](app.scss):** Includes all dependency stylesheets from the above files and folders, and is the target that is ultimately compiled by the build process
## Compiling

For testing locally, or preparing for the staging environment, simply run the following, which will kick of the Gulp build process for the SASS stylesheets:

```
npm run build styles
```

The GitHub Actions will _also_ run through the build process; nonetheless, the compiled files are currently still expected to be committed to the repository.  