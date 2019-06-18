<?php

printAdminHeader('overview', gettext('Bulk tagger'));
?>
<link rel="stylesheet" type="text/css" media="screen" href="bulk-captions.css" />
<?php
echo '</head>';
?>
<body>
	<?php printLogoAndLinks(); ?>
	<div id="main">
		<?php printTabs(); ?>
		<div id="content">
			<ul class="subnav">
				<li class="<?php if (isBulkCaptionDateMode()) { echo "current"; } ?>"><a href="?order=date">By date</a></li>
				<li class="<?php if (isBulkCaptionAlbumMode()) { echo "current"; } ?>"><a href="?order=album">By album</a></li>
			</ul>
			<div class="tabbox">
				<h1>Bulk caption images</h1>
				<form id="captionForm" method="post">
					<div id="captionPanel">
						<?php 
						displayBulkCaptionProcessingResults();
							
						if (getNumPhotostreamImages() > 0) {
						    
							echo "<table class=\"bordered\">";
							echo "<input name=\"imageCount\" value=\"" . getNumPhotostreamImages() . "\" type=\"hidden\" />";            					
							
							global $_zp_current_image;
							$imageID = 0;
							while (next_photostream_image()):
								$imageID++;
								$albumLinkText = getAlbumTitleForPhotostreamImage();
								?>
                        	<tr>
                            	<td class="imagethumb">
                            	    <a href="<?php echo getImageURL();?>" title="<?php echo getImageTitle();?>">
                            		    <img src="<?php echo getImageThumb() ?>" title="<?php echo getImageTitle();?>" alt="<?php echo getImageTitle();?>" />
                            	    </a>
                            	</td>
                            	<td class="imageinputs">
                        		    <div>
                        		        <label for="title_<?php echo $imageID ?>">Title:</label>
                        		        <input name="title_<?php echo $imageID ?>" id="title_<?php echo $imageID ?>" type="text" value="<?php printImageTitle(); ?>" />
                                    </div>
                        		    <div>
                        		        <label for="description_<?php echo $imageID ?>">Description:</label>
                        		        <input name="description_<?php echo $imageID ?>" id="description_<?php echo $imageID ?>" type="text" value="<?php printImageDesc(); ?>" />
                        		    </div>
                        		    <p>Date: <?php printImageDate(); ?></p>
                        		    <p>Album:<?php echo $albumLinkText; ?></p>
                        		    
                        		    <input name="filename_<?php echo $imageID ?>" type="hidden" value="<?php echo $_zp_current_image->getFileName() ?>" />
                        		    <input name="folder_<?php echo $imageID ?>" type="hidden" value="<?php echo getAlbumFolderForPhotostreamImage() ?>" />
                        		    <input name="originalTitle_<?php echo $imageID ?>" type="hidden" value="<?php printImageTitle(); ?>" />
                        		    <input name="originalDescription_<?php echo $imageID ?>" type="hidden" value="<?php printImageDesc(); ?>" />
                            	</td>
                        	</tr>
						<?php
							endwhile;
						?>
            				<tr>
								<td colspan="2">
									<p class="buttons">
										<a href="/zp-core/admin.php">
											<img src="../../zp-core/images/arrow_left_blue_round.png" alt="">
											<strong>Back</strong>
										</a>
										<button type="submit" name="save" value="Save" >
											<img src="../../zp-core/images/pass.png" alt="">
											<strong>Save</strong>
										</button>
									</p>
            				    </td>
            				</tr>
            				<tr>
            				    <td colspan="2" class="bordered" id="imagenavb">
            				        <?php adminPageNav(getBulkCaptionCurrentPage(), getTotalPhotostreamPages(), '/plugins/bulk-captions/', '?order=' . getBulkCaptionOrder()); ?>
            				    </td>
            				</tr>
            			</table>
						<?php } else { ?>
                        <div>
							<p class="buttons">
								<a href="/zp-core/admin.php">
									<img src="../../zp-core/images/arrow_left_blue_round.png" alt="">
									<strong>Back</strong>
								</a>
							</p>
        				</div>
						<?php } ?>
        				<br class="clearall">
					</div>
				</form>
			</div><!-- content -->
		</div><!-- content -->
	</div><!-- main -->
	<?php printAdminFooter(); ?>
</body>
<?php
echo "</html>";
?>