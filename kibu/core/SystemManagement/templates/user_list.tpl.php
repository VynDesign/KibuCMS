

				<fieldset>
					<legend>List of <?=$filter;?> Users</legend>
					<?if(count($userList)):?>
					<table class="sysManagement" cellpadding="7">
						<tr>
							<th>User Name<br />(click to edit)</th>
							<th>Permissions</th>
							<th>Join Date</th>							
							<th>Email<br />Address</th>
							<th>Email<br />Verified?</th>
							<th>Resend<br />Verification<br />email?</th>
							<th>Delete<br />User?</th>
						</tr>
					<?foreach($userList as $user => $data):?>
						<tr>
							<td><a href="/modal.php?dir=SystemManagement&amp;class=UserManagement&amp;mode=Details&amp;userGUID=<?=$data['userGUID'];?>"><?=$data['userName'];?></a></td>
							<td><a href="/modal.php?dir=SystemManagement&amp;class=PermissionManagement&amp;mode=UserRoles&amp;user=<?=$data['userGUID'];?>">Modify</a></td>							
							<td align="center"><?=$data['joinDate'];?></td>
							<td><?=$data['emailAddress'];?></td>							
							<td align="center"><?=$data['emailVerified'];?></td>							
							<td align="center"><?if($data['userLevelNum'] < 10000):?><input type="checkbox" name="reverify[<?=$data['userGUID'];?>]" id="reverify[<?=$data['userGUID'];?>]" /><?endif;?></td>
							<td align="center"><?if($data['userLevelNum'] < 10000):?><input type="checkbox" name="delete[<?=$data['userGUID'];?>]" id="delete[<?=$data['userGUID'];?>]" /><?endif;?></td>
						</tr>
					<?endforeach;?>
						<tr>
							<td colspan="5"></td>
							<td align="center"><input type="submit" id="doReverify" name="confirmReverify" value="Reverify" /></td>
							<td align="center"><input type="submit" id="doDelete" name="confirmDelete" value="Delete" /></td>							
						</tr>						
					</table>
					<?else:?>
					<p>No <?=$filter;?> Users found.</p>
					<?endif;?>
				</fieldset>
