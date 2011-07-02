
			<script type="text/javascript" src="/kibu/core/util/ckeditor/ckeditor.js"></script>

			<fieldset>
				<legend><?=$assetName;?></legend>
					<label for="asset[assetBody]"><?=$assetTypeDesc;?></label>
					<textarea name="asset[assetBody]"><?=$assetBody;?></textarea>
				<script type="text/javascript">
					CKEDITOR.replace( 'asset[assetBody]',
									{
										toolbar :
										[
											[ 'Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink' ],
										]
									});
				</script>
			</fieldset>