<?php
  require("head.html");
  if (file_exists("under_maintenance_flag")) die("Le site est en maintenance... revenez plus tard!");
?> 
<link rel="stylesheet" href="menuderoulant.css" type="text/css" />
<script type="text/javascript">
  window.addEventListener("load",iphonefix);  
  function nullFunction() { }
  function iphonefix() {
    if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i))) {
      var fixes=document.getElementsByClassName("iphonefix");
      for (var i=0; i<fixes.length; i++) fixes[i].addEventListener("click", nullFunction);
    }
  }
</script>
