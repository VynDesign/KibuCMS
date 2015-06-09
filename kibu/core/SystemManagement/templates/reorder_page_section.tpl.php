

				<fieldset>
					<legend>Reorder <?=$reorderName;?></legend>
					<?foreach($itemsToReorder as $itemID => $item):?>
					<label for="reorder[<?=$itemID;?>]">
						<select name="reorder[<?=$itemID;?>]">
							<?=$item['orderOptions'];?>
						</select>
						<?=$item['name'];?>
					</label>
					<?endforeach;?>
				</fieldset>