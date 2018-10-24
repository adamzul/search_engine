<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;



class MapController extends Controller
{
	/**
	 * @Route("/mining-map", name="mining-map")
	 */
	public function miningMap(Request $request)
	{
		$form = $this->createFormBuilder()
			->add('jumlahCenteroid', TextType::class)
			->add('band', ChoiceType::class, array(
		    'choices'  => array(
		        'band 1 dan 2' => 2,
		        'band 1, 2, dan 3' => 3,
		        'band 1, 2, 3 dan 4' => 4,
		        'band 1, 2, 3, 4, dan 5' => 5,
		        'band 1, 2, 3, 4, 5 dan 7' => 6,
		    )))
			->add('mining', SubmitType::class, array('label' => 'mining'))
			->getForm();
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData() ;
			$this->doMiningMap($data['jumlahCenteroid'], $data['band']);

		}
		return $this->render('map/mining-map.html.twig', ['form' => $form->createView()]);

	}
	/**
	 * @Route("/index-map", name="index-map")
	 */
	public function indexMining()
	{
		return $this->render('map/index-map.html.twig');

	}
	public function doMiningMap($jumlahCenteroid, $band)
	{
		set_time_limit(0);
		ini_set('memory_limit','256M');
		$color = [[255,0,0], [0,255,0], [0,0,255], [255,255,0], [255,0,255], [0,255,255], [125,0,0],[0,125,0], [0,0,125], [125,125,0], [125,0,125],[0,125,125]];
		$anggotaCluster = [];
		$centeroid = [];
		$image = [];
		$jarakCenteroid = [];
		$width = [];
		$height = [];
		$folder = scandir('../web/map/');
		array_shift($folder);
		array_shift($folder);
		// $image1 = imagecreatefromgif('../web/map/gb1.gif');
		// imagefilter($image, IMG_FILTER_GRAYSCALE);
		$i = 0;
		foreach ($folder as $fileInfo) {
			$image[$i] = $fileInfo;
			$image[$i] = imagecreatefromgif('../web/map/'.$image[$i]);
			$width[$i] = imagesx($image[$i]);
			$height[$i] = imagesy($image[$i]);
			$arrayGrey[$i] = []; 
			for ($x = 0; $x < $width[$i]; $x++) {
				$arrayGrey[$i][$x] = [];
				// iterate through y axis
				for ($y = 0; $y < $height[$i]; $y++) {
					// look at current pixel
					$arrayGrey[$i][$x][$y] = imagecolorat($image[$i], $x, $y);
						
				}
			}
			
			$i++;
		}
		$arrayKoordinat = [];
		for ($i=0; $i < $height[0]; $i++) { 
			for ($j=0; $j < $width[0]; $j++) { 
				$temp = [];
				for ($k=0; $k < $band; $k++) { 
					array_push($temp, $arrayGrey[$k][$i][$j]);
				}
				array_push($arrayKoordinat, $temp);
				array_push($centeroid, $temp);
				$temp['x'] = $i;
				$temp['y'] = $j;

				array_push($anggotaCluster, [$temp]);
			}
		}
		while (count($centeroid) > $jumlahCenteroid ) {
			# code...
			$jarakCenteroid = [];
			// var_dump(count($centeroid));
			for ($i=0; $i < count($centeroid); $i++) { 
				for ($j=$i + 1; $j < count($centeroid); $j++) { 
					$temp1 = 0;
					for ($k=0; $k < $band; $k++) { 
						$temp1 += pow(($centeroid[$i][$k] - $centeroid[$j][$k]), 2);
					}
					$temp2 = sqrt($temp1);
					array_push($jarakCenteroid,[$i, $j, $temp2]);
				}
			}
			$jarakCenteroidTerdekat = $jarakCenteroid[0][2];
			$indexJarakCenteroidTerdekat = 0;
			for ($i=0; $i < count($jarakCenteroid); $i++) { 
				if($jarakCenteroidTerdekat > $jarakCenteroid[$i][2])
				{
					$jarakCenteroidTerdekat = $jarakCenteroid[0][2];
					$indexJarakCenteroidTerdekat = $i;
				}
			}
			$indexCenteroidYangDigabung = $jarakCenteroid[$indexJarakCenteroidTerdekat][0];
			$indexCenteroidYangDiLebur = $jarakCenteroid[$indexJarakCenteroidTerdekat][1];
			foreach ($anggotaCluster[$indexCenteroidYangDiLebur] as $value) {
				# code...
				array_push($anggotaCluster[$indexCenteroidYangDigabung], $value);
			}
			$anggotaCluster[$indexCenteroidYangDiLebur] = null;
			for ($i=0; $i < count($anggotaCluster)-1; $i++) { 
				if($anggotaCluster[$i] == null)
				{
					$anggotaCluster[$i] = $anggotaCluster[$i+1];
					$anggotaCluster[$i+1] = null;

				}

			}
			unset($anggotaCluster[$i]);
			array_filter($anggotaCluster);

			$centeroid[$indexCenteroidYangDiLebur] = null;
			for ($i=0; $i < count($centeroid)-1; $i++) { 
				if($centeroid[$i] == null)
				{
					$centeroid[$i] = $centeroid[$i+1];
					$centeroid[$i+1] = null;

				}

			}
			unset($centeroid[$i]);
			array_filter($centeroid);
			$temp = [];
			for ($i=0; $i < $band; $i++) { 
				$temp[$i] = 0;
			}
			foreach ($anggotaCluster[$indexCenteroidYangDigabung] as $value) {
				for ($i=0; $i < $band; $i++) { 
					$temp[$i] += $value[$i];
				}
			}
			$temp2 = [];
			for ($i=0; $i < $band; $i++) { 
					array_push($temp2, $temp[$i]/count($anggotaCluster[$indexCenteroidYangDigabung]));
				}
			$centeroid[$indexCenteroidYangDigabung] = $temp2;
			// imagefilter($tes, IMG_FILTER_GRAYSCALE);
		}
		$hasil = imagecreatetruecolor($width[0], $height[0]);
		for ($i=0; $i < 4; $i++) { 
			# code...
			for ($j=0; $j < count($anggotaCluster[$i]); $j++) { 
				# code...
				$colorTemp = imagecolorallocate($hasil, $color[$i][0], $color[$i][1], $color[$i][2]);
				imagesetpixel($hasil, $anggotaCluster[$i][$j]['x'], $anggotaCluster[$i][$j]['y'], $colorTemp);
			}

		}
		// for ($y = 0; $y < $height[0]; ++$y) {
		// 	for ($x = 0; $x < $width[0]; ++$x) {
		// 		$color = imagecolorallocate($tes, $arrayGrey[$i][$x][$y], $arrayGrey[$i][$x][$y], $arrayGrey[$i][$x][$y]);
		// 		imagesetpixel($tes, $x, $y, $color);
		// 	}
		// }
		// var_dump($anggotaCluster);
		imagepng($hasil, '../web/result/hasil'.$band.'.png');
	}

	
}
