
					<fieldset>
						<legend><?=$legend;?></legend>
						<?if(count($abilities)):?>
						<table class="sysManagement" cellpadding="7">
							<tr> 
								<th>Ability Name</th>
								<th>Ability Description</th>
								<th>Actions</th>
							</tr>
							<?foreach($abilities as $ability => $data):?>
							<tr>
								<td><?=$data['permAbilityName'];?></td>
								<td><?=$data['permAbilityDesc'];?></td>
								<td align="center"><a href="/modal.php?dir=SystemManagement&amp;class=PermissionManagement&amp;mode=ModifyAbility&amp;ability=<?=$ability;?>">Edit</a> <!--| 
									<a href="/modal.php?class=PermissionManagement&amp;mode=DeleteAbility&amp;ability=<?=$ability;?>">Delete</a></td>-->
							</tr>
							<?endforeach;?>
						</table>
						<?endif;?>
					</fieldset>