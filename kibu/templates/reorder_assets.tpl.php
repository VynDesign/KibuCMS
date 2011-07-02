
				<fieldset>
						<legend>Reorder Content</legend>
						<?foreach($assetZones as $key => $name) {
								echo "<fieldset class=\"half left\">\n";
								echo "<legend>".$name." (Zone ".$key.")</legend>\n";
								foreach($zoneAssets as $asset) {
										if($asset['assetZoneNum'] == $key) {
												echo "<label for=\"orderOptions[".$asset['assetID']."]\">";
												echo "<select name=\"orderOptions[".$asset['assetID']."]\" id=\"orderOptions[".$asset['assetID']."]\">\n";
												foreach($orderOptions as $option) {
														if($option['assetZoneNum'] == $key) {
																echo "<option value=\"".$option['assetOrderNum']."\"";
																if($asset['assetOrderNum'] == $option['assetOrderNum']) {
																		echo " selected=\"selected\"";
																}
																echo ">".$option['assetOrderNum']."</option>";
														}
												}
												echo "</select>\n";
												echo $asset['assetName'];
												echo "</label>\n";
										}
								}
								echo "</fieldset>\n";
						}?>
				</fieldset>