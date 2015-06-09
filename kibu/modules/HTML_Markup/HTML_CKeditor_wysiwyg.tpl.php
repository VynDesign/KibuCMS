
						<fieldset>
							<legend><?=$assetName;?> Specific Settings</legend>
							<script type="text/javascript" src="/kibu/core/util/ckeditor/ckeditor.js"></script>
							<label for="asset[assetBody]"><?=$assetTypeDesc;?></label>
							<textarea rows="40" cols="75" name="asset[assetBody]"><?=$assetBody;?></textarea>
								<script type="text/javascript">
									CKEDITOR.replace( 'asset[assetBody]',
									{
										width : 650,
										height: 700,
										filebrowserBrowseUrl : '/modal.php?dir=SystemManagement&class=ResourceManager&mode=CKE',
										filebrowserImageBrowseUrl : '/modal.php?dir=SystemManagement&class=ResourceManager&mode=CKE&currentDir=/images',
										toolbar:
											[
												['Undo','Redo','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
												['Bold','Italic','Underline','Strike','Subscript','Superscript'],
												['NumberedList','BulletedList','Blockquote','Outdent','Indent','Format'],
												['Link','Unlink','Anchor'],
												['Image','Table','HorizontalRule','SpecialChar'],
												['Paste','PasteText','PasteFromWord','SelectAll','RemoveFormat','SpellChecker'],
												['Find','Replace','-','ShowBlocks','-','Source']
											]
									});
								</script>
						</fieldset>