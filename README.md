Power VCWSF
==========

This is a CWSF results search tool. Please note that this repo just includes the web GUI. I've pre-configured it to use a public, read-only account to access the same database the real thing runs on, but feel free to mirror the database locally to speed things up. MySQL configuration is in the common.php file.

Requirements
------------
 * A recent version of PHP (5.2ish? who knows, can't be bothered to check)
 * MySQL server, if you're not using the default one
 * Apache with mod_rewrite to allow "More details"/Insight dialogs to work. I'm a sucker for pretty URLs that the user never sees
 * A decent browser