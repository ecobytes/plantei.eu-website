<?php namespace Caravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Contracts\Bus\SelfHandling;

class exportTranslations extends Command implements SelfHandling {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'translations:export';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Output all translations to csv';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$headers = [
			"section",
			"pt",
			"en"
		];

		$sections = [
			"seedbank::messages",
			"seedbank::menu",
			"seedbank::validation",
			"seedbank::buttons",
			"seedbank::forms",
			"authentication::confirmationemail",
			"authentication::messages",
			"authentication::validation",
			"projectpresentation::messages",
			"buttons",
			"validation",
			"footer",
			"pagination",
			"passwords"
		];

		$lines = [];
		$allLangsArray = [];

		foreach (config('app.availableLanguages') as $lang) {
			\App::setLocale($lang);
			foreach ($sections as $section) {
				$t = \Lang::get($section);
				ksort($t);
				$allLangsArray[$section][$lang] = $t;
			}
		}

    $langCount = 0;

		foreach ($allLangsArray as $section => $langs) {
			foreach ($langs as $l){
				if ($lang == 'pt') {
					$langIndex = 0;
				} else {
					$langIndex = 1;
				}
				foreach ($l as $key => $value){
					if (is_array($value)){
						if ($key == 'custom'){
							continue;
						}
						foreach ($value as $k => $v) {
							$lines[$section . "." . $key . "." . $k][] = $v;
						}
					} else {
						$lines[$section . "." . $key][] = $value;
					}
				}
			}
		}

		foreach ($lines as $key => $value) {
			try {
				echo '"' . $key . '"|' . '"' . join('"|"', $value) . '"' . "\n";
			}
			catch (\Exception $e ) {
				echo "--------> " . $key . " <----------------\n";
				echo var_dump($value);
				echo "\n";

			};
		}


		/* return;
		$paths = [''];
		$modules = scandir(base_path().'/modules');
		foreach ($modules as $key => $module) {
			if($module != '.' && $module != '..'){
				array_push($paths, '/modules/'.$module);
			}
		}
		foreach ($paths as $key => $path) {
			echo $path.'/resources/lang'."\n";
			$base = base_path().$path.'/resources/lang';
			if(is_dir($base)){
				foreach (scandir($base) as $key => $langDir) {
					if ($langDir != '.' && $langDir != '..' && $langDir != 'vendor'){
						if (is_dir($base.'/'.$langDir)) {
							$this->info($langDir);
							$translationFiles = scandir($base.'/'.$langDir);
							foreach ($translationFiles as $file) {
								if ($file != '.' && $file != '..'){
									$this->info(' '.$file);
									$translationArray = include $base.'/'.$langDir.'/'.$file;
										$this->outputArray($translationArray, 2);
									echo "\n";
								}
							}
						}
	    		}
				}
			}
		}
	}

	private function outputArray($arr, $level){
    $filecontent = "<?php\nreturn [\n";
		foreach ($arr as $key => $value) {
			if(is_array($value)){
				$this->info($key. ' => ');
				$this->outputArray($value, $level+1);
			}else {
				$spaces = '';
				for ($i = $level; $i > 0; $i--){
					$spaces .= ' ';
				}
				$this->info($spaces.$key .' => '.$value);
			}
    }
    $this->info($filecontent);
		*/

	}

}
