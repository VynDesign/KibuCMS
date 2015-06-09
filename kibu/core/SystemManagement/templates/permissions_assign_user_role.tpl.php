
					<fieldset>
						<legend><?=$legend;?></legend>
						<p>Assign role-based permissions to user <?=$userName;?></p>
						<?if($assignedRoles != null):?>
						<fieldset>
							<legend>Assigned Roles</legend>
							<p class="small">Below are all roles that <strong>HAVE</strong> been assigned to this user. Check the box to the left of each to <strong>REMOVE</strong> these roles when the form is processed.</p>
							<?foreach($assignedRoles as $roleGuid => $roleName):?>
							<label class="left third" for="removeRoles[<?=$roleGuid;?>]">
								<input type="checkbox" id="removeRoles[<?=$roleGuid;?>]" name="removeRoles[<?=$roleGuid;?>]" /> <?=$roleName;?>
							</label>
							<?endforeach;?>
						</fieldset>
						<?endif;?>

						<?if($unassignedRoles != null):?>
						<fieldset>
							<legend>Unassigned Roles</legend>
							<p class="small">Below are all roles that have <strong>NOT</strong> been assigned to this user. Check the box to the left of each to <strong>ADD</strong> these roles when the form is processed.</p>						
							<?foreach($unassignedRoles as $roleGuid => $roleName):?>
							<label class="left third" for="addRoles[<?=$roleGuid;?>]">
								<input type="checkbox" id="addRoles[<?=$roleGuid;?>]" name="addRoles[<?=$roleGuid;?>]" /> <?=$roleName;?>
							</label>
							<?endforeach;?>
						</fieldset>
						<?endif;?>
					</fieldset>	
