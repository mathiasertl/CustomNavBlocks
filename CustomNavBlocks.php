<?php
# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if (!defined('MEDIAWIKI')) {
    echo <<<EOT
INSTRUCTIONS
EOT;
    exit(1);
}

//self executing anonymous function to prevent global scope assumptions
call_user_func( function() {
    
    $GLOBALS['wgHooks']['SkinTemplateOutputPageBeforeExec'][] = 'addCustomNavBlocks';
    $GLOBALS['wgExtensionCredits']['other'][] = array (
        'path' => __FILE__,
        'name' => 'CustomNavBlocks',
        'description' => 'Better customization of your sidebar',
        'version' => '2.2.2',
        'author' => 'Mathias Ertl, [http://www.luukpeters.nl Luuk Peters]',
        'url' => 'https://www.mediawiki.org/wiki/Extension:CustomNavBlocks',
    );
});

function addCustomNavBlocks($skin, $tpl) {
    global $wgParser, $wgCustomNavBlocksEnable;
    if (! $wgCustomNavBlocksEnable)
        return true;

    $parserOptions = new ParserOptions();

    $CustomNavBlocksRaw = $tpl->translator->translate('CustomNavBlocks');
    $CustomNavBlocksClean = trim(preg_replace(
        array('/<!--(.*)-->/s'), array(''), $CustomNavBlocksRaw));
    $blocks = explode("\n", $CustomNavBlocksClean);
    $sidebar = array();

    foreach ($blocks as $block) {
        $tmp = explode('|', $block);

        # silently ignore lines that have more than one '|':
        if (count($tmp) > 2 || count($tmp) < 1) {
            continue;
        }

        if (count($tmp) == 1 && isset($tpl->data['sidebar'][$block])) {
            # try to find default sidebar item
            $sidebar[$block] = $tpl->data['sidebar'][$block];
        } else {
            # some shortcuts
            $definition = $tmp[0];
            $blockTitle = $tmp[1] ?? "";

            # first, we need a title object:
            $title = Title::newFromText($definition, NS_MEDIAWIKI);
            if (is_null($title)) {
                continue;
            }

            # return false if a page defined by MediaWiki:CustomNavBlocks
            # doesn't exist:
            if (! $title->exists()) {
                if ($title->quickUserCan('edit')) {
                    /* make edit link */
                    $html = Linker::link(
                        $title, $title->getPrefixedText(), array(),
                        array('action' => 'edit'));
                } else {
                    $html = '';
                }
            } else {
                # get article and content:
                $content = $tpl->translator->translate("$definition");

                # parse the mediawiki-syntax into html:
                $content = $wgParser->preprocess(
                    $content, $title, $parserOptions);
                $parserOutput = $wgParser->parse(
                    $content, $title, $parserOptions);
                $html = $parserOutput->getText();
            }

            # make a sidebar block:
            $sidebar[$blockTitle] = $html;
        }
    }

    # set sidebar to new thing:
    $tpl->set('sidebar', $sidebar);
    return true;
}
