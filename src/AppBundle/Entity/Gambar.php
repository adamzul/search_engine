<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gambar
 *
 * @ORM\Table(name="gambar")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GambarRepository")
 */
class Gambar
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nama_gambar", type="string", length=255)
     */
    private $namaGambar;

    /**
     * @var float
     *
     * @ORM\Column(name="Y_average", type="float")
     */
    private $yAverage;

    /**
     * @var float
     *
     * @ORM\Column(name="I_average", type="float")
     */
    private $iAverage;

    /**
     * @var float
     *
     * @ORM\Column(name="Q_average", type="float")
     */
    private $qAverage;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set namaGambar
     *
     * @param string $namaGambar
     *
     * @return Gambar
     */
    public function setNamaGambar($namaGambar)
    {
        $this->namaGambar = $namaGambar;

        return $this;
    }

    /**
     * Get namaGambar
     *
     * @return string
     */
    public function getNamaGambar()
    {
        return $this->namaGambar;
    }

    /**
     * Set yAverage
     *
     * @param float $yAverage
     *
     * @return Gambar
     */
    public function setYAverage($yAverage)
    {
        $this->yAverage = $yAverage;

        return $this;
    }

    /**
     * Get yAverage
     *
     * @return float
     */
    public function getYAverage()
    {
        return $this->yAverage;
    }

    /**
     * Set iAverage
     *
     * @param float $iAverage
     *
     * @return Gambar
     */
    public function setIAverage($iAverage)
    {
        $this->iAverage = $iAverage;

        return $this;
    }

    /**
     * Get iAverage
     *
     * @return float
     */
    public function getIAverage()
    {
        return $this->iAverage;
    }

    /**
     * Set qAverage
     *
     * @param float $qAverage
     *
     * @return Gambar
     */
    public function setQAverage($qAverage)
    {
        $this->qAverage = $qAverage;

        return $this;
    }

    /**
     * Get qAverage
     *
     * @return float
     */
    public function getQAverage()
    {
        return $this->qAverage;
    }
}

