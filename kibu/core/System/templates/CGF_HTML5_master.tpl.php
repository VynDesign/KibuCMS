<!DOCTYPE html>
<html>
	<head>
		<title><?=$pageTitle;?></title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta charset="utf-8" />
		<meta name="description" content="<?=$metaDesc;?>" />
		<meta name="keywords" content="<?=$metaKeywds;?>" />
		<script src="/kibu/core/util/JS/jquery-1.7.1.min.js"></script>
		<script src="/kibu/core/util/JS/modernizr-latest.js"></script>
		<script src="/kibu/core/util/JS/js-webshim/minified/polyfiller.js"></script>
		<script>
			//$.webshims.polyfill();
		</script>
		<script src="/kibu/core/util/JS/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
		<link rel="stylesheet" href="/kibu/core/styles/kibu_core.css" />
		<link rel="stylesheet" href="/kibu/site_resources/style/CGF_default.css" />
		<link rel="stylesheet" href="/kibu/core/styles/jquery.fancybox-1.3.4.css" media="screen" />
		<!--[if lte IE 6]>
			<script src="/kibu/site_resources/JS/suckerfishIE.js"></script>  
			<link rel="stylesheet" href="/kibu/site_resources/style/CGF_IE6.css" />
		<![endif]-->
		<?=$additionalHead;?>
	</head>
	<body <?=$bodyExtra;?>>
		<?=$editorToolbar;?>		
		<header id="header">
			<hgroup>
				<h1 class="sitetitle"><a href="/"><span class="noshow"><?=$siteTitle;?></span></a></h1>
				<h2 class="sitetagline"><span class="noshow"><?=$siteTagLine;?></span></h2>
			</hgroup>
		</header>
		<nav id="globalNav">
			<?=$globalNav;?>
		</nav>
		<?=$pageContent;?>
		<footer id="footer">
			<span class="copyright">All content &copy; <?=$year;?> <a href="/"><?=$siteOwner;?></a>. All rights reserved.</span>
			<div class="welcomeMessage">
				<?=$welcomeMessage;?>
			</div>
			<br class="clear" />
		</footer>
		<?=$additionalFoot;?>
	</body>
</html>