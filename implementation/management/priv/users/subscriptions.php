<?php

check_basic_access($_REQUEST);
if (!$User->hasPermission("ManageSubscriptions") || !isset($editUser) || gettype($editUser) != 'object' || $editUser->getUserName() == '') {
	camp_html_display_error(getGS('No such user account.'),$_SERVER['REQUEST_URI']);
	exit;
}

?>
<table border="0" cellspacing="1" cellpadding="3" width="100%" class="table_list">
<tr class="table_list_header">
	<td colspan="5" align="left">
		<table border="0" cellspacing="0" cellpadding="0" width="100%"><tr class="table_list_header">
			<td align="left"><?php putGS("Subscriptions"); ?></td>
			<td align="right" valign="center" nowrap>
				<?php $addURI = "/$ADMIN/users/subscriptions/add.php?User=".$editUser->getId(); ?>
				<a href="<?php echo $addURI; ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A>
				<a href="<?php echo $addURI; ?>"><B><?php putGS("Add new"); ?></B></A>
			</td>
		</tr></table>
	</td>
</tr>
<?php

query ("SELECT * FROM Subscriptions WHERE IdUser=" . $editUser->getId() . " ORDER BY Id DESC", 'q_subs');
if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$color=0;
	?>
	<TR class="table_list_header">
		<TD ALIGN="LEFT" VALIGN="TOP" nowrap><B><?php  putGS("Publication"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" nowrap><B><?php  putGS("Left to pay"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" nowrap><B><?php  putGS("Type"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Active"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Delete"); ?></B></TD>
	</TR>
<?php 
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($q_subs);
?>	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<TD >
<?php 
		query ("SELECT Name FROM Publications WHERE Id=".getSVar($q_subs,'IdPublication'), 'q_pub');
		fetchRow($q_pub);
?>			<A HREF="/<?php echo $ADMIN; ?>/users/subscriptions/sections/?Subs=<?php pgetUVar($q_subs,'Id'); ?>&Pub=<?php pgetUVar($q_subs,'IdPublication'); ?>&User=<?php  echo $editUser->getId(); ?>"><?php pgetHVar($q_pub,'Name'); ?></A>&nbsp;
		</TD>
		<TD >
			<A HREF="/<?php echo $ADMIN; ?>/users/subscriptions/topay.php?User=<?php echo $editUser->getId(); ?>&Subs=<?php pgetUVar($q_subs,'Id'); ?>">
			<?php  pgetHVar($q_subs,'ToPay').' '.pgetHVar($q_subs,'Currency'); ?></A>
		</TD>
		<TD >
			<?php  
			$sType = getHVar($q_subs,'Type');
			if ($sType == 'T')
				putGS("Trial");
			else
				putGS("Paid");
			?>
		</TD>
		<TD ALIGN="CENTER">
		<?php if (getVar($q_subs,'Active') == "Y") { ?>
			<a href="/<?php echo $ADMIN; ?>/users/subscriptions/do_status.php?User=<?php echo $editUser->getId(); ?>&Subs=<?php pgetUVar($q_subs,'Id'); ?>" onclick="return confirm('<?php putGS('Are you sure you want to deactivate the subscription?'); ?>');"><?php putGS('Yes'); ?></a>
		<?php } else { ?>
			<a href="/<?php echo $ADMIN; ?>/users/subscriptions/do_status.php?User=<?php echo $editUser->getId(); ?>&Subs=<?php pgetUVar($q_subs,'Id'); ?>" onclick="return confirm('<?php putGS('Are you sure you want to activate the subscription?'); ?>');"><?php putGS('No');?></a>
		<?php } ?>
		</TD>
		<TD ALIGN="CENTER">
			<A HREF="/<?php echo $ADMIN; ?>/users/subscriptions/do_del.php?User=<?php echo $editUser->getId(); ?>&Subs=<?php pgetUVar($q_subs,'Id'); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the subscription to the publication $1?', getHVar($q_pub,'Name')); ?>');">
			<IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php putGS('Delete subscriptions to $1',getHVar($q_pub,'Name') ); ?>" TITLE="<?php  putGS('Delete subscriptions to $1',getHVar($q_pub,'Name') ); ?>"></A>
		</TD>
	</TR>
<?php 
}
?>
<?php  } else { ?>
<tr class="list_row_odd"><td colspan="5"><?php  putGS('No subscriptions.'); ?></td></tr>
<?php  } ?>
</table>
<br>
