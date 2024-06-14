# Templates

Templates in Craft CMS rely on [Symphony's Twig templating engine](https://twig.symfony.com/). though it's extended significantly by [Craft CMS-specific elements](https://craftcms.com/docs/4.x/dev/twig-primer.html), such as [filters](https://craftcms.com/docs/4.x/dev/filters.html), [tags](https://craftcms.com/docs/4.x/dev/tags.html), [functions](https://craftcms.com/docs/4.x/dev/functions.html), and [global variables](https://craftcms.com/docs/4.x/dev/global-variables.html). To work with CraftCMS, it's also important to understand its [element queries](https://craftcms.com/docs/4.x/element-queries.html), which allow database entries to be queried.

## Calendar
The [`calendar`](calendar), [`checkin`](checkin), and [`passes`](passes) templates all rely heavily on [Solspace's Calendar plugin](https://docs.solspace.com/craft/calendar/v4). To understand this, it's critical that you, at  minimum, understand the [`month`](https://docs.solspace.com/craft/calendar/v4/templates/queries/month/) and [`events`](https://docs.solspace.com/craft/calendar/v4/templates/queries/events/) queries, and the resulting [`month`](https://docs.solspace.com/craft/calendar/v4/templates/objects/month/) and [`event`](https://docs.solspace.com/craft/calendar/v4/templates/objects/event/) objects they respectively return. 

> ***Note:*** There are distinct challemges in working with Events due to the structure of TeenTix' calendars. For detailed information on querying Event data, including sample code, please review the [TeenTix Calendar documentation](calendar/README.md).

## Forms
Once [Sections, Entry Types, and Fields](../config/project) have been configured (see [`/config/project`](../config/project)), form templates can be introduced to [create](https://craftcms.com/knowledge-base/entry-form) or [modify](https://craftcms.com/knowledge-base/entry-form#editing-existing-entries) entries—be sure to review Craft CMS's [Controller Actions documentation](https://craftcms.com/docs/4.x/dev/controller-actions.html#making-requests), and especially the section on [the `entries/save-entry` action](https://craftcms.com/docs/4.x/dev/controller-actions.html#post-entries-save-entry). You can see two examples of this with the [Member Sign-Up Form](account/new.twig) and the [Event Check-In Form](checkin/event.twig).

In addition, for simpler needs, the TeenTix site uses the [Solspace Freeform plugin](https://docs.solspace.com/craft/freeform/v5/) ([settings](https://staging-admin.teentix.org/admin/freeform/settings/general?site=default), [admin](https://staging-admin.teentix.org/admin/freeform/forms?site=default), [submissions](https://staging-admin.teentix.org/admin/freeform/submissions?site=default&source=form%3A1)). Examples of this can be seen on the [Request Physical Pass](account/physical-pass.twig) form.

## Routing
Craft CMS offers [automatic routing](https://craftcms.com/docs/4.x/dev/#template-paths) based on template paths. In addition, Craft CMS supports [Yii Framework's Runtime Routing](https://www.yiiframework.com/doc/guide/2.0/en/runtime-routing) (see [`routes.php`](../config/routes.php)) or, less commonly, Craft CMS's [dynamic routing rules](https://craftcms.com/docs/4.x/routing.html#dynamic-routes) and [advanced routing rules](https://craftcms.com/docs/4.x/routing.html#advanced-routing-with-url-rules) (see [Routes](https://staging-admin.teentix.org/admin/settings/routes?site=default) in [the CraftCMS Settings](https://staging-admin.teentix.org/admin/settings/) and [`/config/project/routes`](../config/project/routes)).

## Assets
You can find the CSS and the JavaScript that these templates depend upon in the [`/assets`](../assets)—and, specifically, the [`/styles/scss`](../assets/styles/scss) and [`/scripts/js`](../assets/scripts/js) directories, respectively. In addition, there are some externally referenced dependencies loaded directly from the [`_layout`](_layout.twig).

## Resources
- [Symphony's Twig documentation](https://twig.symfony.com/)
- [Craft CMS's Twig introduction](https://craftcms.com/docs/4.x/dev/twig-primer.html)
  - [Filters](https://craftcms.com/docs/4.x/dev/filters.html)
  - [Tags](https://craftcms.com/docs/4.x/dev/tags.html)
  - [Functions](https://craftcms.com/docs/4.x/dev/functions.html)
  - [Global Variables](https://craftcms.com/docs/4.x/dev/global-variables.html)
  - [Element Queries](https://craftcms.com/docs/4.x/element-queries.html)
- [Solspace's Calendar plugin](https://docs.solspace.com/craft/calendar/v4)
  - [`month` query](https://docs.solspace.com/craft/calendar/v4/templates/queries/month/) and resulting [`month` object](https://docs.solspace.com/craft/calendar/v4/templates/objects/month/)
  - [`events` query](https://docs.solspace.com/craft/calendar/v4/templates/queries/events/) and resulting [`event` object](https://docs.solspace.com/craft/calendar/v4/templates/objects/event/)
  - [TeenTix Calendar Documentation](calendar/README.md)
- [Creating Custom Forms](https://craftcms.com/knowledge-base/entry-form)
  - [Creating Custom Sections, Entry Types, and Fields](../config/project) (see [`/config/project`](../config/project))
  - [Creating Entry Forms](https://craftcms.com/knowledge-base/entry-form)
  - [Editing Entry Forms](https://craftcms.com/knowledge-base/entry-form#editing-existing-entries) entries—be sure to review Craft CMS's [Controller Actions documentation](https://craftcms.com/docs/4.x/dev/controller-actions.html#making-requests), and especially the section on [the `entries/save-entry` action](https://craftcms.com/docs/4.x/dev/controller-actions.html#post-entries-save-entry)
    - See the [Member Sign-Up Form](account/new.twig) and the [Event Check-In Form](checkin/event.twig) forms
  - [Solspace's Freeform plugin](https://docs.solspace.com/craft/freeform/v5/) ([settings](https://staging-admin.teentix.org/admin/freeform/settings/general?site=default), [admin](https://staging-admin.teentix.org/admin/freeform/forms?site=default), [submissions](https://staging-admin.teentix.org/admin/freeform/submissions?site=default&source=form%3A1))
    - See the [Request Physical Pass](account/physical-pass.twig) Form
- [Craft CMS Routing](https://craftcms.com/docs/4.x/routing.html)
  - [Yii Framework Runtime Routing](https://www.yiiframework.com/doc/guide/2.0/en/runtime-routing) (see [`routes.php`](../config/routes.php))
  - [Craft CMS's Dynamic Routing](https://craftcms.com/docs/4.x/routing.html#dynamic-routes) (see [Routes](https://staging-admin.teentix.org/admin/settings/routes?site=default) in [the CraftCMS Settings](https://staging-admin.teentix.org/admin/settings/) and [`/config/project/routes`](../config/project/routes))
  - [Craft CMS Advanced Routing Rules](https://craftcms.com/docs/4.x/routing.html#advanced-routing-with-url-rules)
