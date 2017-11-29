<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Artikel;
use AppBundle\Entity\Kata;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

use voku\helper\StopWords;
use Sastrawi\Stemmer\StemmerFactory;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchController extends Controller
{
	/**
	 * @Route("/search", name="search")
	 */
	public function indexSearch(Request $request, $result = null )
	{
		$form = $this->createFormBuilder()
			->add('cari', TextType::class)
			
			->add('save', SubmitType::class, array('label' => ' '))
			->getForm();
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData() ;
			$result = $this->textSearch( $data["cari"]);
			foreach ($result as $valueResult) {
				# code...

			}

		}
		return $this->render('search/search.html.twig', [
			'form' => $form->createView(), 'result' => $result
		]);
		// return $this->render('search/search.html.twig', [ 'result' => $result]);
	}

	/**
	 * @Route("/search/textsearch")
	 */
	public function textSearch( $cari)
	{
		$temp = [];
		$stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
		$stemmer  = $stemmerFactory->createStemmer();
		$cari = $stemmer->stem($cari);
		$stopWords = new StopWords();
		$listStopWords = $stopWords->getStopWordsFromLanguage('id');
		$cari = preg_split('/[\s]+|-/', $cari );
		foreach ($cari as  $valueCari) {
			# code...
			if(!in_array($valueCari, $listStopWords) && !in_array($valueCari, $temp))
			{
				array_push($temp, $valueCari);
			}
		}
		
		$repositoryArtikel = $this->getDoctrine()->getRepository(Artikel::class);
		$repositoryKata = $this->getDoctrine()->getRepository(Kata::class);
		$result = $repositoryKata->findBy(['kata'=>$cari], ['jumlah'=>'DESC']);
		
		// usort($result, "cmp");
		$artikel = [];
		// var_dump($result[0]);

		foreach ($result as $valueResult) {
			# code...
			$temp2 = $repositoryArtikel->findOneBy(['id' =>$valueResult->getIdArtikel()]);
			$file = strtolower(file_get_contents('../data/'.$temp2->getJudul()));
			array_push($artikel, ['id' => $temp2->getId(), 'file' => $file, 'jumlah' => $valueResult->getJumlah(), 'judul' => $temp2->getJudul()]);
		}
		for ($i = 0; $i < count($artikel)-1; $i++) {
			# code...
			for ($j = $i+1; $j < count($artikel); $j++) {
				if($artikel[$i]['id'] == $artikel[$j]['id']){
					$artikel[$i]['jumlah'] += $artikel[$j]['jumlah'];
					unset($artikel[$j]);
					$artikel = array_values($artikel);
					$i = 0;
					break;
				}
			}
		}
		for ($i = 0; $i < count($artikel)-1; $i++) {
			# code...
			for ($j = $i+1; $j < count($artikel); $j++) {
				if($artikel[$i]['jumlah'] < $artikel[$j]['jumlah']){
					$temp3 = $artikel[$i];
					$artikel[$i] = $artikel[$j];
					$artikel[$j] = $temp3;
				}
			}
		}
		$artikel = array_slice($artikel, 0, 10);
		return $artikel;
		return $this->render('default/index.html.twig', [
			'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
		]);
	}
	

	
}
