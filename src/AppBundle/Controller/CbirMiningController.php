<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Artikel;
use AppBundle\Entity\Kata;
use AppBundle\Entity\Gambar;
use AppBundle\Entity\Coefficient_i;
use AppBundle\Entity\Coefficient_q;
use AppBundle\Entity\Coefficient_y;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

use voku\helper\StopWords;
use Sastrawi\Stemmer\StemmerFactory;
use AppBundle\Controller\CbirController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
class CbirMiningController extends Controller
{

	public $COEFFNUM = 40;
	public $w = []; 
	

	/**
	 * @Route("/miningcbir", name="miningcbir")
	 */
	public function indexMining(Request $request, $result = null )
	{
		$this->w['Y'] = [5.00, 0.83, 1.01, 0.52, 0.47, 0.30];
		$this->w['I'] = [19.21, 1.26, 0.44, 0.53, 0.28, 0.14];
		$this->w['Q'] = [34.37, 0.36, 0.45, 0.14, 0.18, 0.27];
		$gambarInput = null;
		$form = $this->createFormBuilder()
			->add('file', FileType::class)
			->add('save', SubmitType::class, array('label' => ' '))
			->getForm();
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData() ;
			// $result = $this->textSearch( $data["cari"]);
			// foreach ($result as $valueResult) {
			// 	# code...

			// }
			// $result = $this->UploadImage($data);
			$fs = new Filesystem();
			$fs->copy($data['file'], '../web/query/image.jpg', true);
			$gambarInput = 'image.jpg';


			// Parse image to get y,i,q values
		  list($y_values, $i_values, $q_values) = $this->ParseImage("query/".$gambarInput);

		  // Dempose and trunctate
		  $this->DecomposeImage($y_values);
		  $y_trunc = $this->TruncateCoeffs($y_values, $this->COEFFNUM);
		  $this->DecomposeImage($i_values);
		  $i_trunc = $this->TruncateCoeffs($i_values, $this->COEFFNUM);
		  $this->DecomposeImage($q_values);
		  $q_trunc = $this->TruncateCoeffs($q_values, $this->COEFFNUM);

		  // Calculate score for every image in database
		
		$repositoryGambar = $this->getDoctrine()->getRepository(Gambar::class);
		$gambars = $repositoryGambar->findAll();
		foreach ($gambars as $gambar) {
			# code...
			$scores[$gambar->getId()] = $this->w['Y'][0]*ABS($y_values[0][0] - $gambar->getYAverage())
						  + $this->w['I'][0]*ABS($i_values[0][0] - $gambar->getIAverage()) 
						  + $this->w['Q'][0]*ABS($q_values[0][0] - $gambar->getQAverage());
			$filenames[$gambar->getId()] = $gambar->getNamaGambar();

		}
		  
		  // compare query coefficients with database
		  for ($i = 0; $i < $this->COEFFNUM; $i++) {
		  	// y
		  	$repositoryCoeffsY = $this->getDoctrine()->getRepository(Coefficient_y::class);
			$coeffsY = $repositoryCoeffsY->findBy(array('x' => $y_trunc['x'][$i] , 'y' => $y_trunc['y'][$i], 'tanda' => $y_trunc['sign'][$i]));
			// var_dump($coeffsY);
			foreach ($coeffsY as $valueVoeffsY) {
				# code...
			  $scores[$valueVoeffsY->getGambar()] -= $this->w['Y'][$this->bin($valueVoeffsY->getX(),$valueVoeffsY->getY())];
			 //  var_dump($valueVoeffsY);
				// echo "<br>";
			}
		  	// i
			$repositoryCoeffsI = $this->getDoctrine()->getRepository(Coefficient_i::class);
			$coeffsI = $repositoryCoeffsI->findBy(array('x' => $i_trunc['x'][$i] , 'y' => $i_trunc['y'][$i], 'tanda' => $i_trunc['sign'][$i]));

			foreach ($coeffsI as $valueVoeffsI) {
				# code...
			  $scores[$valueVoeffsI->getGambar()] -= $this->w['I'][$this->bin($valueVoeffsI->getX(),$valueVoeffsI->getY())];

			}
			
		  	// q
			$repositoryCoeffsQ = $this->getDoctrine()->getRepository(Coefficient_q::class);
			$coeffsQ = $repositoryCoeffsQ->findBy(array('x' => $q_trunc['x'][$i] , 'y' => $q_trunc['y'][$i], 'tanda' => $q_trunc['sign'][$i]));

			foreach ($coeffsQ as $valueVoeffsQ) {
				# code...
			  $scores[$valueVoeffsQ->getGambar()] -= $this->w['Q'][$this->bin($valueVoeffsQ->getX(),$valueVoeffsQ->getY())];

			}
			// $i = 1000;
		  }

		  asort($scores,SORT_NUMERIC);
		  $result = [];
		  $i = 0;
		  foreach($scores as $key => $value){
		  	if($i == 5)
		  		break;
		  	$i++;
				array_push($result, $filenames[$key]);
		  }
		}
		

		return $this->render('search-cbir/search-cbir.html.twig', [
			'form' => $form->createView(), 'gambarInput' => $gambarInput, 'result' => $result
		]);
	}



	/**
	 * @Route("/miningcbir/runmining", name="run-cbir-mining")
	 */
	public function runMining()
	{
		set_time_limit(0);

		$miningDone = 'done';
		$folder = new \DirectoryIterator('../web/images2');
		$total = 0;
		foreach ($folder as $fileInfo) {
		  if($fileInfo->isDot()) continue;
		  $total++;
		}
		
		$i=0;

		// $cbir = new CbirController();
		foreach ($folder as $fileInfo) {
		  if($fileInfo->isDot()) continue;
		  $i++;

		  $this->ProcessImage($fileInfo->getFilename());
		  // outputProgress($i,$total);
		}
		return $this->render('mining/mining.html.twig', [ 'miningDone' => $miningDone]);

	}

	/**
	 * @Route("/delete-gambar")
	 */

	public function delete()
	{
		$em = $this->getDoctrine()->getManager();

		$repositoryCoeffsY = $this->getDoctrine()->getRepository(Coefficient_y::class);
		$deleteCoeffsY = $repositoryCoeffsY->findAll();
		foreach ($deleteCoeffsY as $valueDeleteCoeffsY) {
			$em->remove($valueDeleteCoeffsY);
		}
		$em->flush();

		$repositoryCoeffsI = $this->getDoctrine()->getRepository(Coefficient_i::class);
		$deleteCoeffsI = $repositoryCoeffsI->findAll();
		foreach ($deleteCoeffsI as $valueDeleteCoeffsI) {
			$em->remove($valueDeleteCoeffsI);
		}
		$em->flush();

		$repositoryCoeffsQ = $this->getDoctrine()->getRepository(Coefficient_q::class);
		$deleteCoeffsQ = $repositoryCoeffsQ->findAll();
		foreach ($deleteCoeffsQ as $valueDeleteCoeffsQ) {
			$em->remove($valueDeleteCoeffsQ);
		}
		$em->flush();

		$repositoryGambar = $this->getDoctrine()->getRepository(Gambar::class);
		$deleteGambar = $repositoryGambar->findAll();
		foreach ($deleteGambar as $valueDeleteGambar) {
			$em->remove($valueDeleteGambar);
		}
		$em->flush();

		$url = $this->get('router')->generate('miningcbir');
		return $this->redirect($url, 301);


	}

	function outputProgress($current, $total) {
		echo "<span style='position: absolute;z-index:$current;background:#FFF;'>Processed " . round($current / $total * 100) . "% </span>";
		myFlush();
		//sleep(1);
	}

	/**
	 * Flush output buffer
	 */
	function myFlush() {
		echo(str_repeat(' ', 256));
		if (@ob_get_contents()) {
			@ob_end_flush();
		}
		flush();
	}



	// Process Image
	public function ProcessImage($imgFile)
	{
		// global $db_server,$COEFFNUM;
		
		// Parse image
		list ($y_values, $i_values, $q_values) = $this->ParseImage("../web/images2/".$imgFile);

		// Dempose and trunctate

		$this->DecomposeImage($y_values);
		$y_trunc = $this->TruncateCoeffs($y_values, $this->COEFFNUM);
		$this->DecomposeImage($i_values);
		$i_trunc = $this->TruncateCoeffs($i_values, $this->COEFFNUM);
		$this->DecomposeImage($q_values);
		$q_trunc = $this->TruncateCoeffs($q_values, $this->COEFFNUM);

		// connect to database
		// $connection = mysqli_connect($db_server["host"], $db_server["username"], $db_server["password"]);
		// mysqli_select_db($connection, $db_server["database"]);

		// save image file
		$imageid = $this->InsertFileToDB($imgFile,$y_values[0][0],$i_values[0][0],$q_values[0][0]);
		
		// save y-coeffs
		$this->InsertCoeffsToDB("Coefficient_y",$y_trunc,$imageid);

		// save i-coeffs
		$this->InsertCoeffsToDB("Coefficient_i",$i_trunc,$imageid);

		// save q-coeffs
		$this->InsertCoeffsToDB("Coefficient_q",$q_trunc,$imageid);

		// mysqli_close($connection);
	}

	// Get uploaded image
	function UploadImage($data)
	{

		
		// check for errors
		if ($data["file"]["error"] > 0)
		{
		  exit("Return Code: " . $data["file"]["error"] . "<br>");
		}
		
		// get uploaded image
		$filepart = implode(explode(".",$data["file"]["name"],-1));
		$explosion = explode(".", $data["file"]["name"]);
		foreach ($explosion as $i){
			$extension = $i;
		}
		$extension = strtolower($extension);
		
		// size checks
		$image = imagecreatefromjpeg($data["file"]["tmp_name"]);
		$image_width = imagesx($image);
		$image_height = imagesy($image);
		if ($image_height != 200 || $image_width != 200)
		{
			exit("Size must be 200x200");
		}

		// check type and extension
		$allowedExts = array("jpg", "jpeg", "gif", "png");
		if ((($data["file"]["type"] != "image/gif")
			&& ($data["file"]["type"] != "image/jpeg")
			&& ($data["file"]["type"] != "image/png")
			&& ($data["file"]["type"] != "image/pjpeg"))
		|| ($data["file"]["size"] > 100000)
		|| !in_array($extension, $allowedExts))
		{
			exit("Invalid file");
		}

		// rename if necessary
		$filename = $filepart.".".$extension;
		while (file_exists($location."/" . $filename))
		{
			$filename = $filepart."_".mt_rand(11,99).".".$extension;
		}

		// all OK, copy image
		move_uploaded_file($data["file"]["tmp_name"], 'query' . "/" . $filename);
		return $filename;
		
	}

	// Parse image
	function ParseImage($imgFile)
	{
		// read image
		$image = imagecreatefromjpeg($imgFile);
			   
		$image_width = imagesx($image);
		$image_height = imagesy($image);

		// iterate through x axis
		for ($x = 0; $x < $image_width; $x++) {

			// iterate through y axis
			for ($y = 0; $y < $image_height; $y++) {

				// look at current pixel
				$rgb = imagecolorat($image, $x, $y);
				$r = (($rgb >> 16) & 0xFF) / 255;
				$g = (($rgb >> 8) & 0xFF) / 255;
				$b = ($rgb & 0xFF) / 255;
			
				// get YIQ values
				$y_values[$x][$y] = 0.299*$r + 0.587*$g + 0.114*$b; 
				$i_values[$x][$y] = 0.596*$r - 0.275*$g - 0.321*$b; 
				$q_values[$x][$y] = 0.212*$r - 0.523*$g + 0.311*$b; 	
			}
		}
		return array($y_values, $i_values, $q_values);
	}

	// insert file to database
	function InsertFileToDB($imgFile,$y_average,$i_average,$q_average)
	{
		
		$em = $this->getDoctrine()->getManager();

		$gambar = new Gambar();
		$gambar->setNamaGambar($imgFile);
		$gambar->setYAverage($y_average);
		$gambar->setIAverage($i_average);
		$gambar->setQAverage($q_average);

		$em->persist($gambar);
		$em->flush();
		return $gambar->getId();

	}

	// save coefficients
	function InsertCoeffsToDB($dbtable,$coefftable,$imageid)
	{
		
		$em = $this->getDoctrine()->getManager();
		for ($i = 0; $i < $this->COEFFNUM; $i++) {
			$temp = 'AppBundle\Entity\\'.$dbtable;
			$coefficient = new $temp;
			$coefficient->setX($coefftable['x'][$i]);
			$coefficient->setY($coefftable['y'][$i]);
			$coefficient->setTanda($coefftable['sign'][$i]);
			$coefficient->setGambar($imageid);

			$em->persist($coefficient);
		}

		
		$em->flush();
	}

	// Transpose table
	function transpose($array)
	{
		array_unshift($array, null);
		return call_user_func_array('array_map', $array);
	}

	// Decompose Array
	function DecomposeArray(&$array)
	{
		//get length
		$h = count($array);
		
		// initialize array
		for ($x = 0; $x < $h; $x++) {
			$array[$x] = $array[$x] / sqrt($h);
		}
		
		// do the transformation
		while ($h > 1) {
			$h = $h /2;
			for ($i = 0; $i < $h; $i++) {
				$arraynew[$i]    = ($array[2*$i] + $array[2*$i+1])/sqrt(2);
				$arraynew[$h+$i] = ($array[2*$i] - $array[2*$i+1])/sqrt(2);
			}
			// copy arraynew to array
			for ($i = 0; $i < 2*$h; $i++) {
				$array[$i] = $arraynew[$i];
			}
		}
	}

	// Decompose Image
	function DecomposeImage(&$array)
	{
		//get length
		$rows = count($array);
		
		// decompose rows
		for ($x = 0; $x < $rows; $x++) {
			$this->DecomposeArray($array[$x]);
		}
		
		// transpose matrix
		$array = $this->transpose($array);
		
		// decompose rows again
		for ($x = 0; $x < $rows; $x++) {
			$this->DecomposeArray($array[$x]);
		}
		
		// transpose matrix back
		$array = $this->transpose($array);
	}

	// Truncate Coefficients - return array: x,y,sign
	function TruncateCoeffs($multi_array,$m)
	{
		list ($abs_array, $sign_array) = $this->TableToArrays($multi_array);
		arsort($abs_array);
		
		$i = 0;
		foreach ($abs_array as $key => $value){
			if ($i==$m)
				break;
				
			$coord = explode(",", $key);
				
			$trunc['x'][] = $coord[0];
			$trunc['y'][] = $coord[1];
			$trunc['sign'][] = $sign_array[$key];
			
			$i++;
		}
		return $trunc;
	}

	// Convert multi-dimensional array to array
	function TableToArrays($multi)
	{
		foreach ($multi as $x => $array){
			foreach ($array as $y => $value){
				$key = $x.",".$y;
				$abs[$key] = abs($value);
				$sign[$key] = ($value > 0 ? "+" : "-");
			}
		}
		return array ($abs, $sign);
	}

	// Bin
	function bin($i,$j)
	{
		return min(max($i,$j),5);
	}
}
