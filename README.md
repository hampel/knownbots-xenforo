Known Bots for XenForo 2.x
==========================

This XenForo 2.x addon adds additional definitions for bot detection in sessions.

By [Simon Hampel](https://twitter.com/SimonHampel).

Requirements
------------

This addon requires PHP 7.0 or higher and works on XenForo 2.x

Usage
-----

When you look at Current Visitors, you'll see additional robots identified - also look at the "Robots" list on that page
 http://www.example.com/community/online/?type=robot
 
There is also a "Show list of Known Bots" menu in the admin area Tools section which shows the bots we have defined and
links to more information about them (where available)

A count of robots online is added to the Members online and Online statistics widgets. This can be disabled using the
options.

Privacy
-------

v3.0.0 adds new functionality to email the list of new bots detected automatically to an email address configurable by
the admin.

The default and recommended value is to email knownbots@hampel.io

Emails sent to this address will only be used for the purposes of identifying new bots to add to this addon.

Email addresses will NOT be sold or added to any marketing lists - not even ours.

If there are issues detected from the emails you are sending us, we may reply to establish communication - but that will
be on a case-by-case basis and only for the purposes of troubleshooting the operation of this addon.

You can check the information contained in the emails by changing the address temporarily to your own so that emails go
to you. If you still want us to process these emails - please feel free to forward them to the above address.
