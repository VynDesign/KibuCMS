
				<fieldset>
					<legend>Reorder Content</legend>
					<?foreach($assetZones as $zone => $assets):?>
						<fieldset class="half left">
							<legend><?=$zone;?></legend>
							<?foreach($assets as $asset):?>
							<label for="orderOptions[<?=$asset['assetID'];?>]">
								<select name="orderOptions[<?=$asset['assetID'];?>]" id="orderOptions[<?=$asset['assetID'];?>]">
									<?=$asset['orderOptions'];?>
								</select> <?=$asset['assetName'];?>
							</label>					
							<?endforeach;?>
						</fieldset>
					<?endforeach;?>
				</fieldset>