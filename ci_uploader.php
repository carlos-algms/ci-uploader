<?php

class Ci_uploader
{
	protected $CI = null;
	protected $upload = null;
	
	protected $config = array(
		'upload_path'	=> '',
		'allowed_types'	=> null,
		'overwrite'		=> false,
		'file_name'		=> ''
	);

	public function __construct()
	{
		$this->CI =& get_instance();

		$this->CI->load->library('upload');
		$this->CI->load->helper('directory');

		$this->upload = $this->CI->upload;
	}


	public function upload()
	{
		$ret = FALSE;

		if( isset($_FILES[$this->getIdxFile()]) ) {
			$file =& $_FILES[$this->getIdxFile()];

			$this->config['upload_path'] = $this->getPath();

			if( $this->getAllowedTypes() ) {
				$this->config['allowed_types'] = implode('|', $this->getAllowedTypes());
			}

			if( $this->getFixedName() ) {
				$finalName = $this->getFixedName();
				$this->config['overwrite'] = false;

			} else {
				$file['name'] = strtolower( $file['name'] );

				$fileNamePieces = explode( '.', $this->normalyze( $file['name'] ) );

				$finalName = $fileNamePieces[0];
			}

			$finalName = substr($finalName, 0, $this->getNameMaxChars() );

			$this->config['file_name'] = $finalName;

			$this->upload->initialize( $this->config );

			$ret = $this->upload->do_upload( $this->getIdxFile() );

			$this->setDataUpload( $this->upload->data() );
			$this->setErrors( $this->upload->display_errors('','') );

		}

		return $ret;
	}

	private function normalyze( $str, $separator = '-' )
	{
		$charset = config_item('charset');
		//$separator = ($separator == 'underscore') ? '_' : '-';
		$str = strtolower( htmlentities($str, ENT_COMPAT, $charset) );
		$str = preg_replace('/&(.)(acute|cedil|circ|lig|grave|ring|tilde|uml);/', "$1", $str);
		$str = preg_replace('/([^a-z0-9\.]+)/', $separator, html_entity_decode($str, ENT_COMPAT, $charset));
		$str = trim($str, $separator);

		return $str;
	}


	private $idxFile			= 'file';
	private $path				= 'img_bd/temp';
	private $fixedName			= NULL;
	private $allowedTypes		= array('gif', 'jpg', 'png', 'jpeg');
	private $nameMaxChars		= 130;
	private $dataUpload			= array();
	private $errors				= '';


	public function getIdxFile()
	{
		return $this->idxFile;
	}

	public function setIdxFile($idxFile)
	{
		$this->idxFile = $idxFile;
		return $this;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function setPath($path)
	{
		$this->path = $path;
		return $this;
	}

	public function getFixedName()
	{
		return $this->fixedName;
	}

	public function setFixedName($fixedName)
	{
		$this->fixedName = $fixedName;
		return $this;
	}

	public function getAllowedTypes()
	{
		return $this->allowedTypes;
	}

	public function setAllowedTypes($allowedTypes)
	{
		$this->allowedTypes = $allowedTypes;
		return $this;
	}

	public function addAllowedType( $type )
	{
		if( !is_array( $type) ) {
			$type = array( $type );
		}

		$this->allowedTypes = array_unique( array_merge($this->allowedTypes, $type) );

		return $this;
	}

	public function getNameMaxChars()
	{
		return $this->nameMaxChars;
	}

	public function setNameMaxChars($nameMaxChars)
	{
		$this->nameMaxChars = $nameMaxChars;
		return $this;
	}

	public function getDataUpload( $val = NULL )
	{
		if( ! is_null($val) ) {
			if( isset($this->dataUpload[$val]) ) {
				return $this->dataUpload[$val];
			}

			return FALSE;
		}

		return $this->dataUpload;
	}

	public function setDataUpload($dataUpload)
	{
		$this->dataUpload = $dataUpload;
		return $this;
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function setErrors($erros)
	{
		$this->errors = $erros;
		return $this;
	}

}