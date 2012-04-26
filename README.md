#PHP File Downloader#
**A simple PHP file downloader script, works based on existing files in a directory.**

##HOW TO USE##
Put your files in **releases** directory.
See the *configuration section* and customize it for your project.

To download the **latest release** of project use: **download.php?ver=latest**
And for the **custom version** use: **download.php?ver=VERSION_NUMBER**
*e.g.: download.php?ver=0.5*

###Note###
Your files should be named by a specific pattern to become understandable for the program, e.g. something like: 
*NAME_VERSION_BLAH.BLAH.XXX* or *NAME-VERSION.BLAH.BLAH.XXX* ...

In the examples above, the *underscore* or *dash* **before** the *Version number* was used as a **Version Separator** character, which is defined by **VER_SEP** to make understandable version number for the program.
So, there's no limit for choosing this seperator, but don't forget to change the code (line 8).