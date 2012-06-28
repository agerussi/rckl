<?php
  require("head.html")
?> 
  <style type="text/css">
    .fixe { position:relative;
      background-color:#DDDDDD;
  } 
    .menuinline { padding:0; margin:0; 
      font-size:11pt; 
      font-family: sans-serif;
	display:block;
	
    }
    .menuinline li { text-align:left; list-style-type:none;} 
    .menuinline>li { display:inline; white-space:nowrap; margin:0 3px; padding:2px; }
    .menuinline ul { display:none; position:absolute; left:0%; top:100%; padding:0px; margin:0; }
    .menuinline li ul li { background-color:#DDDDDD; padding:2px;}
    .menuinline li:hover ul { display:block; } 
    .menuinline li:hover { position:relative; background-color:#FFFFFF; border-radius:8px;}
    .menuinline a { color:black; text-decoration:none; }
    .menuinline a:hover { color:blue; }
  </style>
