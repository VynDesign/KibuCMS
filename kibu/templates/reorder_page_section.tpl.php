

				<fieldset>
						<legend>Reorder <?=$reorderName;?></legend>
						<?foreach($itemsToReorder as $itemID => $item):?>
								<label for="reorder[<?=$itemID;?>]">
										<select name="reorder[<?=$itemID;?>]">
												<?foreach($reorderOptions as $option => $orderNum):?>
												<option value="<?=$orderNum;?>"	<?if($item['orderNum'] == $orderNum) { echo " selected=\"selected\"";}?>><?=$orderNum;?></option>
												<?endforeach;?>
										</select>
										<?if($item['isVisible'] == 'n' || $item['isVisible'] == 'invis' || $item['isVisible'] == 'inac'):?>
										<em><?=$item['name'];?> (hidden)</em>
										<?else:?>
										<?=$item['name'];?>
										<?endif;?>
								</label>
						<?endforeach;?>
				</fieldset>