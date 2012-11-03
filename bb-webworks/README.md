Power VCWSF PlayBook App
==========

To compile this, you'll need the BlackBerry WebWorks SDK, and either a) the Ripple emulator for Chrome b) a real playbook to run it on (+ appropriate signing keys). Before the first time you'll need to run prep.bat to sync all the shared code from the main site into the app/common/ folder.

All API calls are performed against http://cwsf.cpfx.ca, as defined at the end of js/query.js. This is also the location where participant images are loaded from.
