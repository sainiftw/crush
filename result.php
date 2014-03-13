<?php
error_reporting(0);
require_once 'config.php';
require_once 'facebook.php';

$fb = new Facebook(array(
				'appId' => $app_id,
				'secret' => $app_secret,
				'cookie' => true
));

$me = null;

$user = $fb->getUser();
?>

<style>
#img-res{margin-bottom: 20px; border:1px solid #ddd; -moz-border-radius:5px; -webkit-border-radius:5px; border-radius:5px; -moz-box-shadow:0 0 5px #7E7E7E; box-shadow:0 0 5px #7E7E7E;-webkit-box-shadow:0 0 5px #7E7E7E}
</style>

<script>
function post() {
alert("Published");
}
</script>

<center>


<!-- Begin Ad Call Tag - Do not Modify -->
<iframe width='728' height='90' frameborder='no' framespacing='0' scrolling='no' src='//ads.lfstmedia.com/slot/slot33038?ad_size=728x90&adkey=a7d'></iframe>
<!-- End of Ad Call Tag -->

<!-- Begin Ad Call Tag - Do not Modify -->
<iframe width='728' height='90' frameborder='no' framespacing='0' scrolling='no' src='//ads.lfstmedia.com/slot/slot33038?ad_size=728x90&adkey=a7d'></iframe>
<!-- End of Ad Call Tag -->

<img src="img/<?php echo $user; ?>.jpg" id="img-res" />
<a href="#" onclick="post()"><img src="images/share.png" /></a>

<!-- Begin Ad Call Tag - Do not Modify -->
<iframe width='728' height='90' frameborder='no' framespacing='0' scrolling='no' src='//ads.lfstmedia.com/slot/slot33038?ad_size=728x90&adkey=a7d'></iframe>
<!-- End of Ad Call Tag -->



<!-- Begin Ad Call Tag - Do not Modify -->
<iframe width='728' height='90' frameborder='no' framespacing='0' scrolling='no' src='//ads.lfstmedia.com/slot/slot33038?ad_size=728x90&adkey=a7d'></iframe>
<!-- End of Ad Call Tag -->

<br/>
</center>
