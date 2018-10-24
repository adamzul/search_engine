<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Coefficient_i
 *
 * @ORM\Table(name="coefficient_i")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Coefficient_iRepository")
 */
class Coefficient_i
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
     * @ORM\Column(name="X", type="integer")
     */
    private $x;

    /**
     * @var int
     *
     * @ORM\Column(name="Y", type="integer")
     */
    private $y;

    /**
     * @var string
     *
     * @ORM\Column(name="tanda", type="string", length=255)
     */
    private $tanda;

    /**
     * @var int
     *
     * @ORM\Column(name="gambar", type="integer")
     */
    private $gambar;


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
     * Set x
     *
     * @param integer $x
     *
     * @return Coefficient_i
     */
    public function setX($x)
    {
        $this->x = $x;

        return $this;
    }

    /**
     * Get x
     *
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Set y
     *
     * @param integer $y
     *
     * @return Coefficient_i
     */
    public function setY($y)
    {
        $this->y = $y;

        return $this;
    }

    /**
     * Get y
     *
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Set tanda
     *
     * @param string $tanda
     *
     * @return Coefficient_i
     */
    public function setTanda($tanda)
    {
        $this->tanda = $tanda;

        return $this;
    }

    /**
     * Get tanda
     *
     * @return string
     */
    public function getTanda()
    {
        return $this->tanda;
    }

    /**
     * Set gambar
     *
     * @param integer $gambar
     *
     * @return Coefficient_i
     */
    public function setGambar($gambar)
    {
        $this->gambar = $gambar;

        return $this;
    }

    /**
     * Get gambar
     *
     * @return int
     */
    public function getGambar()
    {
        return $this->gambar;
    }
}

