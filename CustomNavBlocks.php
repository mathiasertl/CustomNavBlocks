<?php
# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if (!defined('MEDIAWIKI')) {
	echo <<<EOT
INSTRUCTIONS
EOT;
	exit( 1 );
}

$wgHooks['SkinTemplateOutputPageBeforeExec'][] = 'addCustomNavBlocks';
$wgExtensionCredits['other'][] = array (
	'name' => 'CustomNavBlocks',
	'description' => 'Better customization of your sidebar',
	'version' => '2.0.0-1.13.0',
	'author' => 'Mathias Ertl',
	'url' => 'http://pluto.htu.tuwien.ac.at/devel_wiki/CustomNavBlocks',
);

function addCustomNavBlocks( $skin, $tpl ) {
	$parserOptions = new ParserOptions();
	global $wgParser;
	
	$CustomNavBlocksRaw = $tpl->translator->translate( 'CustomNavBlocks' );
	$blocks = explode( "\n", $CustomNavBlocksRaw );
	$sidebar = array();
	
	foreach ($blocks as $block) {
		$tmp = explode( '|', $block );

		# return false if a line in MediaWiki:CustomNavBlocks has more than one "|"
		if ( count( $tmp ) != 2 )
			return false;

		# some shortcuts
		$definition = $tmp[0];
		$blockTitle = $tmp[1];

		# first, we need a title object:
		$title = Title::newFromText( $definition, NS_MEDIAWIKI );

		# return false if a page defined by MediaWiki:CustomNavBlocks doesn't exist:
		if ( ! $title->exists() )
			return false;

		# get article and content:
		$article = new Article( $title );
		$article->loadContent();
		$content = $article->getContent();

		# parse the mediawiki-syntax into html:
		$content = $wgParser->preprocess( $content, $title, $parserOptions );
		$parserOutput = $wgParser->parse( $content, $title, $parserOptions );
		$html = $parserOutput->getText();

		# make a sidebar block:
		$sidebar["$blockTitle"] = $html;
	}

	# set sidebar to new thing:
	$tpl->set( 'sidebar', $sidebar );
	return true;
}

?>
