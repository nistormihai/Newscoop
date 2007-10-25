			<TABLE width="100%" style="border: 1px solid #EEEEEE;">
			<TR>
				<TD>
					<TABLE width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
					<TR>
						<TD align="left">
						<STRONG><?php putGS("Files"); ?></STRONG>
						</TD>
						<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('AddFile')) {  ?>
						<TD align="right">
							<IMG src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/add.png" border="0">
							<A href="javascript: void(0);" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "files/popup.php"); ?>', 'attach_file', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=500, height=400, top=200, left=100');"><?php putGS("Attach"); ?></A>
						</TD>
						<?php } ?>
					</TR>
					</TABLE>
				</TD>
			</TR>
			<?php
			foreach ($articleFiles as $file) {
				$fileEditUrl = "/$ADMIN/articles/files/edit.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_article_number=$f_article_number&f_attachment_id=".$file->getAttachmentId()."&f_language_id=$f_language_id&f_language_selected=$f_language_selected";
				$deleteUrl = "/$ADMIN/articles/files/do_del.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_article_number=$f_article_number&f_attachment_id=".$file->getAttachmentId()."&f_language_selected=$f_language_selected&f_language_id=$f_language_id";
				$downloadUrl = "/attachment/".basename($file->getStorageLocation())."?g_download=1";
				if (strstr($file->getMimeType(), "image/") && (strstr($_SERVER['HTTP_ACCEPT'], $file->getMimeType()) ||
										(strstr($_SERVER['HTTP_ACCEPT'], "*/*")))) {
				$previewUrl = "/attachment/".basename($file->getStorageLocation())."?g_show_in_browser=1";
				}
			?>
			<TR>
				<TD align="center" width="100%">
					<TABLE>
					<TR>
						<TD align="center" valign="top">
							<?php if ($f_edit_mode == "edit") { ?><a href="<?php p($fileEditUrl); ?>"><?php } p(wordwrap($file->getFileName(), "25", "<br>", true)); ?><?php if ($f_edit_mode == "edit") { ?></a><?php } ?><br><?php p(htmlspecialchars($file->getDescription($f_language_selected))); ?>
						</TD>
						<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('DeleteFile')) { ?>
						<TD>
							<A title="<?php putGS("Delete"); ?>" href="<?php p($deleteUrl); ?>" onclick="return confirm('<?php putGS("Are you sure you want to remove the file \\'$1\\' from the article?", camp_javascriptspecialchars($file->getFileName())); ?>');"><IMG src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/unlink.png" border="0" /></A><BR />
							<?php if (!empty($previewUrl)) { ?>
							<A title="<?php putGS("Preview"); ?>" href="javascript: void(0);" onclick="window.open('<?php echo $previewUrl; ?>', 'attach_file', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=500, height=400, top=200, left=100');"><IMG src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/preview-16x16.png" border="0" /></A>
							<?php } ?>
						</TD>
						<?php } ?>
					</TR>
					<TR>
						<TD align="center"><?php p(camp_format_bytes($file->getSizeInBytes())); ?> <A title="<?php putGS("Download"); ?>" href="<?php p($downloadUrl); ?>"><IMG src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/download.png" border="0" /></A></TD>
						<TD></TD>
					</TR>
					</TABLE>
				</TD>
			</TR>
			<?php } ?>
			</TABLE>
