<?php

class EJCropper
{
	public $targ_w = 200;
	public $targ_h = 200;
	public $thumbPath;

	public function getCoordsFromPost()
	{
		$coords = array('x' => null, 'y' => null, 'h' => null, 'w' => null);
		foreach ($coords as $key => $value) {
			$coords[$key] = $_POST[$key];
		}
		return $coords;
	}

	public function crop($src, $dst, array $coords)
	{
		$image = Yii::app()->image->load($src);
		$image->crop($coords['w'], $coords['h'], $coords['y'], $coords['x'])->resize($this->targ_w, $this->targ_h);
		$image->save($dst);
		return $dst;
	}

}
