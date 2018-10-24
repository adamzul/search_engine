<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Coefficient_y
 *
 * @ORM\Table(name="coefficient_y")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Coefficient_yRepository")
 */
class Coefficient_y
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
     * @var string
     *
     * @ORM\Column(name="gambar", type="string", length=255)
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
     * @return Coefficient_y
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
     * @return Coefficient_y
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
     * @return Coefficient_y
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
     * @param string $gambar
     *
     * @return Coefficient_y
     */
    public function setGambar($gambar)
    {
        $this->gambar = $gambar;

        return $this;
    }

    /**
     * Get gambar
     *
     * @return string
     */
    public function getGambar()
    {
        return $this->gambar;
    }
}

