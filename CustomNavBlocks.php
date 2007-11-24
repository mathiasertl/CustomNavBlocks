<?php
/**
 * This extension requires the CustomNavBlocks patch for MonoBook.php.
 * You can get it at:
 * 	http://svn.fsinf.at/mediawiki/patches/MonoBook.php/
 */
	
$text = $this->translator->translate( 'CustomNavBlocks' );
$customblocks = explode ("\n", $text);

$wgExtensionCredits['other'][] = array(
	'name' => 'CustomNavBlocks',
	'description' => 'Allows a more flexible sidebar',
	'version' => 1.0-1.11.0,
	'author' => 'Mathias Ertl',
	'url' => 'http://pluto.htu.tuwien.ac.at/devel_wiki/index.php/CustomNavBlocks',
);

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
