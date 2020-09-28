# php-to-smashing
A couple of PHP functions to POST data to Smashing dashboards

Edit the file to configure an optional caching database.

Usage:

send_event takes a widget name and a JSON string.  

Set the force flag to skip the check for a match in the caching database.  This is required for widgets where you send idential JSON each time to trigger a certain behavior.

Set the bypass flag to skip insertion into the database at all.  This can be used if you send separate data and control messages to widgets and don't want to overwrite the cached data.

The get_cached_json function will allow you to pull data previously sent to the widget.  This is useful for retreiving historical series data and adding an element (such as with a time series graphing widget.
