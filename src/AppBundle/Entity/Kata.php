<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Kata
 *
 * @ORM\Table(name="kata")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\KataRepository")
 */
class Kata
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
     * @var int
     *
     * @ORM\Column(name="id_artikel", type="integer")
     */
    private $idArtikel;

    /**
     * @var string
     *
     * @ORM\Column(name="kata", type="string", length=255)
     */
    private $kata;

    /**
     * @var int
     *
     * @ORM\Column(name="jumlah", type="integer")
     */
    private $jumlah;


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
     * Set idArtikel
     *
     * @param integer $idArtikel
     *
     * @return Kata
     */
    public function setIdArtikel($idArtikel)
    {
        $this->idArtikel = $idArtikel;

        return $this;
    }

    /**
     * Get idArtikel
     *
     * @return int
     */
    public function getIdArtikel()
    {
        return $this->idArtikel;
    }

    /**
     * Set kata
     *
     * @param string $kata
     *
     * @return Kata
     */
    public function setKata($kata)
    {
        $this->kata = $kata;

        return $this;
    }

    /**
     * Get kata
     *
     * @return string
     */
    public function getKata()
    {
        return $this->kata;
    }

    /**
     * Set jumlah
     *
     * @param integer $jumlah
     *
     * @return Kata
     */
    public function setJumlah($jumlah)
    {
        $this->jumlah = $jumlah;

        return $this;
    }

    /**
     * Get jumlah
     *
     * @return int
     */
    public function getJumlah()
    {
        return $this->jumlah;
    }

    public function tambahJumlah()
    {
        $this->jumlah++;
        return $this;
    }
}

