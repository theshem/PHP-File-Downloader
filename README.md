File Downloader
===============
###A simple file downloader program, works based on existing files in &quot;released&quot; directory.###

download the **latest release** of project by:
**download.php?ver=latest**

and this one to download **custom version**:
**download.php?ver=VERSION_NUMBER**
*e.g.: download.php?ver=0.5*

##HOW TO USE##
Put your files in **releases** directory.
and that's it :D

###Note###
Your files should be named by a specific pattern to become understandable for the program, e.g. something like: 
*NAME_VERSION_BLAH.BLAH.XXX* or *NAME-VERSION.BLAH.BLAH.XXX* ...

In the examples above, the *underscore* or *dash* **before** the *Version number* was used as a **Version Separator**, witch is defined by **VER_SEP** to make understandable version number for the program.
So, there's no limit for choosing this seperator, but don't forget to change the code (line 8) and customize it for your projects.