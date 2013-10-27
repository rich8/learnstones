learnstones-wp-02

Learnstones Wordpress Plugin v0.2 Incorporating http://wordpress.org/plugins/jquery-colorbox/
Raymond Francis, Packaged by Richard Drake

REQUIREMENTS
============ 
Wordpress 3.6.1 (probably still works with 3.5.2 and 3.6)
Wordpress Plugin: wp-markdown 1.4 (1.3 doesn't like iframes)

INSTRUCTIONS
============
in wordpress/wp-content 
1. git init
2. git pull https://github.com/rich8/learnstones-wp.git

In Wordpress Admin Panel
3. Plugins/Installed Plugins/Activate Learnstones (Lessons should appear in the left panel under comments)
4. Appearance/Themes change theme to twentytwelve 
5. Settings/Writing/Markdown/Enable Markdown for Lessons

THATS IT!

USAGE
=====
Author lesson in Markdown. For samples lessons, copy from learnstones.com (protype v0.1 lessons) using the edit button at the bottom left of the lesson screen

--- creates a new slide/page

Students can traffic light (RAG) their progress. This data is not persisted between sessions in this version.

ROAD MAP
========

UI: 
- Make the Lesson full screen, disable escape key, check on Win XP and smaller 1024x768 resolutions
- swipe not needed. Stones provide ample navigation

Feedback / Tracking Progress
- ability to inform teacher of your progress eg. via email form or client side db, present in dashboard

Social Learning
- Integrate the user-submited-posts plugin (doesn't work in colorbox pluing)

Shared Planning
- Integrate the post-forking plugin as collaboration platform (doesn't work on custome post types)

Open
- All lessons open. Anonymous users can work and login later
- Anonymous users can use user-submitted-post if teacher wishes (default is login required)
