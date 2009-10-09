<?php

function help() {
	echo "Compress a single javascript or stylesheet file or a whole folder.\n";
	echo "\n";
	echo "Usage:\n";
	echo "  php minifier.php /public [options]\n";
	echo "  php minifier.php /stylesheets/common.css [options]\n";
	echo "\n";
	echo "Options are:\n";
	echo str_pad("  -q, --quiet", 26, " ") . "no output\n";
	echo str_pad("  -p, --pretend", 26, " ") . "no changes are made\n";
	echo str_pad("  -r, --recursive", 26, " ") . "execute the action on all subdirectories\n";
	echo str_pad("  -js, --javascripts", 26, " ") . "compress only javascript files\n";
	echo str_pad("  -css, --stylesheets", 26, " ") . "compress only stylesheets files\n";
	echo str_pad("  -c, --combine", 26, " ") . "combines all files in one\n";
	echo str_pad("  -h, --help", 26, " ") . "show this\n";

	exit;
}

if ($_SERVER['argc'] == 1)
	help();

require_once 'minify.php';

$options = array();
$path = false;

$arguments = array_splice($_SERVER['argv'], 1);

foreach ($arguments as $arg) {
	$is_option = preg_match('/^-/', $arg);

	if ($is_option && !$path)
		help();

	switch ($arg) {
		case '--combine':
		case '-c':
			$options[] = 'combine';
			break;

		case '--quiet':
		case '-q':
			$options[] = 'quiet';
			break;

		case '--pretend':
		case '-p':
			$options[] = 'pretend';
			break;

		case '--recursive':
		case '-r':
			$options[] = 'recursive';
			break;

		case '--javascripts':
		case '-js':
			if (in_array('stylesheets', $options))
				help();

			$options[] = 'javascripts';

		case '--stylesheets':
		case '-css':
			if (in_array('javascripts', $options))
				help();

			$options[] = 'stylesheets';

		default:
			if ($is_option || $path)
				help();

			$path = $arg;
	}
}

minify::it($path, $options);

?>