learnstones-wp-02

Learnstones Wordpress Plugin v0.2 Incorporating http://wordpress.org/plugins/jquery-colorbox/
Raymond Francis, Packaged by Richard Drake

REQUIREMENTS
============ 
Wordpress 3.6.1 (probably still works with 3.5.2 and 3.6)
Wordpress Plugin: wp-markdown 1.4 (1.3 doesn't like iframes)

INSTRUCTIONS
============
1. copy plugin/learnstones folder into wordpress/wp-content/plugins
2. copy themes/twentytwelve/single-ls_lesson.php  into wordpress/wp-content/themes/twentytwelve

In Wordpress Admin Panel
3. Plugins/Installed Plugins/Activate Learnstones (Lessons should appear in the left panel under comments)
4. Appearance/Themes change theme to twentytwelve 
5. Settings/Writing/Markdown/Enable Markdown for Lessons

THATS IT!

USAGE
=====
Author lesson in Markdown. 
--- creates a new slide/page

Students can traffic light (RAG) their progress. This data is not persisted between sessions in this version.

ROAD MAP
========

UI: 
- Make the Lesson full screen
- disable escape key
- check on Win XP and smaller 1024x768 resolutions
 

Feedback:
- ability to inform teacher of your progress eg. via email form or client side db
