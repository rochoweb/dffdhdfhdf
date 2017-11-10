<div id="makesomething-line" class="re">
	<div class="makediy">

		<div class="makediy-top"></div>

		<div class="makediy-content">
			<div class="container" id="page-editor">
				<?php echo '
				<div class="makediy-header" id="diy-header">
					'.$EDITOR->generate_diy_header().'
				</div>

				<div class="makediy-body diy-container">
					
					<div class="mdiy-body-h">
						
					</div>

					<div class="mdiy-body-b">
						<form>
							<div class="mdiy-body-c mdiy-body-cl">
								
								<div class="utools-content">
									<div class="utools-header">'.$GD->text(222).'</div>

									<div class="utools-body">

										<div class="addi-input">
											<div class="addi-head">
												<label for="additem-title"><span class="i-req">'.$GD->text(215).'</span></label>
											</div>
											
											<div class="addi-body"> 
												<input type="text" class="adi-text" id="additem-title" value="'.$EDITOR->get_data("title").'">

											</div>
										</div>

										<div class="addi-input">
											<div class="addi-head">
												<label for="additem-description"><span class="i-req">'.$GD->text(216).'</span></label>
											</div>
											
											<div class="addi-body"> 
												<textarea class="adi-textarea" id="additem-description">'.$EDITOR->get_data("description").'</textarea>
											</div>
										</div>
									</div>
								</div>

								<div class="utools-content">
									<div class="utools-header">'.$GD->text(261).'</div>

									<div class="utools-body">
										<div class="addi-group addig3">
											<div class="addi-input">
												<div class="addi-head">
													<label for="additem-height"><span>'.$GD->text(232).'</span></label>
												</div>
												
												<div class="addi-body add-i"> 
													<input type="text" class="adi-text" id="additem-height">
													<div class="adi-inf">mm</div>
												</div>
											</div>

											<div class="addi-empty"></div>

											<div class="addi-input">
												<div class="addi-head">
													<label for="additem-length"><span>'.$GD->text(233).'</span></label>
												</div>
												
												<div class="addi-body"> 
													<input type="text" class="adi-text" id="additem-length">
													<div class="adi-inf">mm</div>
												</div>
											</div>

											<div class="addi-empty"></div>

											<div class="addi-input">
												<div class="addi-head">
													<label for="additem-width"><span>'.$GD->text(234).'</span></label>
												</div>
												
												<div class="addi-body"> 
													<input type="text" class="adi-text" id="additem-width">
													<div class="adi-inf">mm</div>
												</div>
											</div>

											<div class="cleaner"></div>
										</div>

										<div class="addi-input">
											<div class="addi-head">
												<label for="additem-diffsize"><span>'.$GD->text(238).'</span></label>
											</div>
											
											<div class="addi-body"> 
												<input type="text" class="adi-text" id="additem-diffsize">
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="mdiy-body-c mdiy-body-cr">
								
								<div class="utools-content">
									<div class="utools-header">'.$GD->text(221).'</div>

									<div class="utools-body">

										<div class="addi-group addig2">
											<div class="addi-input">
												<div class="addi-head">
													<label for="additem-price"><span class="i-req">'.$GD->text(217).'</span></label>
												</div>
												
												<div class="addi-body add-i"> 
													<input type="text" class="adi-text" id="additem-price" value="'.$EDITOR->get_data("price").'">
													<div class="adi-inf">EUR</div>
												</div>
											</div>

											<div class="addi-empty"></div>

											<div class="addi-input">
												<div class="addi-head">
													<label for="additem-quantity"><span class="i-req">'.$GD->text(218).'</span></label>
												</div>
												
												<div class="addi-body"> 
													<input type="text" class="adi-text" id="additem-quantity">
													<div class="adi-inf">KS</div>
												</div>
											</div>

											<div class="cleaner"></div>
										</div>

										<div class="addi-input">
											<div class="addi-head">
												<label for="additem-availability"><span class="i-req">'.$GD->text(220).'</span></label>
											</div>
											
											<div class="addi-body"> 
												<input type="text" class="adi-text show-select" id="additem-availability" value="'.$EDITOR->get_data("availability").'" readonly>
												<div class="ss-icon i i32"></div>
												<div class="select-menu dH" id="item-avail">
													'.$GD->generate_availability().'
												</div>
											</div>

										</div>

										<div class="addi-input">
											<div class="addi-head">
												<label for="additem-public"><span class="i-req">'.$GD->text(262).'</span></label>
											</div>
											
											<div class="addi-body"> 
												<input type="text" class="adi-text show-select" id="additem-public" value="'.$EDITOR->get_data("public").'" readonly>
												<div class="ss-icon i i32"></div>
												<div class="select-menu dH" id="item-avail">
													'.$GD->generate_public().'
												</div>
											</div>

										</div>
									</div>
								</div>

								<div class="utools-content">
									<div class="utools-header">'.$GD->text(239).'</div>

									<div class="utools-body">
										'.$EDITOR->generate_categories().'

										<div class="addi-input selecat">
											<div class="addi-head">
												<label for="additem-keywords"><span class="i-req">'.$GD->text(243).'</span></label>
											</div>
											
											<div class="addi-with-select">
												<div class="addi-body"> 
													<input type="text" class="adi-text finddata-select" data-showtarget="found-tags" id="additem-keywords" placeholder="'.$GD->text(244).'">
												</div>
												<div class="addi-select">
													<a href="#" class="show-tags show-tar update-data et-a" data-showtarget="all-availtags" data-updatetarget="all-availtags" data-updateevent="additem-keywords" data-updateinfo="ALL">'.$GD->text(246).'</a>
												</div>

												<div class="select-menu addtoinput dH" id="found-tags" data-target="add-data-loc" data-input="additem-keywords">
													<div class="s-m-container finddata-select-re">
														'.$EDITOR->generate_keywords().'
													</div>
												</div>

												<div class="select-menu addtoinput dH" id="all-availtags" data-target="add-data-loc" data-input="additem-keywords"><div class="s-m-container finddata-select-re"></div></div>

												<div class="cleaner"></div>
											</div>

											<div class="addi-taglist"><ol class="add-data-loc" id="additem-tags">'.$EDITOR->generate_tags().'</ol></div>
										</div>
									</div>
								</div>
								
								
							</div>
						</form>
						<div class="cleaner"></div>
					</div>

					<div class="mdiy-body-footer">
						<!--<a href="#" class="add-step et-a" name="add-step">PRIDAŤ KROK <div class="i i106 bn-icon"></div></a>-->
						'.$EDITOR->generate_diy_buttons().'
					</div>';

					/*<div class="subs-input addi2">
										<input type="text" class="ss-text" id="additem-title">
										<label for="additem-title" class="labelD"><span>'.$GD->text(215).'</span></label>
										<div class="ss-icon"></div>
										<div class="ss-help tac"><div>Použite slovo/á, ktoré naznačia o akú vec ide.</div></div>

										<div class="resultbar"></div>
									</div>

									<div class="subs-input addi2textarea">
										<textarea class="ss-text ss-textarea" id="additem-description"></textarea>
										<label for="additem-description" class="labelD"><span>'.$GD->text(216).'</span></label>
										<div class="ss-icon"></div>
										
										<div class="resultbar"></div>
									</div>*/
					//'.$EDITOR->generate_diy_buttons().'
?>
				<!--
					<div class="mdiy-body-topmenu">
						<a href="#" class="all-steps et-a" name="all-steps"><div class="i i107 bn-icon"></div></a>
						<a href="#" class="add-step et-a" name="add-step">PRIDAŤ KROK <div class="i i106 bn-icon"></div></a>

						<a href="#" class="diy-settings et-a" name="diy-settings">NASTAVENIA <div class="i i108 bn-icon"></div></a>
						<div class="cleaner"></div>
					</div>

					<ul class="mdiy-body-steps" id="steps-sortable">

						<li class="mdiy-step">
							<div class="step-content">
								<div class="step-header" title="Presunuť potiahnutím">
									<div> <span><strong>UVOD</strong></span> </div>
								</div>

								<div class="step-body">
									<div class="step-half">
										<div class="step-files sf-empty toomuch-">
											<ul class="step-added-files"></ul>
											<span class="step-default-message"><strong>SEM PRESUŇTE OBRÁZKY</strong></span>
										</div>
									</div>

									<div class="step-half sh-break"></div>

									<div class="step-half">
										<div class="step-text sf-empty">
											<div class="step-edit-text">
												<button type="button" class="i i112 bn-icon" name="edit-text"></button>
											</div>
											<span class="step-default-message"><strong>PRIDAŤ POPIS</strong></span>
										</div>
									</div>

									<div class="cleaner"></div>
								</div>
								
							</div>	
						</li>

						<li class="mdiy-step">
							<div class="step-content">
								<div class="step-header">
									<div> <span><strong>KROK 1</strong></span> </div>
								</div>

								<div class="step-body">
									<div class="step-half">
										<div class="step-files sf-empty">
											<ul class="step-added-files"></ul>
											<span class="step-default-message"><strong>SEM PRESUŇTE OBRÁZKY</strong></span>
										</div>
									</div>

									<div class="step-half sh-break"></div>

									<div class="step-half">
										<div class="step-text sf-empty">
											<div class="step-edit-text">
												<button type="button" class="i i112 bn-icon" name="edit-text"></button>
											</div>
											<span class="step-default-message"><strong>PRIDAŤ POPIS</strong></span>
										</div>
									</div>

									<div class="cleaner"></div>
								</div>
								
							</div>	
						</li>

						<li class="mdiy-step">
							<div class="step-content">
								<div class="step-header">
									<div> <span><strong>KROK 1</strong></span> </div>
								</div>

								<div class="step-body">
									<div class="step-half">
										<div class="step-files sf-empty">
											<span class="step-default-message"><strong>SEM PRESUŇTE OBRÁZKY</strong></span>
										</div>
									</div>

									<div class="step-half sh-break"></div>

									<div class="step-half">
										<div class="step-text sf-empty">
											<div class="step-edit-text">
												<button type="button" class="i i112 bn-icon" name="edit-text"></button>
											</div>
											<span class="step-default-message"><strong>PRIDAŤ POPIS</strong></span>
										</div>
									</div>

									<div class="cleaner"></div>
								</div>
								
							</div>	
						</li>
					</ul>

					<div class="mdiy-body-footer">
						<a href="#" class="add-step et-a" name="add-step">PRIDAŤ KROK <div class="i i106 bn-icon"></div></a>

					</div>
				</div>-->
			</div>
		</div>

	</div>
</div>
