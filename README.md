Problem
=======
 - Javascripts and stylesheets are too large because they are not compressed
 - Users & your bandwidth is wasted for useless metadata
 
Solution
========
 - file size reduction (10-97% size reduction)
 - combines all files (js+css) from a given folder

Usage
=====
Compress a single javascript or stylesheet file or a whole folder.

Usage:
    php minifier.php /public [options]
    php minifier.php /stylesheets/common.css [options]

Options are:
    -q, --quiet                      no output
    -p, --pretend                    no changes are made
    -r, --recursive                  execute the action on all subdirectories
    -js, --javascripts               compress only javascript files
    -css, --stylesheets              compress only stylesheets files
    -c, --combine                    combines all files in one


Example
=======
    php minifier.php /images
      minifing /public/common.js
      2887 -> 132                              = 4%

      minifing /public/stylesheets/all.css
      3136 -> 282                              = 8%

      minifing /javascripts/carrousel.js
      5045 -> 4                                = 0%
      ...

Author
======
[Javier Martinez Fernandez](http://ecentinela.com)  
ecentinela@gmail.com