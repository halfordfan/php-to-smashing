# smashing-php-poster
A couple of PHP functions to POST data to Smashing dashboards

## Installation

Place the file in your ```jobs/``` directory.  Edit the file to configure your auth token and Smashing URL, as well as an optional caching database.  The caching database serves two purposes: storing historical widget data in case of a restart where history is valuable (such as a time-series graph), and reducing rendering of widgets that are frequently updated but don't change often.

Use the sample jobs files from [this gist](https://gist.github.com/halfordfan/7dea9c3c3f2293ac0125c8d4987cc37e) to write your own PHP jobs.

Usage:

```send_event``` takes a widget name and a JSON string.  

Set the ```force``` flag to skip the check for a match in the caching database.  This is required for widgets where you send idential JSON each time to trigger a certain behavior.

Set the ```bypass``` flag to skip insertion into the database at all.  This can be used if you send separate data and control messages to widgets and don't want to overwrite the cached data (see the leaflet with migration plugin I have for an example of this).

The ```get_cached_json``` function will allow you to pull data previously sent to the widget.  This is useful for retreiving historical series data and adding an element (such as with a time series graphing widget).
