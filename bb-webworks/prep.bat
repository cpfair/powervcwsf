@echo off
rem as opposed to lazy-loading the entire application live, this is what I run before deploying the BB app world...
rmdir /S /Q app\common
xcopy /S ..\img  app\common\img\
xcopy /S ..\js  app\common\js\
xcopy /S ..\css  app\common\css\
wget http://cwsf.cpfx.ca/queryui_projects.php -O app/proj_query.html