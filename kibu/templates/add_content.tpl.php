
						<fieldset>
								<legend>Add a new content block to this page</legend>
								<label for="assetName" class="third left">Content Name:<br />
										<input name="assetName" id="assetName" type="text" />
								</label>
								<label for="assetTypeID" class="third left">Content Type:<br />
										<select name="assetTypeID" id="assetTypeID">
												<option value="" selected="selected">.:Choose:.</option>
												<?foreach($assetTypes as $assetTypeID => $assetTypeName):?>
														<option value="<?=$assetTypeID;?>"><?=$assetTypeName;?></option>
												<?endforeach;?>
										</select>
								</label>
								<label for="contentZoneID" class="third left">Content Zone:<br />
										<select name="contentZoneID" id="contentZoneID">
												<option value="" selected="selected">.:Choose:.</option>
												<?foreach($assetZones as $zoneID => $zoneName):?>
														<option value="<?=$zoneID;?>"><?=$zoneName;?></option>
												<?endforeach;?>
										</select>
								</label>
								<input type="hidden" name="<?=$hiddenInputID;?>" id="<?=$hiddenInputID;?>" value="<?=$hiddenInputVal;?>" />
						</fieldset>

