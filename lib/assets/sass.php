<?php
/**
 *	SASS plugin for Sugar Asset manager
 *
 *	The contents of this file are subject to the terms of the GNU General
 *	Public License Version 3.0. You may not use this file except in
 *	compliance with the license. Any of the license terms and conditions
 *	can be waived if you get permission from the copyright holder.
 *
 *	Copyright (c) 2017-2018 ~ ikkez
 *	Christian Knuth <ikkez0n3@gmail.com>
 *
 *	@version: 1.0.1
 *	@date: 24.08.2017
 *
 **/
namespace Assets;

use \Leafo\ScssPhp\Compiler;

class Sass extends \Assets {

	function init() {
		// get existing instance
		$assets = \Assets::instance();

		// register sass handler
		/** @var \Base $f3 */
		$f3 = \Base::instance();

		$assets->filter('sass',function($collection) use($f3,$assets) {

			// check final path
			$public_path = $f3->get('ASSETS.public_path');
			if (!is_dir($public_path))
				mkdir($public_path,0777,true);
			foreach($collection as $i=>&$asset) {
				if ($asset['origin']=='inline' || $asset['origin'] == 'external')
					continue;
				$path = $asset['path'];
				if (is_file($path)
					&& preg_match('/.*\.s(a|c)ss(?=[?#].*|$)/i',$path)) {
					// proceed
					$path_parts = pathinfo($path);
					$filename = $path_parts['filename'].'.css';
					$watch=[];
					if (isset($asset['watch']))
						foreach ($f3->split($asset['watch']) as $scanPath)
							$watch+=glob($path_parts['dirname'].'/'.$scanPath);
					$watch[]=$path_parts['dirname'].'/'.$path_parts['basename'];
					if (!is_file($public_path.$filename) || (
							($cmtime=filemtime($public_path.$filename)) &&
							(bool) implode(array_map(function($path) use ($cmtime){
								return filemtime(realpath($path)) > $cmtime;
							},$watch))
						)) {
						$bak=setlocale(LC_NUMERIC, 0);
						setlocale(LC_NUMERIC, 'C');
						$scss = new Compiler();
						$scss->addImportPath($path_parts['dirname']);
						$css = $scss->compile($f3->read($path));
						$css = $assets->fixRelativePaths($css,$path_parts['dirname'].'/',$public_path);
						setlocale(LC_NUMERIC, $bak);
						$f3->write($public_path.$filename,$css);
					}
					$asset['path'] = $public_path.$filename;
					$asset['type'] = 'css';
				}
				unset($asset);
			}
			return $collection;
		});

		$assets->formatter('sass',function($asset) use($f3,$assets) {
			if ($asset['origin']=='inline')
				return sprintf('<style type="text/css">%s</style>',$asset['data']);
			$path = $asset['path'];
			unset($asset['path'],$asset['origin'],$asset['type'],
				$asset['exclude'],$asset['slot']);
			$params=$assets->resolveAttr($asset+array(
					'rel'=>'stylesheet',
					'type'=>'text/css',
					'href'=>$path,
				));
			return sprintf('<link%s/>',$params);
		});

		$f3->set('ASSETS.filter.sass','sass');
	}
}