<script>
	var userId = "<?php echo $this->Session->read('Auth.User.Investor.investor_identity') ?>";
<?php //echo $this->Session->read('Auth.User.Investor'); ?>
console.log("userId = " + userId);	
	
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-90344434-1', 'auto');
	ga('send', 'pageview');
	ga('set', 'userId', userId); // Establezca el ID de usuario mediante el user_id con el que haya iniciado sesion.
 
	
// scroll detector
setTimeout(function(){var a=document.createElement("script");
var b=document.getElementsByTagName("script")[0]; a.src=document.location.protocol+"//script.crazyegg.com/pages/scripts/0018/6177.js?[1]"+Math.floor(new Date().getTime()/3600000);
a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)},1);

</script>