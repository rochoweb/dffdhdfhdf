	

	<body class="<?= $CHR->PD->name; ?>" <?php if ( isset($_COOKIE["online"]) ) echo 'id="'.$_COOKIE["online"].'"'; ?> itemscope itemtype="http://schema.org/WebSite">

		<meta itemprop="url" content="<?= $GD->link(1); ?>" />

		<div id="MonaMade" lang="<?= $CHR->LANG; ?>">
			<!--<div class="mm-navigation">
				<div class="nav-content">
					
				</div>
			</div>-->

			<div class="mm-content"<?= $html_id; ?>>
				<div id="fixedHeader-line" class="re">
					<div class="head-content">
						<div class="container">
							<div class="logo-content">
								<a href="<?= $GD->link(1); ?>" class="mm-logo" title="<?= $GD->text(63); ?>"><span class="ii iLogo"></span></a>
							</div>

							<?= '
							<div class="small-menu">
								<a href="'.$GD->link(504).'"><span class="ii iDelivery"></span>'.$GD->text(731).'</a>
								<a href="'.$GD->link(500).'"><span class="ii iEmail"></span>'.$GD->text(737).'</a>
							</div>
							'; ?>

							<div class="right-content">

								<div class="right-menu">
									<div class="rm- br user-search">
										<?= '<a href="'.$GD->link(4).'" class="top-search ii iZoom fc-show searchIco" name="search" title="'.$GD->text(78).'"></a>'; ?>
									</div>

									<div class="rm- br user-login-pos">
										<?= $UI["menu1"]; ?>
									</div>

									<div class="rm- user-cart">
										<?= $BA->gen_basket(); ?>
									</div>

									<div class="cleaner"></div>
								</div>

								<div class="cleaner"></div>
							</div>

							<div class="cleaner"></div>
						</div>
					</div>

					<?php 
						if ( $CHR->PD->page != 14 && $CHR->PD->page != 18 ) 
							echo $GD->generate_mainmenu(); 
					?>

					<div class="mobile-statusbar">
						<button type="button" name=".mobile-statusbar" class="hideThing ii iExit" title="<?= $GD->text(39); ?>"></button>
						<div class="mstatus"></div>
					</div>

					<div id="up"></div>
				</div>

				<div class="lines"<?php if ( $CHR->PD->page == 100 || $CHR->PD->page == 101 || $CHR->PD->page == 102 || $CHR->PD->page == 103 || $CHR->PD->page == 14 ) echo 'id="l-nh"'; else if (  $CHR->PD->page == 12 ) echo 'id="l-ni"'; ?>>
					<?php
						
						switch ($CHR->PD->page) {
							case 5:
								$bd = '<div class="headers"><h1>'.mb_strtoupper($GD->text( $data->text ), "UTF8").'</h1></div>';
								break;
							
							default: $bd = ""; break;
						}

						if ( $CHR->PD->search_box == true ) {
							$button = '<button type="submit" class="subs-input-send is">'.$GD->text(158).'</button>';
							echo '
							<div id="top-line" class="re">
								<div class="container">
									<div class="topline-content">
										'.$bd.'
										<div class="index-search">
											<form method="get" action="'.$GD->link(4).'/" class="search-submit" itemprop="potentialAction" itemscope itemtype="http://schema.org/SearchAction">
												<meta itemprop="target" content="'.$GD->link(4).'/{q}/"/>
												'.$GD->generate_inputs(500, false, "", $button, true).'	
											</form>
										</div>

										<div class="cleaner"></div>
									</div>
								</div>
							</div>';
						}

					?>

					<?php
					if ( $CHR->PD->page == 12 || $CHR->PD->page == 14 )
						if ( file_exists($content) ) require_once($content); else echo "dev";
					else {
						echo '
					<div id="mid-line" class="re">
						<div class="container">
							<div class="body-content">
							';
								if ( file_exists($content) ) require_once($content); else echo "dev";

								echo '<div class="cleaner"></div>
							</div>
						</div>
					</div>';
					}
					?>

					<div id="bot-line" class="re">
						<div class="bot-body">
							<div class="container"></div>
						</div>
						<div class="bot-footer">
							<div class="container">

								<?= '
								<div class="bot-left">
									
									<div class="ulli">
										<ul>
											<li class="liheader">'.$GD->text(727).'</li>
											<li><a href="'.$GD->link(504).'">'.$GD->text(731).'</a></li>
											<li><a href="'.$GD->link(505).'">'.$GD->text(732).'</a></li>
										</ul>
									</div>
									
									<div class="ulli">
										<ul>
											<li class="liheader">'.$GD->text(728).'</li>
											<li><a href="'.$GD->link(501).'">'.$GD->text(735).'</a></li>
											<li><a href="'.$GD->link(507).'">'.$GD->text(736).'</a></li>
										</ul>
									</div>
									
									<div class="ulli">
										<ul>
											<li class="liheader">'.$GD->text(729).'</li>
											<li><a href="'.$GD->link(500).'">'.$GD->text(737).'</a></li>
										</ul>
									</div>

									<div class="cleaner"></div>
								</div>

								<div class="bot-right">
									

									<div class="ulli">
										<ul class="bottomsocials">
											<li class="liheader">'.$GD->text(730).'</li>
											<li><a href="'.SOCIAL_FCB.'" title="Facebook" class="fcb" target="_blank"><i class="fa fa-facebook-square" aria-hidden="true"></i></a></li>
											<li><a href="'.SOCIAL_INSTA.'" title="Instagram" class="insta" target="_blank"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
										</ul>
									</div>

									<div class="cleaner"></div>
								</div>
								';
								?>
								<div class="cleaner"></div>

								<div class="bot-center">
									<div class="footer-copyright">
										<p><i>&copy;</i> <span><?= '<a href="'.$GD->link(1).'"><span itemprop="name">MONAMADE</span>.SK</a>'; ?></span> SINCE 2017</p>
									</div>
								</div>

								<div class="bot-cookies">
									<?= sprintf($GD->text(772), '<a href="'.$GD->link(507).'">'.$GD->text(818).'</a>'); ?>
								</div>
							</div>
						</div>
					</div>

					<?= $GD->itemscope(1); ?>
				</div>


				<div class="bottom-fixed-menu">
					<?php 
						if ( $CHR->PD->page == 5 ) {
							echo '
							<button type="button" class="fim-button fml" data-target=".fim-c"><span class="ii iCats"></span>FILTER</button>';

							echo '
							<div class="fim-c">
								<div class="filters">
									'.$filters[1].'
									
									'.$filters[0].'
											
									'.$filters[2].'

									'.$filters[3].'
								</div>
								<div class="fim-c-punt"></div>
							</div>
							';
						}
					?>


					<button type="button" class="fim-button fmr" data-target=".fim-menu"><span class="ii iMenu"></span>MENU</button>

					<div class="fim-menu">
						<div class="bfm-move bfmleft"><span class="f-up ii iLeft"></span></div>
						<?php 
							if ( $CHR->PD->page != 14 && $CHR->PD->page != 18 ) 
								echo $GD->generate_mainmenu("navimenu2"); 
						?>
						<div class="bfm-move bfmright"><span class="f-up ii iRight"></span></div>
					</div>


				</div>
			</div>
			<!--<div class="cleaner"></div>-->
		</div>

		<div class="fullscreen">

			<div class="fc-content">

				<div class="fcTemp dSize">
					<i class="fa fa-cog fa-spin fa-fw fc-load"></i>
				</div>

				<div class="fcR dSize">
					<button type="button" class="fc-hide ii iExit" title="<?= $GD->text(39); ?>"></button>

					<div class="fcR-content"></div>

					<div class="utools-loading" id="fc-loading">
						<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>
					</div>
				</div>
			</div>
		</div>

		<div class="fullscreen_message">

			<div class="fc-content">

				<div class="fcTemp dSize">
					<i class="fa fa-cog fa-spin fa-fw fc-load"></i>
				</div>

				<div>
					<div class="fcR-contentt"></div>

					<div class="utools-loading" id="fc-loading">
						<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>
					</div>
				</div>
			</div>
		</div>

		<div class="fullscreenn" id="fullscreen-fatalerror">
			<div class="container">
				<div class="fcc-content">
					<div class="fcc-body">
						<div class="ferr-icon"></div>
						<div class="ferr-message">
							fatal error
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php

		if ( $CHR->PD->page == 16 )
			echo '
			<div id="imgzoom" class="aoe">
				<div class="imgzcont">
					<div class="imgz-big"></div>
					<div class="imgz-list"></div>
				</div>
			</div>
			';
		?>

		<div class="utools-loading" id="main-loading">
			
		</div>

		<div class="fc-message">
			<div class="fc-message-content"></div>
			<button type="button" name=".fc-message" class="hideThing ii iExit" title="<?= $GD->text(39); ?>"></button>
		</div>

		<?php
		echo '
		<script type="text/javascript" src="'.$HEADER["link"]["js-jquery"].'"></script>
		<script type="text/javascript" src="'.$HEADER["link"]["js-plugin-moment"].'"></script>

		<script type="text/javascript" src="'.$HEADER["link"]["js-plugin-mobile"].'"></script>
		';

		//<script type="text/javascript" src="'.$HEADER["link"]["js-plugin-timestamp"].'"></script>
	


		$jsui = '<script type="text/javascript" src="'.$HEADER["link"]["js-ui"].'"></script>';

		switch ( $CHR->PD->page ) {
			case 12:
				/*echo $jsui.'
		<script type="text/javascript" src="'.$HEADER["link"]["js-diyeditor"].'"></script>';
				break;*/
			case 14:
			//case 17:
			/*case 102:
				echo $jsui.'
		<script type="text/javascript" src="'.$HEADER["link"]["js-shopie"].'"></script>';
				break;*/

			case 17:
			case 18:
				echo '
		<script type="text/javascript" src="'.$HEADER["link"]["js-basket"].'"></script>';
				break;
		}

		echo '
		<script type="text/javascript" src="'.$HEADER["link"]["js-main"].'"></script>';

		if( file_exists($STRUCTURE["maps"]) ) require($STRUCTURE["maps"]);

		if ( $CHR->PD->page == 18 || $CHR->PD->page == 500 ) {
				echo '
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAgKQkg5AyEVrCh8dO9phFIBvm06N2snyw&callback=initialize" async defer></script>
				';
			}

		if ( $CHR->PD->page == 16 ) {
				echo '
		<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js" async defer></script>
		<script type="text/javascript" src="https://apis.google.com/js/platform.js" async defer></script>
		<script>(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/sk_SK/sdk.js#xfbml=1&version=v2.10&appId=257417734667564";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, "script", "facebook-jssdk"));</script>
				';
			}

		echo '
		
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-26278640-4"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag("js", new Date());

		  gtag("config", "UA-26278640-4");
		</script>
		';
		?>
</body>
