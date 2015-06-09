<!DOCTYPE html>
<html>
	<head>
		<title><?=$pageTitle;?></title>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=iso-8859-1" />
		<meta name="description" content="<?=$metaDesc;?>" />
		<meta name="keywords" content="<?=$metaKeywds;?>" />
		<script src="/kibu/core/util/JS/jquery-1.6.min.js" type="text/javascript"></script>
		<script type="text/javascript" src="/kibu/core/util/jqueryplugins/jquery.mousewheel-3.0.4.pack.js"></script>
		<script src="/kibu/core/util/JS/fancybox/jquery.fancybox-1.3.4.pack.js" type="text/javascript"></script>		
		<link rel="stylesheet" type="text/css" href="/kibu/core/styles/jquery.fancybox-1.3.4.css" media="screen" />		
		<script src="/kibu/core/util/JS/prototype.js" type="text/javascript"></script>
		<script src="/kibu/core/util/JS/scriptaculous.js?load=builder,effects" type="text/javascript"></script>
		<link rel="stylesheet" href="/kibu/core/styles/kibu_core.css" type="text/css" />
		<link rel="stylesheet" href="/kibu/core/styles/modalbox.css" type="text/css" />
		<link rel="stylesheet" href="/kibu/site_resources/style/CGF_default.css" type="text/css" />
		<!--[if lte IE 6]>
			<script src="/kibu/site_resources/JS/suckerfishIE.js" type="text/javascript">  
			<link rel="stylesheet" href="/kibu/site_resources/style/CGF_IE6.css" type="text/css" />
		<![endif]-->
		<?=$additionalHead;?>
	</head>
	<body <?=$bodyExtra;?>>
		<div id="header">
			<h1 class="sitetitle"><a href="/"><span class="noshow"><?=$siteTitle;?></span></a></h1>
			<h2 class="sitetagline"><span class="noshow"><?=$siteTagLine;?></span></h2>
		</div>
		<div id="globalNav">
			<?=$globalNav;?>
		</div>
		<?=$editorToolbar;?>
		<?=$pageContent;?>
		<div id="footer">
			<span class="copyright">All content &copy; <?=$year;?> <a href="/"><?=$siteOwner;?></a>. All rights reserved.</span>
			<div class="welcomeMessage">
				<?=$welcomeMessage;?>
			</div>
			<br class="clear" />
		</div>
		<?=$additionalFoot;?>
	</body>
</html>