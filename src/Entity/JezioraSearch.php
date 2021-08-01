<?php

namespace App\Entity;

class JezioraSearch
{
  /**
   * @var string|null
   */
  private $nazwaSearch;

  /**
   * @var int|null
   */
  private $powierzchniaSearch;

  /**
   * @var string|null
   */
  private $miejscowoscSearch;

  /**
   * @var string|null
   */
  private $pomostySearch;

  /**
   * @var string|null
   */
  private $lodzSearch;

  /**
   * @var string|null
   */
  private $kuszaSearch;

  /**
   * @var string|null
   */
  private $regionSearch;
  
  /**
   * @return string|null
   */
  public function getNazwaSearch(): ?string
  {
    return $this->nazwaSearch;
  }

  /**
   * @param string|null $nazwaSearch
   * @return JezioraSearch
   */
  public function setNazwaSearch(string $nazwaSearch): JezioraSearch
  {
    $this->nazwaSearch = $nazwaSearch;

    return $this;
  }

  /**
   * @return int|null
   */
  public function getPowierzchniaSearch(): ?int
  {
    return $this->powierzchniaSearch;
  }

  /**
   * @param string|null $powierzchniaSearch
   * @return JezioraSearch
   */
  public function setPowierzchniaSearch(int $powierzchniaSearch): JezioraSearch
  {
    $this->powierzchniaSearch = $powierzchniaSearch;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getMiejscowoscSearch(): ?string
  {
    return $this->miejscowoscSearch;
  }

  /**
   * @param string|null $miejscowoscSearch
   * @return JezioraSearch
   */
  public function setMiejscowoscSearch(string $miejscowoscSearch): JezioraSearch
  {
    $this->miejscowoscSearch = $miejscowoscSearch;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getPomostySearch(): ?string
  {
    return $this->pomostySearch;
  }

  /**
   * @param string|null $pomostySearch
   * @return JezioraSearch
   */
  public function setPomostySearch(string $pomostySearch): JezioraSearch
  {
    $this->pomostySearch = $pomostySearch;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getLodzSearch(): ?string
  {
    return $this->lodzSearch;
  }

  /**
   * @param string|null $lodzSearch
   * @return JezioraSearch
   */
  public function setLodzSearch(string $lodzSearch): JezioraSearch
  {
    $this->lodzSearch = $lodzSearch;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getKuszaSearch(): ?string
  {
    return $this->kuszaSearch;
  }

  /**
   * @param string|null $kuszaSearch
   * @return JezioraSearch
   */
  public function setKuszaSearch(string $kuszaSearch): JezioraSearch
  {
    $this->kuszaSearch = $kuszaSearch;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getRegionSearch(): ?string
  {
    return $this->regionSearch;
  }

  /**
   * @param string|null $regionSearch
   * @return JezioraSearch
   */
  public function setRegionSearch(string $regionSearch): JezioraSearch
  {
    $this->regionSearch = $regionSearch;

    return $this;
  }
}