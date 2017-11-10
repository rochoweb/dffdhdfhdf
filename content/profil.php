<?php
	
	echo '
	<div class="userInterfaceProfile">
		<div class="uip-left">
			<div class="uipl-content">
				'.$USERPROFIL->gen_userMenu().'
			</div>
		</div>
		
		<div class="uip-right">
			<div class="uipr-content">
				'.$USERPROFIL->gen_subuserMenu().'
			</div>

		</div>

		<div class="cleaner"></div>
	</div>


	';