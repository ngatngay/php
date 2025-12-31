<?php

namespace nightmare\epub;

use nightmare\fs;
use Exception;
use ZipArchive;

class reader {
    private $zip;
    private $file_path;

    // private $epub_opf_file;
    private $epub_opf_dir;

    public function __construct($file_path) {
        $this->file_path = $file_path;
        $this->zip = new ZipArchive();

        $this->check();
        $this->read_metadata();
    }

    private function check() {
        // check file
        if (!is_file($this->file_path)) {
            throw new Exception('epub not exists or not permission');
        }

        if (filesize($this->file_path) < 1) {
            throw new Exception('epub not exists or not permission');
        }

        // check epub valid
        if ($this->zip->open($this->file_path) !== TRUE) {
            throw new Exception('epub read error');
        }

        if ($this->zip->getFromName('mimetype') !== 'application/epub+zip') {
            throw new Exception('epub format error');
        }
    }

    private function read_metadata() {
        $meta = $this->zip->getFromName('META-INF/container.xml');
        $meta = simplexml_load_string($meta);
        $this->epub_opf_dir = (string) $meta->rootfiles->rootfile['full-path'];
 
        $meta = $this->zip->getFromName($this->epub_opf_dir);
        $meta = simplexml_load_string($meta);

        var_dump($meta->metadata->children('http://purl.org/dc/elements/1.1/'));
    }
}
