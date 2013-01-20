<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/** 
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 *
 * See http://code.google.com/p/minify/wiki/CustomSource for other ideas
 **/

return array(
    'js' => array('../js/jquery-1.7.2.min.js', '../js/jquery.address-1.4.min.js','../js/jquery.cycle.lite.js','../js/jquery.imageloader.min.js','../js/displayutils.js','../js/query.js','../js/insight.js'),
    'js-proj' => array('../js/query_projects.js'),
    'css' => array('../css/style.css', '../css/style_results.css','../css/style_insight.css'),
);
