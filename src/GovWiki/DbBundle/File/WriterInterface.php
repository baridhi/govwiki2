<?php

namespace GovWiki\DbBundle\File;

/**
 * Interface WriterInterface
 * @package GovWiki\DbBundle\File
 */
interface WriterInterface
{
    /**
     * @param array $row One line of data.
     *
     * @return void
     */
    public function write(array $row);
}
