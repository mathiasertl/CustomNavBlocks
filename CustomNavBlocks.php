<?php
/* this extension requires the CustomNavBlocks patch for MonoBook.php.
 * You can get it at:
 * 	http://svn.fsinf.at/mediawiki/patches/MonoBook.php/
 */
	
$text = $this->translator->translate( 'CustomNavBlocks' );
$customblocks = explode ("\n", $text);

while(list($iarg, $ival) = each($customblocks))	{
	echo "\n<div class=\"portlet\" id=\"" . substr($ival,0,strpos($ival,"|")) . "\">\n";
	echo "	<h5>" . substr($ival,strpos($ival,"|")+1,strlen($ival)) . "</h5>\n";
	echo "	<div class=\"pBody\">\n";
	
	echo "	" . $this->msgWiki(substr($ival,0,strpos($ival,"|")));
			
	echo "	</div>\n";
	echo "</div>\n\n";
}
echo "<br>\n";

?>
