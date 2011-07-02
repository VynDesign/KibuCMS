
				<fieldset>
						<legend><?=$assetName;?> Settings</legend>
						<input type="hidden" name="assetID" id="assetID" value="<?=$assetID;?>" />
						<label class="small left" for="asset[assetName]">Content Name<br />
								<input type="text" name="asset[assetName]" id="asset[assetName]" value="<?=$assetName;?>" />
						</label>
						<label class="small left" for="isVisible">Visibility<br />
								<select name="asset[isVisible]" id="asset[isVisible]">
										<option value="y" <?if($isVisible == 'y') {echo " selected=\"selected\"";}?>>Visible</option>
										<option value="n" <?if($isVisible == 'n') {echo " selected=\"selected\"";}?>>Hidden</option>
								</select>
						</label>
						<br class="clear" />
						<?=$assetEditTpl;?>
				</fieldset>