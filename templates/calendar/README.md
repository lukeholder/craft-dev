# Event Query Patterns

The `calendar`, and [`checkin`](../checkin) templates all rely heavily on [Solspace's Calendar plugin](https://docs.solspace.com/craft/calendar/v4). To understand these sections, it's critical that you, at  minimum, understand the [`month`](https://docs.solspace.com/craft/calendar/v4/templates/queries/month/) and [`events`](https://docs.solspace.com/craft/calendar/v4/templates/queries/events/) queries, and the resulting [`month`](https://docs.solspace.com/craft/calendar/v4/templates/objects/month/) and [`event`](https://docs.solspace.com/craft/calendar/v4/templates/objects/event/) objects they respectively return. 

> ***Note:*** A unique challenge of TeenTix's implementation is that there are two calendars for each site: 
> - Events ([admin](https://staging-admin.teentix.org/admin/calendar/events?site=default&source=calendar%3A1)) 
> - Events Additional Time ([admin](https://staging-admin.teentix.org/admin/calendar/events?site=default&source=calendar%3A2))
>
> The former has all content associated with the Event, including a full set of metadata such as related Genres and Categories; the latter only has additional scheduling information, alongside a reference to the Partner and Venue and the original ("parent") Event. When querying the full calendar, you will retrieve both types back. They will have different `event.id`s, and may even have different `event.slug`s. 
>
> There are different strategies for handling this, which will be explored below—but which can also be explored in [`month`](month.twig) and [`upcoming-listings`](_partials/upcoming-listings.twig) files.

## Working with Dates
Before querying and working with the results of the Solspace Calendar, it's important to understand how the plugin handles dates. 

While Craft CMS stores dates as UTC in the database, it always assumes they are being entered according to Craft CMS's configured Time Zone, and converts them accordingly ([reference](https://craftcms.com/docs/4.x/date-time-fields.html#querying-elements-with-date-fields)). Then, when it returns the dates, it returns them as the configured Time Zone. 

For TeenTix, Craft CMS is configured to display times as PST/PDT. When a date is entered into the Control Panel, it is assumed to be PST/PDT. And when it is returned to Twig templates, it is returned as PST/PDT. The date is saved in the database as UTC, but admins and developers never need to know that.

By contrast, dates in Solspace Calendar are saved and returned as UTC, _independent of what Craft's Time Zone is set to_ ([reference](https://docs.solspace.com/craft/calendar/v4/setup/faq/#how-does-calendar-handle-timezones)). What this means is that if you apply Craft's [`|date`](https://craftcms.com/docs/4.x/dev/filters.html#date) or [`|datetime`](https://craftcms.com/docs/4.x/dev/filters.html#datetime) filter, it will convert them from UTC to the local time. Since the dates are being entered into the Control Panel based on the local time, this will display the incorrect time. E.g., if an event starts at 7:00PM PST, and an admin adds an event for 7:00PM, it will actually save that as 7:00PM UTC, and return it as 7:00PM UTC. If you apply the `|datetime` filter, it will display it as either 11:00AM or 12:00PM, depending on daylight savings time. 

There are two ways of fixing this. The first is to use PHP's `.format()` ([reference](https://www.php.net/manual/en/datetime.format.php)), just working with the date "as is". The second is to use Solspace's (underdocumented) `startDateLocalized` property ([reference](https://docs.solspace.com/craft/calendar/v4/setup/translating/)), which will return the originally entered date as though it were PST/PDT, thus maintaining compatibility with other Craft CMS dates.

## Setting Parameters
In most cases, you will want to filter Events—perhaps by Site, by relationship, by a limit on the number of results, or perhaps all of these. The following show how to do this.

### Filtering by Site
Unless the listing is going to be filtered by a Venue or Partner, you’ll likely want to filter the Events by the local calendars. This parameter can be set using the following:

```twig
{# SET CALENDAR BY SITE #}
{% set sitePrefix               = currentSite.primary? "" : currentSite.handle ~ "_" %}
{% set calendars                = [sitePrefix ~ "events", sitePrefix ~ "eventsAdditional"] %}
```

### Filtering by Relationship Parameters
If you need to filter by relationships—such as Event, Genre, Category, Partner, or Venue—you can do that via the `relatedTo` parameter. To collect the values for these filters, you can use the following:

```twig
{% set searchParam              = craft.app.request.getQueryParam('param') %}
{% set relationParam            = ['and'] %}
{% if searchParam %}
  {% set relationParam          = relationParam|merge([{ targetElement: searchPartner }]) %}
{% endif %}
```

> ***Note:*** The `|merge` filter will merge one array with another array, so this can be used multiple times with multiple parameters, as appropriate. 

### Limiting Results
If you’d like to limit the results returned for any one Event, you can set a `limit` variable and then pass it to the `occurrences` argument when querying:

```twig
{% set limit                    = 5 %}
```

## Querying Events
Once your parameters are configured, you can use the following to query all Events in a month:

```twig
{# SET CALENDAR VARIABLES #}
{% set month                    = craft.calendar.month({
  date                          : “month”,
  relatedTo                     : relationParam|filter|length > 1? relationParam : null,
  loadOccurrences               : limit+1,
  calendar                      : calendars
}) %}
```

Or, if you’d like to query all Events without them being grouped by month, week, and day:

```twig
{% set event                    = craft.calendar.events({
  rangeStart                    : "today"
  relatedTo                     : relationParam|filter|length > 1? relationParam : null,
  loadOccurrences               : limit+1,
  calendar                      : calendars
}).all() %}
```

> ***Note:*** The `relatedTo` condition acknowledges that there may not be any filters. If the `relatedTo` argument gets an array that looks like `[“and”, “”]` it will return no results, so this prevents that scenario.

> ***Note:*** We add one to the `limit` variable so that we can easily detect if there are additional Events. This then allows us to provide messaging around that; e.g., a “Load more events…” link. This is shown below, when displaying results.

### Cross-Referencing Calendars
If you’d like to filter Events by parameters only available on the Events calendar—such as Genre or Category—but include additional showtimes, you can combine a query of each calendar:

```twig
{% set events                   = craft.calendar.events({
  relatedTo                     : relationParam|filter|length > 1? relationParam : null,
  rangeStart                    : "today",
  calendar                      : calendars
}).all() %}
{% set relatedEvents            = craft.calendar.events({
  relatedTo                     : events|column(“id”),
  rangeStart                    : "today"
}).all() %}
{% set datearray                = events|merge(related) %}
{% set alldates                 = datearray|supersort('sortAs', '{{ object.startDate }}') %}
{% set alldates                 = alldates|slice(0, 30) %}
```

> ***Note:*** We could explicitly limit the `calendar` to just the Events calendar. But if the `relationParam` is limited to the Events calendar, this will be implicit. 

> ***Note:*** In this case, we don’t know how many Events will be loaded from each calendar, or what their time distribution is, so we must first download them, then sort them, then limit the number of Events displayed. 

## Displaying Results
To display the results from the above queries, we can use the following logic:

```twig
{% for showtime in events %}
  {% if loop.index0 < limit or loop.length == limit %}
    <strong>{{showtime.startDate.format("D, M j, Y")}}</strong>
      {% if showtime.allDay %}
        All day
      {% else %}
        {{showtime.startDate.format("g:ia")}}
      {% endif %}
      at <a href="{{ showtime.eventVenue.one().url }}">{{ showtime.eventVenue.one().title }}</a>
  {% elseif loop.index0 == limit %}
    …and {{ showtime.occurrences|length-limit }} more
  {% endif %}
{% endfor %}
```

> ***Note:*** If we’re working with a `month` or `week`query, we either need to call the `.events` property to get an array of Events—or we need to loop through the `week` and/or `day` properties to get to the Events. See `month.twig` as an example.

> ***Note:*** The condition regarding the `loop.index0` is only needed if we want to limit the number of showtimes displayed—while simultaneously giving an indication of the number remaining. If we aren’t using the `limit` variable with the `loadOccurrences` parameter (see above) this isn’t necessary.

## Event Grouping
If you’d like showtimes to be grouped by each Event, then you can do the following:

```twig
{% set events                   = events|group(e => e.eventRelated? e.eventRelated.one().id : e.id) %}
```

In this construction, we’re grouping by the `id` of the primary Event, which we determine based on the calendar. If it’s the Events calendar (`1`) then we just use the Event’s `id`, otherwise we get the first Event that it’s related to via the `eventRelated` field.

> ***Note:*** An Additional Event Time _must_ be related to one (and _only_ one Event. That said, Craft CMS exposes all relationships as an array, and so we need to use the `|first` filter to get a reference to that one Event.

> ***Note:*** We use the condition around `calendar.eventRelated` so that we’re always grouping by the same “root” Event, regardless of whether or not the Event is from the Events calendar or the Additional Event Time calendar. This helps normalize the two sources. We’ll revisit this below, when displaying grouped results.

### Displaying Grouped Results
To display grouped results, we must use a different constriction:

```twig
{% for eventId, showtimes in events %}

  {% set event = showtimes|first %}
  {% set event = event.id == eventId? event : event.eventRelated|first %}

  <a href="{{ siteUrl }}calendar/event/{{ event.slug }}">{{ event.title }}</a>

  {% for showtime in showtimes %}
    …see previous template…
  {% endfor %}
{% endfor %}
```

> ***Note:*** It’s not possible to group by an Event object, so we much group by e.g. the `event.id` instead, and then look up the corresponding Event. We _could_ do a query to grab the Event object, but we can easily infer that from the first showtime—even if it’s from the Additional Event Time calendar (via the `eventRelated|first` filter). 

## Reference

- [Solspace's Calendar plugin](https://docs.solspace.com/craft/calendar/v4)
  - [`month` query](https://docs.solspace.com/craft/calendar/v4/templates/queries/month/) and resulting [`month` object](https://docs.solspace.com/craft/calendar/v4/templates/objects/month/)
  - [`events` query](https://docs.solspace.com/craft/calendar/v4/templates/queries/events/) and resulting [`event` object](https://docs.solspace.com/craft/calendar/v4/templates/objects/event/)