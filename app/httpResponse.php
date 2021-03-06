<?php
	namespace Core;

	class httpResponse extends Singleton  {
		private $page;
		private $header;
		private $no_render;
		private $file;

		protected function __construct() {
			$this->headers = array();
			$this->page = new Page();
			$this->file = null;
			$this->no_render = false; //si true => pas bessoin de render
		}

		public function addHeader($string = null) {
			if($string != null) {$this->headers[] = $string;}
			return $this;
		}

		public function redirect($url) {
			$this->headers[] = "Location: " . $url;
			$this->no_render = true;
			return $this;
		}

		public function setCode($int = null) {
			$this->page = new CodePage($int);
			return $this;
		}

		public function setPage($page) {
			$this->page = $page;
			return $this;
		}

		public function getPage() {
			return $this->page;
		}

		public function setFile($name, $ext, $path, $delete = false) {
			if (!file_exists($path)) {
				return false;
			}
			$this->no_render = true;
		    $this->addHeader('Content-Description: File Transfer')
		    	 ->addHeader('Content-Type: application/octet-stream')
		    	 ->addHeader('Content-Disposition: attachment; filename="'. $name . "." . $ext .'"')
		    	 ->addHeader('Expires: 0')
		    	 ->addHeader('Cache-Control: must-revalidate')
		    	 ->addHeader('Pragma: public')
		    	 ->addHeader('Content-Length: ' . filesize($path))
		    ;
		   	$this->file = array($path, $delete);
		   	return true;
		}

		private function flushHeader() {
			foreach ($this->headers as $header) {
				header($header);
			}
			return $this;
		}

		public function send() {
			$cookies = new CookieController();
			$cookies->flushCookies();
			
			$this->flushHeader();

			if ($this->file !== null) {
		    	readfile($this->file[0]);
		    	if ($this->file[1]) {unlink($this->file[0]);}
		    	return $this;
			}

			if (!$this->no_render) {$this->page->render();} //Si non redirigé => render
			
			return $this;
		}
	}
?>