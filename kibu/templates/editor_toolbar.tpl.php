
				<fieldset class="editorToolbar">
						<legend>Admin Tools <!--(<a href="">Hide</a>)--></legend>
						<div id="tabcontainer">
								<ul class="tabbox">
										<li>
												<h6 class="tabTitle"><a href="#pageoptions" name="pageoptions" onclick="return changeTab(this)" onfocus="return changeTab(this)">Page Options</a></h6>
														<div class="revealer toolpanel">
																<a href="?mode=edit">Edit Page</a> | <a href="/modal.php?mode=pagesettings&amp;recordNum=20090723-073700-1" title="Modify Page Settings" onclick="Modalbox.show(this.href, {title: this.title, width: 700}); return false;">Modify Settings</a>	| <a href="/modal.php?mode=addcontent&amp;recordNum=20090723-073700-1" title="Add Content" onclick="Modalbox.show(this.href, {title: this.title, width: 700}); return false;">Add Content</a>
														</div>
										</li>
										<li>
												<h6 class="tabTitle"><a href="#sectionoptions" name="sectionoptions" onclick="return changeTab(this)" onfocus="return changeTab(this)">Section Options</a></h6>
												<div class="revealer toolpanel">
														<a href="/modal.php?mode=createpage" title="Create New Page" onclick="Modalbox.show(this.href, {title: this.title, width: 700}); return false;">Create New Page</a> | <a href="/modal.php?mode=sectionsettings" title="Modify Section Settings" onclick="Modalbox.show(this.href, {title: this.title, width: 700}); return false;">Modify Section Settings</a> | <a href="/modal.php?mode=reorder&amp;reordertype=pages" title="Reorder Pages" onclick="Modalbox.show(this.href, {title: this.title, width: 700}); return false;">Reorder Pages</a></div>
										</li>
										<li>
												<h6 class="tabTitle"><a href="#siteoptions" name="siteoptions" onclick="return changeTab(this)" onfocus="return changeTab(this)">Site Options</a></h6>
												<div class="revealer toolpanel">
														<a href="/modal.php?mode=createsection" title="Create New Section" onclick="Modalbox.show(this.href, {title: this.title, width: 700}); return false;">Create New Section</a> | <a href="/modal.php?mode=sitesettings" title="Modify Site Settings" onclick="Modalbox.show(this.href, {title: this.title, width: 700}); return false;">Site Settings</a> | <a href="/modal.php?mode=reorder&amp;reordertype=sections" title="Reorder Sections" onclick="Modalbox.show(this.href, {title: this.title, width: 700}); return false;">Reorder Sections</a></div>
										</li>
								</ul>
						</div>
				</fieldset>