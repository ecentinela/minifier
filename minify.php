<?php

Class minify {

	/*
	*/
	static function it($path, $options = array()) {
		$only_javascripts = in_array('javascripts', $options);
		$only_stylesheets = in_array('stylesheets', $options);
		$quiet = in_array('quiet', $options);
		$pretend = in_array('pretend', $options);
		$recursive = in_array('recursive', $options);
		$combine = in_array('combine', $options);

		// is the path is a folder, we get all files on these folder
		$dir = is_dir($path);
		$fn = $dir ? 'folder' : 'file';

		// arrays to store contents for combining files later
		$all_content = array('js' => array(), 'css' => array());
		$all_sizes = array('js' => 0, 'css' => 0);

		// call the method for compress
		call_user_func_array('minify::' . $fn, array($path, '/(?<!\.min)\.(js|css)$/', $quiet, $pretend, $recursive, $combine, &$all_content, &$all_sizes));

		// combine the files if the combine option is present and a dir is passed
		if ($dir && $combine) {
			$path .= substr($path, -1) == DIRECTORY_SEPARATOR ? '' : DIRECTORY_SEPARATOR;

			// javascripts are requested
			if (!$only_stylesheets && $all_sizes['js'] > 0) {
				$content = implode("\n", $all_content['js']);
				$new_size = strlen($content);

				file_put_contents($path . 'all.min.js', $content);

				echo "  combining javascript files\n";
				echo str_pad("  " . $all_sizes['js'] . " -> " . $new_size, 26, " ") . " = " . round($new_size * 100 / $all_sizes['js']) . "%\n";
			}

			// stylesheets are requested
			if (!$only_javascripts && $all_sizes['css'] > 0) {
				$content = implode("\n", $all_content['css']);
				$new_size = strlen($content);

				file_put_contents($path . 'all.min.css', $content);

				echo "  combining stylesheet files\n";
				echo str_pad("  " . $all_sizes['css'] . " -> " . $new_size, 26, " ") . " = " . round($new_size * 100 / $all_sizes['css']) . "%\n";
			}
		}
	}

	/*
	*/
	private static function folder($path, $regexp, $quiet, $pretend, $recursive, $combine, &$all_content, &$all_sizes) {
		// loop through all files on the folder to get images
		$it = new DirectoryIterator($path);

		foreach ($it as $file)
			// ignore the dot file
			if (!$file->isDot()) {
				$path = $file->getPathname();

				// if it's a folder, scan it too
				if ($file->isDir()) {
					if ($recursive)
						self::folder($path, $regexp, $quiet, $pretend, $recursive, $combine, $all_content, $all_sizes);
				}
				// compress the file
				elseif (preg_match($regexp, $path)) {
					self::file($path, $regexp, $quiet, $pretend, $recursive, $combine, $all_content, $all_sizes);

					if (!$quiet)
						echo "\n";
				}
			}
	}

	/*
	*/
	private static function file($path, $regexp, $quiet, $pretend, $recursive, $combine, &$all_content, &$all_sizes) {
		// check that the file exists
		if (!file_exists($path))
			throw new Exception('Invalid file path: ' . $path);
		// check it is a valid field
		elseif (preg_match($regexp, $path, $info)) {
			if (!$quiet)
				echo "  minifing " . $path . "\n";

			// execute yuicompressor
			$content = exec('java -jar yuicompressor.jar ' . $path);

			// new file size
			$new_size = strlen($content);

			// old file size
			$old_size = filesize($path);

			// type of file
			$extension = $info[1];

			if (!$quiet)
				echo str_pad("  " . $old_size . " -> " . $new_size, 26, " ") . " = " . round($new_size * 100 / $old_size) . "%\n";

			// if combine, store the result
			if ($combine) {
				$all_content[$extension][] = $content;

				$all_sizes[$extension] += $old_size;

				return true;
			}

			if ($pretend)
				return true;

			// get the path for the new file
			$new_path = substr($path, 0, -strlen($extension)) . 'min.' . $extension;

			return file_put_contents($new_path, $content);
		}
	}

}

?>