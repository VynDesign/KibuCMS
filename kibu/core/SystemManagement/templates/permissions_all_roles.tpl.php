
					<fieldset>
						<legend><?=$legend;?></legend>
						<?if(count($roles)):?>
						<table class="sysManagement" cellpadding="7">
							<tr> 
								<th>Role Name</th>
								<th>Role Description</th>
								<th>Actions</th>
							</tr>
							<?foreach($roles as $role => $data):?>
							<tr>
								<td><?=$data['permRoleName'];?></td>
								<td><?=$data['permRoleDesc'];?></td>
								<td align="center"><a href="/modal.php?dir=SystemManagement&amp;class=PermissionManagement&amp;mode=ModifyRole&amp;role=<?=$data['permRoleGUID'];?>">Edit</a> | 
									<a href="/modal.php?dir=SystemManagement&amp;class=PermissionManagement&amp;mode=AssignRole&amp;role=<?=$data['permRoleGUID'];?>">Assign User</a><!-- | 
									<a href="/modal.php?class=PermissionManagement&amp;mode=DeleteRole&amp;role=<?=$data['permRoleGUID'];?>">Delete</a></td>-->
							</tr>
							<?endforeach;?>
						</table>
						<?endif;?>
					</fieldset>