
						<fieldset>
							<legend><?=$assetName;?> Specific Settings</legend>
							<label for="asset[assetBody]"><?=$assetTypeDesc;?></label>
							<?
								require_once './kibu/core/util/fckeditor/fckeditor.php';
								$oFCKeditor = new FCKeditor("asset[assetBody]") ;
								$oFCKeditor->BasePath = '/kibu/core/util/fckeditor/' ;
								$oFCKeditor->Value = $assetBody ;
								$oFCKeditor->Width  = '650' ;
								$oFCKeditor->Height = '600' ;
								$oFCKeditor->Config["CustomConfigurationsPath"] = "/kibu/core/util/fckeditor/kibu_config.js"  ;
								$oFCKeditor->ToolbarSet = 'KibuToolbar';
								$oFCKeditor->Create() ;
							?>
						</fieldset>