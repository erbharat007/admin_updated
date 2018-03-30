<div class="footer">
	<div class="container">
		<div class="row">
			<div id="footer-copyright" class="col-md-6">
				&copy; 2016 Admin
			</div> <!-- /span6 -->
		</div> <!-- /row -->
	</div> <!-- /container -->
</div> <!-- /footer -->
<script type="text/javascript">
	$(document).ready(function(){
       $(".tabsmenu li").click(function(){
       	  var tabs = $(this).attr("data-id");
       	  // alert(tabs);
       	  $(".tabsmenu li").removeClass("active");
       	  $(this).addClass("active");
       	  $(".tab-section").removeClass("active");
       	  $("#"+tabs).addClass("active");
       });
	});
</script>
<!-- Placed Javascripts at the end of the document so the pages load faster -->
<script src="<?php echo SITE_URL;?>/js/bootstrap.min.js"></script>
<script src="<?php echo SITE_URL;?>/js/jquery.lightbox.min.js"></script>
<script src="<?php echo SITE_URL;?>/js/jquery.msgbox.min.js"></script>
<?php

?>
<script src="<?php echo SITE_URL;?>/js/jquery.flot.js"></script>
<script src="<?php echo SITE_URL;?>/js/jquery.flot.pie.js"></script>
<script src="<?php echo SITE_URL;?>/js/jquery.flot.resize.js"></script>
<script src="<?php echo SITE_URL;?>/js/Application.js"></script>
<script src="<?php echo SITE_URL;?>/js/area.js"></script>
<script src="<?php echo SITE_URL;?>/js/donut.js"></script>
<script src="<?php echo SITE_URL;?>/js/notifications.js"></script>
<?php

?>


</body>
</html>