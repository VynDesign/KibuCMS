

						<fieldset>
							<legend><?=$assetName;?> Specific Settings</legend>
							<label for="contentAsset[<?=$assetID;?>]"><?=$assetTypeDesc;?></label>
							<?if($assetBody):?><p>Current file: <a href="<?=$assetBody;?>" target="blank"><?=$assetBody;?></a><?endif;?>
							<label for="file">Upload file<br />
								<input type="hidden" name="contentAsset[<?=$assetID;?>]" id="contentAsset[<?=$assetID;?>]" value="<?=$assetBody;?>" />
								<input type="file" name="file[<?=$assetID;?>]" id="file[<?=$assetID;?>]" />
							</label>
						</fieldset>