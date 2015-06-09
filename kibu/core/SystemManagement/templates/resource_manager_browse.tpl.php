			
			<fieldset>
				<legend>Browse Resources</legend>
				<div class="rmFolderPane left">
					<h4>Folder List</h4>
					<ul class="rmFolderList">
						<?if($showUpOneLevel):?>
						<li><a href="/modal.php?dir=SystemManagement&amp;class=ResourceManager&amp;mode=<?=$mode;?>&amp;currentDir=<?=$parentDir;?>">^ Up One Level</a></li>
						<?endif;?>
						<?if($folders != null):?>
						<?foreach($folders as $key => $folder):?>
						<li><a href="/modal.php?dir=SystemManagement&amp;class=ResourceManager&amp;mode=<?=$mode;?>&amp;currentDir=<?=$key;?>"><?=$folder;?></a></li>
						<?endforeach;?>
						<?endif;?>
					</ul>
					<div class="rmFolderCreatePane">
						<label for="rmNewFolder" class="full">Create New Folder<br />
							<input type="text" class="full" name="rmNewFolder" id="rmNewFolder" />
						</label>
						<label for="createFolder" class="right"><br />
							<input type="submit" name="createFolder" id="createFolder" value="Create" />
						</label><br /><br />
					</div>
				</div>
				<div class="rmFilePane right">
					<h4>File List</h4>
					<?if(isset($files)):?>
					<ul	class="rmFileList">					
						<?foreach($files as $filePath => $fileName):?>
						<li><a href="<?=$filePath;?>" target="_blank"><?=$fileName;?></a></li>
						<?endforeach;?>
					</ul>						
					<?else:?>
					<div class="rmFileList">
						No files found in <?=$currentDir;?>
					</div>
					<?endif;?>
					<div class="rmFileuUploadPane">
						<label for="slideFile" class="left">Upload a new file<br />
							<input type="file" name="rmUpload" id="rmUpload" />
							<input type="hidden" id="fileInputID" name="fileInputID" value="rmUpload" />
						</label>
						<label for="overwriteFile" class="left"><br />
							<input type="checkbox" name="overwriteFile" id="overwriteFile" /> Overwrite existing file?
						</label>			
						<br class="clear" />
						<label for="upload" class="right"><br />
							<input type="submit" name="upload" id="upload" value="Upload" />
						</label>
					</div>
				</div>
			</fieldset>