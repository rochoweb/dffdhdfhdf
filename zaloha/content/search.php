<?php
	require_once ('./functions/search.php');

	if ( isset( $_GET["q"] ) )
		$default = $SE->action("global-search", $_GET["q"]);
	else {
		$default & $default["r"] = "";
	}
		
	$defval = isset($_GET["q"]) ? $_GET["q"] : "";

	$button = '<button type="submit" class="subs-input-send is">'.$GD->text(158).'</button>';

	echo '
		<div class="user-forms search-form">
			<div class="utools-head">
				<div> <span><h1>'.$GD->text(75).'</h1></span> </div>
			</div>

			<form method="get" action="'.$GD->link(4).'/" class="search-submit" itemprop="potentialAction" itemscope itemtype="http://schema.org/SearchAction">
				<meta itemprop="target" content="'.$GD->link(4).'/{q}/"/>
				<div class="searchribbon">
					
					<div class="sribb">
						<div class="sribb-left"></div>
						'.$GD->generate_inputs(502, true, "", $button, true, $defval).'
						<div class="sribb-right"></div>
					</div>
					
				</div>
			</form>
		
			<div class="search-result">'.$default["r"].'</div>
		</div>


		'.$GI->gen_itemhistory().'
	';
