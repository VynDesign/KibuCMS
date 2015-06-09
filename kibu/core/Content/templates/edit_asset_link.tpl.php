		

				<script type="text/javascript">
					$().ready(function() {
						$("a#assetEdit_<?=$assetID;?>").fancybox({hideOnOverlayClick: false, height: 600, width: 700, type: 'iframe' });
					});
				</script>
				<a class="editAssetLink" title="Edit Settings for <?=$assetName;?>" id="assetEdit_<?=$assetID?>" href="/modal.php?dir=SystemManagement&amp;class=AssetManagement&amp;mode=EditAsset&amp;assetID=<?=$assetID;?>">Edit <?=$assetName;?></a>
		