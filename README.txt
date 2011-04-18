PeoplePods, version 0.9

PeoplePods is an SDK for creating modern web applications where many people
come together to meet, talk, share, read, work, publish and explore.  Every
effort has been made to NOT pigeon-hole you as a developer into one model of
a site over another.  PeoplePods is not a blog, it is not a social network,
and it is not a content management system.  Rather, PeoplePods is a set of tools
that can be combined in many ways to create all sorts of interactions, while
providing a simple and unified software interface.

For information about PeoplePods, in-depth documentation about the SDK, and
tools to help you run a successful site, visit PeoplePods.net!

http://peoplepods.net

PeoplePods is a product of XOXCO, Inc.  XOXCO is a clickable research and 
development company. We help people design and build web products and social 
applications.  We are available to help you customize PeoplePods for your needs!

http://xoxco.com

*************************************************
** REQUIREMENTS

PHP 5+
MySQL 4+
Level 9
10,000 HP


*************************************************
** LICENSE

PeoplePods is released under the MIT open source license.  

Please refer to LICENSE.txt

*************************************************
** PACKAGE CONTENTS

peoplepods/
	PeoplePods.php	- PHP include file	
	README.txt		- This file
	LICENSE.txt		- License information
	INSTALL.txt		- Installation instructions
	admin/ 			- PeoplePods Command Center
	files/			- Stub directories for cache and file uploads
	install/		- Installation script
	js/				- Javascript libraries
	lib/			- PeoplePods SDK files
	pods/			- Core Pods
	themes/			- Default themes
		default/	- Default front end theme
		admin/		- Default Command Center theme


*************************************************
** INSTALLING PEOPLEPODS

Please refer to INSTALL.txt or view online at:

http://peoplepods.net/readme/installing-peoplepods


*************************************************
** UPGRADING PEOPLEPODS

1) To be extra safe, first make a backup of your entire /peoplepods directory

2) Then, make a copy of peoplepods/lib/etc/options.php.  You will need this file
after you do the update.  Also, make sure to copy any custom pods and themes you've created,
as well as any pods or themes you've modified.

3) Download the latest version of PeoplePods from http://peoplepods.net/version

4) Upload the .tar.gz file to the directory that currently contains the peoplepods/ folder

5) Un-tar this file by running this command:

> tar -zxvf peoplepods-0.9.tar.gz

This will overwrite all of your existing PeoplePods files.  

6) Move the copy of peoplepods/lib/etc/options.php that you made back into peoplepods/lib/etc/

7) Visit the command center.  If schema changes are necessary, a message will be displayed!

Voila!  


*************************************************
** RELEASE NOTES

Download the latest version of PeoplePods at http://peoplepods.net/version

v0.9
April 15, 2011

View these notes online: http://peoplepods.net/version/09

Thanks to Damien Bell, Paul Armand, and all the contributors on PeoplePods.net for help with this release.

NEW FEATURES

* The default theme is now valid HTML5, and features vastly improved markup and javascript!

* Tags may now be applied to users, groups, comments, and files

* Tags now have a "weight" value which can be used to add information about the importance of the tag.

* Stack results may now optionally be cached.  Caching is off by default.  All other objects have improved caching.

* Plugin pods have several new capabilities, including:
  - Ability to specify changes to the schema
  - Ability to specify a function called upon installation of the pod
  - Ability to specify a function called upon the uninstallation of the pod
  - Ability to specify a function to process fields upon select and insert into the database
  - Specify a token-replacement pattern for generating the permalinks of content by type

* The core_api_simple pod has been completely rewritten and now offers many more API end points.
  - The first version is still available

* The PeoplePods object no longer requires authentication details to be passed in as a parameter.  The standard pp_auth cookie will be used in the absence of an explicit parameter.

BUG FIXES

* The query generation code in the Obj class has been vastly improved and now properly handles subordinate clauses

* The install process is now more robust.  All default pods will be turned on during the install, instead of requiring an admin to do it post-install.

* Comments in foreign language alphabets should now be saved and displayed properly

* Enabling and disabling pods is now handled 

* Rich text fields are now off by default in the admin interface

* $POD->formatOutput now attempts to detect html in the content and make smart decisions about formatting


*************************************************

v0.81
November 24, 2010

View these notes online: http://peoplepods.net/version/081

Just in time for Thanksgiving, here's another release of PeoplePods! Though this is a minor release of PeoplePods, it does bring some substantial new functionality for developers.

Thanks to Damien Bell, Gabe Hayes, Karl Fogel and James Harris for their contributions to this release!

NEW FEATURES

A new messaging class has been added! The mutant child of private messaging and activity streams, Alerts allows you to trigger automated alerts that are sent to users. Alerts are displayed until a specific action has been taken, or the user dismisses the alert.

In order to manage all of these messaging tools, a new Command Center tool has been added to manage some of the built in alerts, activity stream posts and emails that are sent from within the core libraries.

The Person object has gained a 'stub' field that behaves just like the Content stub field. It also now has a 'fullname' field for those sites that require more detailed information about their users.

You can now attach comments directly to a Person object using the $Person->addComment() method. You can get a list of comments attached to a person using $Person->comments().

You can now attach files directly to a Group Object using the $Group->addFile() method. You can get a list of files attached to a group using $Group->files().

Finally, developers may now override some of the key functions in the core libraries to customize the behavior of PeoplePods. This new ability allows developers to do things like create custom permissions, change the way the caching system works, use a different templating system, use a different email system, and change the way permalinks are generated for all of the objects. For a full list of the functions that can be overwritten, and some instructions on how to override them, read this!

BUG FIXES

The $POD->sanitizeInput() function which is called to strip user input of malacious code has been updated to allow for tags related to video embeds, as well as to allow the PRE tag.

A thousand tiny fixes to a thousand sloppy associative array calls have been fixed, so your error_log should be DRASTICALLY calmer during normal operation.

*************************************************

v0.8
Dec 10, 2010

View these notes online: http://peoplepods.net/version/08

Version 0.8 is a significant upgrade from 0.7, including numerous bug fixes and feature additions.
The most notable features are the redesigned command center tools, an upgraded and expanded
plugin system, and the inclusion of three new pods: OpenId, Facebook and Twitter connect.

Additionally, some changes were made to the SDK:

* Command center admin tools now feature tools for comment and flag management, WYSIWYG editing of content, and improved image placement options

* Items output via a $stack->output function now carry more information about their position in the stack,
including the listCount and isNthItem variables.  http://peoplepods.net/readme/stack-output

* $Obj->flagDate() can now be used to find the date an object was flagged. http://peoplepods.net/readme/obj-flagdate

* File objects now include a field called local_file which is the path to the original file on the web server. http://peoplepods.net/readme/file-object

* File fields original_name and file_name are now 60 characters long.

* $Obj->hasFlag() and $Obj->removeFlag() no longer require the $person parameter.  http://peoplepods.net/readme/obj-hasflag http://peoplepods.net/readme/obj-removeflag

* lib/etc/options.php was removed from the tarball to decrease problems with upgrading

* Unit tests for most of the objects and most of the functions have been added in the peoplepods/tests folder.

* A new class for creating activity streams has been added.  http://peoplepods.net/readme/activity

* Plugin pods may now specify a file of additional methods, and a settings function.  The registerPOD function now takes 2 additional parameters. http://peoplepods.net/readme/creating-new-pods

* Plugin pod may now add methods to all of the core classes using the registerMethod function.  http://peoplepods.net/readme/registerMethod

* Plugin pods may now include template files which will be used if a matching file is not found in the theme.

*************************************************

v0.71

View these notes online: http://peoplepods.net/version/071

* This version includes one small bug fix which fixes a problem with the private message inbox.

*************************************************

v0.7

View these notes online: http://peoplepods.net/version/07

* Editing and accessing object data no longer requires ->get and ->set because now there are magic methods to do it for you
* Rewritten query engine allows querying of multiple meta fields and related content
* Meta fields can now be added to an object before it is saved
* Completely rewritten save function
* New $file->src() method for dynamically resizing images
* Files can be browsed and managed via command center
* Flags can be browsed and managed via command center
* There is a new tag cloud sidebar included with the default theme
* New $POD->getFiles() method allows querying of files

*************************************************

v0.667
Nov 03, 2009

View these notes online: http://peoplepods.net/version/667

* Fixed bug in install where meta table was created with an invalid enum field
* Fixed bug in content and people admin tools where files uploaded were not handled properly
* Fixed bug in core_friends module where incorrect permission was being checked
* Fixed bug in core_usercontent that caused /edit to overlap with /editprofile
* Fixed bug where empty document returned false on ->success()

*************************************************

v 0.666
Oct 31, 2009
DEVELOPER PREVIEW

View these notes online: http://peoplepods.net/version/666

This is the first release of PeoplePods!  I've decided to call this a developer
preview because some of the SDK is still under-documented, and because there may
be rapid fixes rolling out over the first few weeks that I wouldn't want any
non-experts dealing with.  That said, thank you for downloading PeoplePods!

The intent of PeoplePods is not to provide an "off the shelf" social network.  The
core pods and default theme are provided as examples of the capabilities of the SDK,
and should not be used to run your final site.  While some of the pods can be used without
modification, you will definitely want to go into more depth than a simple CSS skin.

That said, they are absolutely intended to be used as the basis for your own work:
cut-and-paste to your hearts content! 

I recommend that you do not modify the Pod and Theme files that came with PeoplePods.
Instead, MAKE COPIES, and modify the copies.   This way, future releases will not
overwrite your changes.

Please send us feedback about your experience with PeoplePods on the forum at our website.


*************************************************
** COMING SOON

* Support for Memcached and other caching types
* Improved tag and comment objects
* Support for Gravatars

Keep up with new releases and information at our blog:
http://peoplepods.net/news

*************************************************
** CONTACT US

Found a bug?  Got a patch?  Want to share the pod or theme you made?  
Visit our forum or send us an email.

http://peoplepods.net/forum
info@peoplepods.net

Need help using PeoplePods to build your site?  
XOXCO, Inc, the creators of PeoplePods, can help!

http://xoxco.com/
info@xoxco.com

