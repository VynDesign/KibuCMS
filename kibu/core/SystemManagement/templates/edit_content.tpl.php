
				<fieldset>
					<legend>General Content Settings</legend>
					<input type="hidden" name="assetID" id="assetID" value="<?=$assetID;?>" />
					<label class="twothirds left" for="asset[assetName]">Content Name<br />
						<input type="text" class="full" name="asset[assetName]" id="asset[assetName]" value="<?=$assetName;?>" />
					</label>
					<label class="third right" for="isVisible">Visibility<br />
						<select class="full" name="asset[isVisible]" id="asset[isVisible]">
							<option value="y" <?if($isVisible == 'y') {echo " selected=\"selected\"";}?>>Visible</option>
							<option value="n" <?if($isVisible == 'n') {echo " selected=\"selected\"";}?>>Hidden</option>
						</select>
					</label>
					<br class="clear" />
				</fieldset>
				<?=$assetEditTpl;?>