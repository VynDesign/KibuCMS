<!DOCTYPE html>
<html>
	<head>
		<title>Modal Window</title>
		<script src="/kibu/core/util/JS/jquery-1.6.min.js"></script>
		<script src="/kibu/core/util/JS/modernizr-latest.js"></script>
		<script src="/kibu/core/util/JS/js-webshim/minified/polyfiller.js"></script>
		<script>
			$.webshims.polyfill();
		</script>	
		<script src="/kibu/core/util/JS/fancybox/jquery.fancybox-1.3.4.pack.js"></script>		
		<link rel="stylesheet" href="/kibu/core/styles/kibu_core.css" type="text/css" />
		<link rel="stylesheet" type="text/css" href="/kibu/core/styles/jquery.fancybox-1.3.4.css" media="screen" />		
	</head>
	<body id="modal">
		<?=$modalContent;?>		
	</body>
</html>
