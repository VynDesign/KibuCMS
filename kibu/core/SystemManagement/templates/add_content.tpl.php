
						<fieldset>
							<legend>Add New Content Asset</legend>
							<label for="assetName" class="full">Content Name:<br />
								<input class="full" name="assetName" id="assetName" type="text" />
							</label>
							<br />
							<label for="assetTypeID" class="full">Content Type:<br />
								<select class="full" name="assetTypeID" id="assetTypeID">
									<option value="" selected="selected">.:Choose:.</option>
									<?=$assetTypeOptions;?>
								</select>
							</label>
							<br />
							<label for="contentZoneID" class="full">Content Zone:<br />
								<select class="full" name="contentZoneID" id="contentZoneID">
									<option value="" selected="selected">.:Choose:.</option>
									<?=$assetZoneOptions;?>
								</select>
							</label>
							<br />
							<input type="hidden" name="<?=$hiddenInputID;?>" id="<?=$hiddenInputID;?>" value="<?=$hiddenInputVal;?>" />
						</fieldset>

