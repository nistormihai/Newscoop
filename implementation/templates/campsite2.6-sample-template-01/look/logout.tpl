<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Campsite Template/01</title>
<link rel="stylesheet" type="text/css" href="/look/01-style.css">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>

<body topmargin="5" leftmargin="5" bgcolor="#FFFFFF">

<table width="747" cellpadding="0" cellspacing="0" border="0">

  <!--main baner-->
   <!-- add some banner rotator here -->
  
  <!--end main baner-->
  
  <!--header-->
  
  <tr> 
    <td colspan="5" height="69">
	  <!** include header.tpl></td>
  </tr>
  
  <!-- end header-->
  
  <!--main index-->
  
  <tr> 
    <td colspan="5" valign="top"> 
	  <!** include header-01.tpl></td>
  </tr>
  
  <!--end main index-->
  
  <tr>
    <td width="131" valign="top">
	
  <!--index left-->
  
           <!** include menu.tpl><!--end index left-->
	  
    </td>
	<td width="8"></td>
    <td width="467" valign="top">
<!** local>
<!** section off>
<META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserId=; path=/">
<META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserKey=; path=/">
<META http-equiv="refresh" content="5;url=<!** uri template home.tpl>">
<p class="tekst">You have been logged out. Home page will be atuomaticly loaded. Please wait...<br><br>
If loading fail click <a class="naslov" href="<!** uri template home.tpl>">here.</a>.
<!** endlocal>
<!--end main middle--></td>
    <td width="8"></td>
    <td width="133" valign="top" bgcolor="#d3e5f1"> 
	
    <!--main right--> 
	
        <!** include right.tpl></td>
  </tr>
  
  <!-- footer -->
  
  <tr>
    <td colspan="2"></td>
	<td align="center"><!** include banner.tpl></td>
	<td colspan="2"></td>
  </tr>
  <tr>
    <td colspan="5" height="25"></td>
  </tr>
  <tr>
    <td colspan="2"></td>
	<td align="center" style="padding: 3px 0px 3px 0px"><p class="footer">
<!** include footer.tpl></p></td>
	<td colspan="2"></td>
  </tr>
  <tr>
    <td colspan="5" align="center" style="padding: 3px 0px 3px 0px"><p class="footer">
<!** include footer-01.tpl></p></td>
  </tr>
  
</table>

</body>

</html>