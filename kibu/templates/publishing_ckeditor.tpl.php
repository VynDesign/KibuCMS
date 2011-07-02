
			<fieldset>
				<legend><?=$assetName;?></legend>
					<label for="asset[assetBody]"><?=$assetTypeDesc;?></label>
					<textarea rows="40" cols="400" name="asset[assetBody]"><?=$assetBody;?></textarea>
				<script type="text/javascript">
					CKEDITOR.replace( 'asset[assetBody]',
						{
								width : 660,
								height: 300
						});
				</script>
			</fieldset>