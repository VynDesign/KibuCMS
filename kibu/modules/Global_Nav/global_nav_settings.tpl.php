
						<fieldset>
							<legend><?=$assetName;?> Specific Settings</legend>
							<p style="color:red;"><strong>Warning: </strong> Changing the default values here can affect the design of your site.</p>
							<label class="small left" for="paramOpts[listID]">List ID<br />
								<input type="text" name="paramOpts[listID]" id="paramOpts[listID]" value="<?=$listID;?>" />
							</label>
							<label class="small left" for="paramOpts[listClass]">List Class<br />
								<input type="text" name="paramOpts[listClass]" id="paramOpts[listClass]" value="<?=$listClass;?>" />
							</label>
							<label class="small left" for="paramOpts[listItemClass]">List Item Class<br />
								<input type="text" name="paramOpts[listItemClass]" id="paramOpts[listItemClass]" value="<?=$listItemClass;?>" />
							</label>
							<label class="small left" for="paramOpts[listItemLinkClass]">List Item Link Class<br />
								<input type="text" name="paramOpts[listItemLinkClass]" id="paramOpts[listItemLinkClass]" value="<?=$listItemLinkClass;?>" />
							</label>
							<label class="small left" for="paramOpts[selectedListItemClass]">Selected List Item Class<br />
								<input type="text" name="paramOpts[selectedListItemClass]" id="paramOpts[selectedListItemClass]" value="<?=$selectedListItemClass;?>" />
							</label>
							<label class="small left" for="paramOpts[selectedListItemLinkClass]">Selected List Item Link Class<br />
								<input type="text" name="paramOpts[selectedListItemLinkClass]" id="paramOpts[selectedListItemLinkClass]" value="<?=$selectedListItemLinkClass;?>" />
							</label>	
							<br class="clear" />
							<label>
								<strong>Options Key:</strong>
								<ul>
									<li><em>List ID:</em> A unique identifier for the over-all list, generally used for specifying stylesheet rules.</li>
									<li><em>List Class:</em> A generic identifier for the over-all list, generally used for specifying stylesheet rules.</li>
									<li><em>List Item Class:</em> A generic identifier that will be applied to each list item.</li>
									<li><em>List Item Link Class:</em> A generic identifier that will be applied to each link inside each list item.</li>
									<li><em>Selected List Item Class:</em> A generic identifier applied to the specific list item containing the current "selected" navigation item.</li>
									<li><em>Selected List Item Link Class:</em> A generic identifier applied to the specific currently "selected" navigation item link.</li>
								</ul>
							</label>						
						</fieldset>