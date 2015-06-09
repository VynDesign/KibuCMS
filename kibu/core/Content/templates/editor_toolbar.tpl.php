

				<script type="text/javascript">
					$().ready(function() {
						$("a#pageSettings").fancybox({hideOnOverlayClick: false, height: 600, width: 700, type: 'iframe' });
						$("a#addContent").fancybox({hideOnOverlayClick: false, height: 325, width: 350, type: 'iframe' });
						$("a#reorderContent").fancybox({hideOnOverlayClick: false, height: 600, width: 700, type: 'iframe' });
						
						$("a#createPage").fancybox({hideOnOverlayClick: false, height: 600, width: 700, type: 'iframe' });
						$("a#sectionSettings").fancybox({hideOnOverlayClick: false, height: 400, width: 700, type: 'iframe' });
						$("a#reorderPages").fancybox({hideOnOverlayClick: false, height: 500, width: 500, type: 'iframe' });
						
						$("a#newSection").fancybox({hideOnOverlayClick: false, height: 400, width: 700, type: 'iframe' });
						$("a#reorderSections").fancybox({hideOnOverlayClick: false, height: 400, width: 400, type: 'iframe' });
						$("a#siteSettings").fancybox({hideOnOverlayClick: false, height: 600, width: 700, type: 'iframe' });
						$("a#serverSettings").fancybox({hideOnOverlayClick: false, height: 400, width: 700, type: 'iframe' });
						
						$("a#manageResources").fancybox({hideOnOverlayClick: false, height: 600, width: 800, type: 'iframe' });
						$("a#manageDocs").fancybox({hideOnOverlayClick: false, height: 600, width: 800, type: 'iframe' });
						$("a#manageImages").fancybox({hideOnOverlayClick: false, height: 600, width: 800, type: 'iframe' });
						$("a#manageFlash").fancybox({hideOnOverlayClick: false, height: 600, width: 800, type: 'iframe' });
						$("a#manageMedia").fancybox({hideOnOverlayClick: false, height: 600, width: 800, type: 'iframe' });
						$("a#manageStyles").fancybox({hideOnOverlayClick: false, height: 600, width: 800, type: 'iframe' });

						$("a#useroverview").fancybox({hideOnOverlayClick: false, height: 600, width: 800, type: 'iframe' });	
						$("a#verifiedusers").fancybox({hideOnOverlayClick: false, height: 600, width: 800, type: 'iframe' });												
						$("a#unverifiedusers").fancybox({hideOnOverlayClick: false, height: 600, width: 800, type: 'iframe' });						
						$("a#purgeusers").fancybox({hideOnOverlayClick: false, height: 400, width: 650, type: 'iframe' });
						
						$("a#newAbility").fancybox({hideOnOverlayClick: false, height: 250, width: 500, type: 'iframe' });
						$("a#newRole").fancybox({hideOnOverlayClick: false, height: 500, width: 650, type: 'iframe' });
						$("a#allRoles").fancybox({hideOnOverlayClick: false, height: 600, width: 750, type: 'iframe' });
						$("a#allAbilities").fancybox({hideOnOverlayClick: false, height: 600, width: 750, type: 'iframe' });
						$("a#assignRole").fancybox({hideOnOverlayClick: false, height: 600, width: 750, type: 'iframe' });


						$("a#modulesOverview").fancybox({hideOnOverlayClick: false, height: 600, width: 750, type: 'iframe' });
						$("a#installModule").fancybox({hideOnOverlayClick: false, height: 600, width: 750, type: 'iframe' });
						$("a#uninstallModule").fancybox({hideOnOverlayClick: false, height: 600, width: 750, type: 'iframe' });
					});
					
					
					var currentTab = 0; // Set to a different number to start on a different tab.
					function openTab(clickedTab) {
						var thisTab = $(".tabbed-box .tabs a").index(clickedTab);
						$(".tabbed-box .tabs li a").removeClass("active");
						$(".tabbed-box .tabs li a:eq(" + thisTab + ")").addClass("active");
						$(".tabbed-box .tabbed-content").hide();
						$(".tabbed-box .tabbed-content:eq(" + thisTab + ")").show();
						currentTab = thisTab;
					}
					jQuery(document).ready(function () {
						$(".tabbed-box .tabs li a").click(function () {
							openTab($(this)); return false;
						});
						$(".tabbed-box .tabs li a:eq(" + currentTab + ")").click()
					});					
				</script>

				<div class="editorToolbar">		
					<h3>Admin Toolbar</h3>
					<div class="tabbed-box">
						<ul id="tabs" class="tabs">
							<li><a href="#pageoptions">Page</a></li>
							<li><a href="#sectionoptions">Section</a></li>
							<li><a href="#siteoptions">Site/Server</a></li>
							<li><a href="#resourcemanagement">Resources</a></li>						
							<li><a href="#usermanagement">Users</a></li>
							<li><a href="#permissionmanagement">Permissions</a></li>
							<li><a href="#modulemanagement">Modules</a></li>
							<!--<li><a href="#templateslayouts">Templates/Layouts</a></li>
							<li><a href="#contenttypemanagement">Content Types</a></li>-->							
						</ul>
						<div class="tabbed-content panel" id="pageoptions">
							<a id="editContent" href="<?=$contentLink;?>?mode=<?=$mode;?>"><?=$editLinkText;?></a> | 
							<a id="pageSettings" href="/modal.php?dir=SystemManagement&amp;class=PageSettings&amp;mode=ModifyPage&amp;recordNum=<?=$contentRecordNum;?>" title="Modify Page Settings">Modify Page Settings</a> | 
							<a id="addContent" href="/modal.php?dir=SystemManagement&amp;class=AssetManagement&amp;mode=AddAsset&amp;recordNum=<?=$contentRecordNum;?>" title="Add Content" >Add Content</a> | 
							<a id="reorderContent" href="/modal.php?dir=SystemManagement&amp;class=AssetManagement&amp;mode=ReorderAssets&amp;recordNum=<?=$contentRecordNum;?>" title="Reorder Content">Reorder Content</a>
						</div>
						<div class="tabbed-content panel" id="sectionoptions">
							<a id="createPage" href="/modal.php?dir=SystemManagement&amp;class=PageSettings&amp;mode=createpage" title="Create New Page">Create New Page</a> |
							<a id="sectionSettings" href="/modal.php?dir=SystemManagement&amp;class=SectionSettings&amp;mode=ModifySection&amp;sectionID=<?=$sectionID;?>" title="Modify Section Settings">Modify Section Settings</a> |
							<a id="reorderPages" href="/modal.php?dir=SystemManagement&amp;class=Reorder&amp;mode=pages&amp;sectionID=<?=$sectionID;?>" title="Reorder Pages">Reorder Pages</a>
						</div>
						<div class="tabbed-content panel" id="siteoptions">
							<a id="newSection" href="/modal.php?dir=SystemManagement&amp;class=SectionSettings&amp;mode=CreateSection" title="Create New Section">Create New Section</a> |
							<a id="reorderSections" href="/modal.php?dir=SystemManagement&amp;class=Reorder&amp;mode=sections&amp;siteConfigID=<?=$siteConfigID;?>" title="Reorder Sections">Reorder Sections</a> | 						
							<a id="siteSettings" href="/modal.php?dir=SystemManagement&amp;class=SiteSettings&amp;mode=sitesettings" title="Modify Site Settings">Modify Site Settings</a> | 
							<a id="serverSettings" href="/modal.php?dir=SystemManagement&amp;class=SiteSettings&amp;mode=ServerSettings" title="Modify Server Settings">Modify Server Settings</a>
						</div>
						<div class="tabbed-content panel" id="resourcemanagement">
							<a id="manageResources" href="/modal.php?dir=SystemManagement&amp;class=ResourceManager&amp;mode=">All Resources</a> | 
							<a id="manageDocs" href="/modal.php?dir=SystemManagement&amp;class=ResourceManager&amp;mode=&amp;currentDir=/docs">Documents</a> | 
							<a id="manageImages" href="/modal.php?dir=SystemManagement&amp;class=ResourceManager&amp;mode=&amp;currentDir=/images">Images</a> | 
							<a id="manageFlash" href="/modal.php?dir=SystemManagement&amp;class=ResourceManager&amp;mode=&amp;currentDir=/flash">Flash</a> | 
							<a id="manageMedia" href="/modal.php?dir=SystemManagement&amp;class=ResourceManager&amp;mode=&amp;currentDir=/media">Media</a> | 
							<a id="manageStyles" href="/modal.php?dir=SystemManagement&amp;class=ResourceManager&amp;mode=&amp;currentDir=/style">Stylesheets</a>
						</div>					
						<div class="tabbed-content panel" id="usermanagement">
							<a id="useroverview" href="/modal.php?dir=SystemManagement&amp;class=UserManagement&amp;mode=List">All Users</a> | 
							<a id="verifiedusers" href="/modal.php?dir=SystemManagement&amp;class=UserManagement&amp;mode=List&amp;filter=Verified">Verified Users</a> | 
							<a id="unverifiedusers" href="/modal.php?dir=SystemManagement&amp;class=UserManagement&amp;mode=List&amp;filter=Unverified">Unverified Users</a> | 
							<a id="purgeusers" href="/modal.php?dir=SystemManagement&amp;class=UserManagement&amp;mode=Purge">Purge Unverified</a>
						</div>
						<div class="tabbed-content panel" id="permissionmanagement">
							<a id="newAbility" href="/modal.php?dir=SystemManagement&amp;class=PermissionManagement&amp;mode=CreateAbility">Create New Ability</a> | 
							<a id="allAbilities" href="/modal.php?dir=SystemManagement&amp;class=PermissionManagement&amp;mode=AllAbilities">All Abilities</a> | 
							<a id="newRole" href="/modal.php?dir=SystemManagement&amp;class=PermissionManagement&amp;mode=CreateRole">Create New Role</a> | 
							<a id="allRoles" href="/modal.php?dir=SystemManagement&amp;class=PermissionManagement&amp;mode=AllRoles">All Roles</a> 
						</div>					
						<div class="tabbed-content panel" id="modulemanagement">
							<a id="modulesOverview" href="/modal.php?dir=SystemManagement&amp;class=ModuleManagement&amp;mode=">Modules Overview</a> | 
							<a id="installModule" href="/modal.php?dir=SystemManagement&amp;class=ModuleManagement&amp;mode=InstallModule">Install Module</a> | 
							<a id="uninstallModule" href="/modal.php?dir=SystemManagement&amp;class=ModuleManagement&amp;mode=UninstalledModules">Uninstall Module</a>
						</div>	
						<!--<div class="tabbed-content panel" id="templateslayouts">
							<a id="">All Page Layouts</a> | 
							<a id="">Create Page Layout</a> | 
							<a id="">All Templates</a> | 
							<a id=""></a>
						</div>
						<div class="tabbed-content panel" id="contenttypemanagement">
							<a id="contentTypesOverview">All Content Types</a> | 
							<a id="createContentType">Create Content Type</a>	
						</div>-->						
					</div>
				</div>
