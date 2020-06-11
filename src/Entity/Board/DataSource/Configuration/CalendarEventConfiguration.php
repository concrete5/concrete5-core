<?php

namespace Concrete\Core\Entity\Board\DataSource\Configuration;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardConfiguredDataSourceConfigurationCalendarEvent")
 */
class CalendarEventConfiguration extends Configuration
{

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Calendar\Calendar")
     * @ORM\JoinColumn(name="caID", referencedColumnName="caID")
     */
    protected $calendar;

    /** @ORM\Embedded(class = "\Concrete\Core\Entity\Search\Query") */
    protected $query;

    /**
     * @return \Concrete\Core\Entity\Search\Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param mixed $query
     */
    public function setQuery($query): void
    {
        $this->query = $query;
    }

    /**
     * @return mixed
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * @param mixed $calendar
     */
    public function setCalendar($calendar): void
    {
        $this->calendar = $calendar;
    }








}
