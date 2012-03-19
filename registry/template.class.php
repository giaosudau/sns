<?php
class Template {
	/*
	 * contructor
	 */

	public function __contruct(Registry $registry) {
		$this -> registry = $registry;
		include (FRAMEWORK_PATH . '/registry/page.class.php');
		$this -> page = new Page($this -> registry);

	}

	/*
	 * Set content of page based on of templates
	 *
	 */
	public function buildFromTemplates() {
		$bits = func_get_arg();
		$content = "";
		foreach ($bits as $bit) {
			if (strpos($bit, 'views/') === FALSE) {
				$bit = 'views/' . $this -> registry -> getSetting('view') . '/templates/' . $bit;

			}
			if (file_exists($bit) == TRUE) {
				$content .= file_get_contents($bit);

			}
			$this -> page -> setContent($content);
		}
	}

	/*
	 * Add templates
	 */
	public function addTemplateBit($tag, $bit) {
		if (strpos($bit, 'views/') === FALSE) {
			$bit = 'views/' . $this -> registry -> getSettings('view') . '/templates/' . $bit;
		}
		$this -> page -> addTemplateBit($tag, $bit);
	}

	/*
	 * Replace templates
	 */
	public function replaceBits() {
		$this -> page -> getBits();
		// loop through template bits
		foreach ($bits as $tag => $template) {
			$templateContent = file_get_contents($template);
			$newContent = str_replace('{' . $tag . '}', $templateContent, $this -> page -> getContent());
		}
		$this -> page -> setContent($newContent);
	}

	/*
	 * Replace Tag with our Content
	 * The $pp parameter indicates whether we are processing "Post Parse Tags";
	 *
	 */
	private function replaceTags($pp = FALSE) {
		// get tags in the page
		if ($pp == FALSE) {
			$tags = $this -> page -> getTags();
		} else {
			$tag = $this -> page -> getPPTags();
		}
		// go through them all
		foreach ($tags as $tag => $data) {
			if (is_array($data)) {
				if ($data[0] == 'SQL') {
					$this -> replaceDBTags($tag, $data[1]);

				} elseif ($data[0] == "DATA") {
					$this -> replaceDataTags($tag, $data[1]);
				}
			} else {
				// replace content
				$newContent = str_replace('{' . $tags . '}', $data, $this -> page -> getContent());
				// update content
				$this -> page -> setContent($newContent);
			}
		}
	}

	public function replaceDBTags($tag, $cacheId) {
		$block = '';
		$blockOld = $this -> page -> getBlock($tag);
		//
		while ($tags = $this -> registry -> getObject('db') -> resultFromCache($cacheId)) {
			$blockNew = $blockOld;
			// Check any additional parsing data
			if (in_array($tag, $apdkey)) {
				foreach ($tags as $ntag => $data) {
					$blockNew = str_replace('{' . $ntag . '}', $data, $blockNew);
					if (array_key_exists($ntag, $apd[$tag])) {
						$extra = $apd[$tag][$ntag];
						if ($data == $extra['condition']) {
							$blockNew = str_replace('{' . $extra['tag'] . '}', $extra['data'], $blockNew);

						} else {
							$blockNew = str_replace('{' . $extra['tag'] . '}', '', $blockNew);
						}
					}

				}

			} else {
				foreach ($tag as $ntag => $data) {
					$blockNew = str_replace('{' . $ntag . '}', $data, $blockNew);
				}
			}
			$block .= $blockNew;
		}
		$pageContent = $this -> page -> getContent();
		$newContent = str_replace('<!-- START ' . $tag . ' -->' . $blockOld . '<!-- END', $block, $pageContent);
		$this -> page -> setContent($newContent);

	}

	/*
	 * Replace data Tags with data from cache
	 * @param String $tag defining area of content
	 * @param int $cacheId data cache iq
	 */
	public function replaceDataTags($tag, $cacheId) {
		$blockOld = $this -> page -> getBlock($tag);
		$block = '';
		$tags = $this -> registry -> getObject('db') -> dataFromCache($cacheId);
		foreach ($tags as $key => $tagsdata) {
			$blockNew = $blockOld;
			foreach ($tagsdata as $taga => $data) {
				$blockNew = str_replace('{' . $taga . '}', $data, $blockNew);
			}
			$block .= $blockNew;
		}
		$pageContent = $this -> page -> getContent();
		$newContent = str_replace('<!-- START ' . $tag . ' -->' . $blockOld . '<!-- END ' . $tag . ' -->', $block, $pageContent);
		$this -> page -> setContent($newContent);
	}

	/*
	 * Convert array data into some tags
	 */
	public function dataToTags($data, $prefix) {
		foreach ($data as $key => $content) {
			$this -> page -> addTag($prefix . $key, $content);

		}
	}

	/*
	 * Insert Title into view
	 */
	public function parseTitle() {
		$newContent = str_replace('<title>', '<title>' . $this -> page -> getTitle(), $this -> page -> getContent());
		$this -> page -> setContent($newContent);
	}

	/*
	 * Parse the page object into some output
	 */
	public function parseOutput() {
		$this -> replaceBits();
		$this -> replaceTags(FALSE);
		$this -> replaceBits();
		$this -> replaceTags(TRUE);
		$this -> parseTitle();
	}

}
?>