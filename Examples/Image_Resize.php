<?php

class Image {
	
	public function resize(){
		
		$fileData = array(
			'options' => array(
				'style' => array(
					'top' => 0,
					'left' => 0,
					'width' => 0,
					'height' => 0
				),
				'thumb' => array(
					'style' => array(
						'top' => 0,
						'left' => 0,
						'width' => 0,
						'height' => 0
					)
				)
			)
		);
		
		try {
			
			/* The versatility in checking multi-dimensional arrays is profoundly intuitive! */
			$contract =  new Site_Contract();
			$contract->term('fileData', $fileData)->arraylist()
						   ->element('options')->arraylist()
			   					       ->element('style')->arraylist()
						   					 ->element('top')->natural()->end()
											 ->element('left')->natural()->end()
											 ->end()
								       ->element('thumb')->arraylist()
											 ->element('style')->arraylist()
						  							   ->element('top')->natural()->end()
													   ->element('left')->natural()->end()
													   ->end();
			$contract->metOrThrow();

		
		}
		catch(Exception $exception){
			
			echo $exception->term;
			
		}
		
	}
	
}
