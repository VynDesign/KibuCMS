
				<fieldset>
						<legend>Edit Content</legend>
						<label for="assetID">Choose Content to Edit<br />
								<select name="assetID">
										<option>.:Choose:.</option>
										<?foreach($assetZones as $zoneName => $assets):?>
										<optgroup label="<?=$zoneName;?>">
												<?foreach($assets as $assetID => $assetName):?>
												<option value="<?=$assetID;?>"><?=$assetName;?></option>
												<?endforeach;?>
										</optgroup>
										<?endforeach;?>
								</select>
						</label>
				</fieldset>