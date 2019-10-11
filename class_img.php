<?php
class img_cat {
	
	function __construct()
	{
		$this->path=$_GET['path'];
		$this->path=rtrim($this->path, '/');
		
		if(isset($_GET['q']))
		{
			$this->q=$_GET['q'];
		}
		if(!isset($_GET['q']))
		{
			$this->q=75;
		}
		if($this->q<10)
		{
			$this->q=10;
		}
	}
	
	function get_info_before()
	{
		$dir = opendir($this->path);
		$size=0;
		while (false !== ($file = readdir($dir))) {
			$size += filesize($this->path . DIRECTORY_SEPARATOR . $file);
		}
		closedir($dir);
		
		if ($size >= 1073741824) {
			$size = number_format($size / 1073741824, 2) . ' GB';
		}
	 
		elseif ($size >= 1048576) {
			$size = number_format($size / 1048576, 2) . ' MB';
		}
	 
		elseif ($size >= 1024) {
			$size = number_format($size / 1024, 2) . ' KB';
		}
	
		echo '<div class="size">Размер каталога до сжатия: '.$size.'</div>';
		
	}
	
	function get_file_size($filename)
	{
		$dir = opendir($this->path);
		$size=0;
		while (false !== ($file = readdir($dir))) {
			if($this->path . DIRECTORY_SEPARATOR . $file == $filename)
			{
				$size = filesize($this->path . DIRECTORY_SEPARATOR . $file);
			}
		}
		closedir($dir);
		
		if ($size >= 1073741824) {
			$size = number_format($size / 1073741824, 2) . ' GB';
		}
	 
		elseif ($size >= 1048576) {
			$size = number_format($size2 / 1048576, 2) . ' MB';
		}
	 
		elseif ($size >= 1024) {
			$size = number_format($size / 1024, 2) . ' KB';
		}
	
		return $size;
	}
	
	function get_info_after()
	{
		$dir = opendir($this->path);
		$size=0;
		$i=0;
		while (false !== ($file = readdir($dir))) {
			if(($file !='.')&&($file !='..'))
			{
				$size += filesize($this->path . DIRECTORY_SEPARATOR . $file);
				//echo 'name='.$file.', size='.filesize($this->path . DIRECTORY_SEPARATOR . $file);
				$file_size=filesize($this->path . DIRECTORY_SEPARATOR . $file);
				if ($file_size >= 1073741824) {
				$file_size = number_format($file_size / 1073741824, 2) . ' GB';
				}
			 
				elseif ($file_size >= 1048576) {
					$file_size = number_format($file_size / 1048576, 2) . ' MB';
				}
			 
				elseif ($file_size >= 1024) {
					$file_size = number_format($file_size / 1024, 2) . ' KB';
				}
				$this->info[$i]['size_after']=$file_size;
				$i++;
			}
		}
		closedir($dir);
		
		if ($size >= 1073741824) {
			$size = number_format($size / 1073741824, 2) . ' GB';
		}
	 
		elseif ($size >= 1048576) {
			$size = number_format($size / 1048576, 2) . ' MB';
		}
	 
		elseif ($size >= 1024) {
			$size = number_format($size / 1024, 2) . ' KB';
		}
	
		$this->cat_info='<div class="size">Размер каталога после сжатия: '.$size.'</div>';
		
	}
	
	function show_result()
	{
		foreach($this->info as $info)
		{
			echo $info['name_type'].$info['q_lowered'].$info['png_to_jpeg'];
			if($info['show_size']==1)
			{
				echo $info['size_after'];
				
			}
			echo '<br><br>';
		}
		
		echo $this->cat_info;
	}

	function save_images()
	{
		//open catalog
		$dir = opendir($this->path);
		$i=0;
		while (false !== ($file = readdir($dir))) 
		{
			$filename = $this->path.'\\'.$file;
			if(($file !='.')&&($file !='..'))
			{
				$info=getimagesize($filename);
				$type=$info['mime'];
				$size=$this->get_file_size($filename);
								
				$this->info[$i]['name_type']='<div class="image">Файл: "'.$filename.'", тип: "'.$type.'", размер до преобразований: '.$size.'</div>';
				$this->info[$i]['q_lowered']='';
				$this->info[$i]['show_size']=0;
				
				$img=new Imagick();				
			
				if($type=='image/jpeg')
				{
					//check quality
					$this->info[$i]['png_to_jpeg']='';
					$this->info[$i]['q_lowered']='Размер файла не изменился.';
					$img->readImage(__DIR__.'/'.$filename);
					$q_original=$img->getImageCompressionQuality();
					//echo 'qq='.$q_original;
					
					//resave if need due to auality
					if($q_original>$this->q)
					{
						$image=imagecreatefromjpeg($filename);
						$bg=imagecreatetruecolor(imagesx($image), imagesy($image));
						imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
						imagealphablending($bg, TRUE);
						imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
						imagedestroy($image);
						imagejpeg($bg, $filename, $this->q);
						
						$this->info[$i]['q_lowered']='Качество файла снижено. Размер теперь составляет: ';
						$this->info[$i]['show_size']=1;						
					}
				}
				//convert to jpg if need
				if($type=='image/png')
				{
					$image=imagecreatefrompng($filename);
					$bg=imagecreatetruecolor(imagesx($image), imagesy($image));
					imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
					imagealphablending($bg, TRUE);
					imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
					imagedestroy($image);
					imagejpeg($bg, $filename, $this->q);
					$this->info[$i]['png_to_jpeg']='Файл преобразован из png в jpeg. Размер теперь составляет: '; 
					$this->info[$i]['show_size']=1;
				}
				//echo '<br><br>';
				$i++;
			}
		}
		closedir($dir);
	}
	
}