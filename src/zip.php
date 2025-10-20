<?php

namespace ngatngay;

use ZipArchive;
use SplFileInfo;

class zip extends ZipArchive {
    /**
     * @param string $path
     * @param string|null $relative
     * @return bool
     */
    public function add($path, $relative = null)
    {
        if (!file_exists($path)) {
            return false;
        }
        
        $file = new SplFileInfo($path);
        $path = $file->getPathname();
        $pathRelative = $path;

        if ($relative) {
            $pathRelative = substr($path, strlen($relative));
        }
    
        if ($file->isFile()) {
            $this->addFile($path, $pathRelative);
        }
        
        if ($file->isDir()) {
            $this->addEmptyDir($pathRelative);
        }
        
        return true;
    }
}
