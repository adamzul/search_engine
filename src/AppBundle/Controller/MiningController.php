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

class MiningController extends Controller
{
	/**
	 * @Route("/mining", name="mining")
	 */
	public function indexMining($miningDone = null )
	{
		return $this->render('mining/mining.html.twig', [ 'miningDone' => $miningDone]);
	}

	/**
	 * @Route("/mining/textmining", name="text-mining")
	 */
	public function textMining()
	{
		set_time_limit(0);
		$stopWords = new StopWords();
		$listStopWords = $stopWords->getStopWordsFromLanguage('id');
		$stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
		$stemmer  = $stemmerFactory->createStemmer();
		$path = '../data/';
		$files = scandir($path);
		array_shift($files);
		array_shift($files);
		$em = $this->getDoctrine()->getManager();
		$repositoryArtikel = $this->getDoctrine()->getRepository(Artikel::class);
		$repositoryKata = $this->getDoctrine()->getRepository(Kata::class);
		$this->delete();
		foreach ($files as $valueFiles) {
			# code...
			$artikel = new Artikel();
			$artikel->setJudul($valueFiles);
			$em->persist($artikel);
			$em->flush();

			$file = strtolower(file_get_contents($path.''.$valueFiles));
			$file = str_replace(str_split('\\/:*?"<>|,.\''), '', $file);

			$file = preg_split('/[\s]+|-/', $file );

			foreach ($file as $key => $valueFile) {
				# code...
				if(in_array($valueFile, $listStopWords))
				{
					continue;
				}
				
				$valueFile = $stemmer->stem($valueFile);
				// var_dump($valueFile);
				$kata = $repositoryKata->findOneBy(array('idArtikel' => $artikel->getId(),'kata' => $valueFile));
				if($kata != null)
				{
					$kata->tambahJumlah();
					continue;
				}
				$kata = new Kata();
				$kata->setIdArtikel($artikel->getId());
				$kata->setKata($valueFile);
				$kata->setJumlah(1);
				$em->persist($kata);
				$em->flush();
				
			}
			$totalKata = $repositoryKata->findAll();
		}
		 return $this->indexMining('done');
		// return $this->render('default/index.html.twig', [
		// 	'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
		// ]);
	}

	public function delete(){
		$em = $this->getDoctrine()->getManager();
		$repositoryArtikel = $this->getDoctrine()->getRepository(Artikel::class);
		$repositoryKata = $this->getDoctrine()->getRepository(Kata::class);

		$deleteArtikel = $repositoryArtikel->findAll();
		foreach ($deleteArtikel as $valueDeleteArtikel) {
			$em->remove($valueDeleteArtikel);
		}
		$deleteKata = $repositoryKata->findAll();
		
		foreach ($deleteKata as $valueDeleteKata) {
			$em->remove($valueDeleteKata);
		}
		

		$em->flush();
	}
}
