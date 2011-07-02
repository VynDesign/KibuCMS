<?="<?";?>xml version="1.0" encoding="ISO-8859-1"<?= "?>\n"; ?>
		<rss version="2.0">
				<channel>
						<title><?=$pageTitle;?> - RSS 2.0 Feed</title>
						<link>http://<?=$siteAddress;?></link>
						<description><?=$metaDesc;?></description>
						<language>en-us</language>
						<lastBuildDate><?= date('D, d M Y H:i:s');?> EST</lastBuildDate>
						<docs>http://<?=$siteAddress;?></docs>
						<?=$pageContent[1];?>
				</channel>
		</rss>