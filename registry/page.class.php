<?php
class Page{
	// Page Title
	private $title;
	// template tags
	private $tags = array();
	// post tags
	private $postParseTags = array();
	// template bits 
	private $bits = array();
	// page content
	private $content = '';
	private $apd = array();
	/*
	 * Construct 
	 */
	function __contruct(Registry $registry){
		$this->registry = $registry;
	}
	/* Get page title 
	 * Return String
	 */
	public function getTitle(){
		return $this->title;
	}
	/*
	 * Set Page Title
	 */
	public function setTile($title){
		$this->title = $title;
	}
	/*
	 * Set Content
	 */
	public function setContent($content){
		$this->content = $content;
	}
	/* Add Tag with data
	 * key
	 * data
	 */
	public function addTags($key, $data){
		$this->tags[$key] = $data;
	}
	/*
	 * Get Tag associate with page
	 */
	public function getTags(){
		return $this->tags;
	}
	/*
	 * Remove Tag
	 */
	public function removeTags($key){
		unset($this->tags[$key]);
	}
	/*
	 * Get tags to be parsed after the first batch have been parsed
	 */
	public function getPPTags(){
		return $this->postParseTags;
	}
	/*
	 * Add PP Tags 
	 */
	public function addPPTags($key, $data){
		$this->postParseTags[$key] = $data;
	}
	/*
	 * Add Template Bit
	 */
	public function addTemplateBit($tag, $bit){
		$this->bits[$tag] = $bit;
	}
	/*
	 * Add additional parsing data
	 */
	public function additionalParsingData($block, $tags, $condition, $extratag, $data){
		$this->apd[$block] = array($tag => array('condition' => $condition,
								'tag' => $extratag, 'data' => $data));
	}
	/*
	 * Get template bits
	 */
	public function getBits(){
		return $this->bits;
	}
	public function getAdditionParsingData(){
		return $this->apd;
	}
	public function getContent(){
		return $this->content;
	}
	public function getBlock($tag){
		preg_match('#<!-- START , . $tag . , -->(.+?)<!-- END ,. $tag . , -->#si', $this->content, $tor);
		$tor = str_replace('<!-- START '. $tag . ' -->', '',$tor[0]);
		$tor = str_replace('<!-- END' . $tag . ' -->', $tor);
		return $tor;
	}
	public function getContentToPrint(){
		$this->content = pre_replace('#{form_(.+?)}#si', '', $this->content);
		$this->content = pre_replace('#{nbd_(.+?)}#si','', $this->content);
		$this->content = str_replace('</body>', '<!-- Generate by UIT Social Network --> </body>', $this->content);
		return $this->content;
	}
}

?>